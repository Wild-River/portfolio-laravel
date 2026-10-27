<?php

namespace Tests\Feature\Console;

use App\Enums\PublishStatus;
use App\Models\Post;
use App\Models\User;
use App\Models\Work;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Testing\PendingCommand;
use Tests\TestCase;

class ImportLegacyTest extends TestCase
{
    use RefreshDatabase;

    private string $uploads;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        // An in-memory SQLite database stands in for the plain PHP version's MySQL.
        config(['database.connections.legacy' => ['driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '']]);
        DB::purge('legacy');
        $this->createLegacySchema();

        $this->uploads = 'storage/framework/testing/legacy-uploads';
        File::ensureDirectoryExists(base_path("{$this->uploads}/posts"));
        File::ensureDirectoryExists(base_path("{$this->uploads}/works"));
        File::put(base_path("{$this->uploads}/posts/thumb.jpg"), 'post thumbnail');
        File::put(base_path("{$this->uploads}/works/thumb.png"), 'work thumbnail');
        File::put(base_path("{$this->uploads}/works/gallery.png"), 'gallery image');
    }

    protected function tearDown(): void
    {
        File::deleteDirectory(base_path($this->uploads));

        parent::tearDown();
    }

    private function createLegacySchema(): void
    {
        $schema = Schema::connection('legacy');

        foreach (['categories', 'tags'] as $table) {
            $schema->create($table, function (Blueprint $t) {
                $t->id();
                $t->string('name');
                $t->string('slug');
            });
        }
        foreach (['posts' => 'body', 'works' => 'description'] as $table => $text) {
            $schema->create($table, function (Blueprint $t) use ($text) {
                $t->id();
                $t->unsignedBigInteger('admin_id')->nullable();
                $t->unsignedBigInteger('category_id')->nullable();
                $t->string('title');
                $t->text($text);
                $t->string('thumbnail_path')->nullable();
                $t->string('slug');
                $t->string('status');
                $t->timestamp('created_at');
            });
        }
        $schema->create('work_images', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('work_id');
            $t->string('file_path')->nullable();
            $t->integer('sort_order')->default(0);
        });
        $schema->create('post_tag', function (Blueprint $t) {
            $t->unsignedBigInteger('post_id');
            $t->unsignedBigInteger('tag_id');
        });
        $schema->create('tag_work', function (Blueprint $t) {
            $t->unsignedBigInteger('work_id');
            $t->unsignedBigInteger('tag_id');
        });
    }

    private function seedLegacy(): void
    {
        $db = DB::connection('legacy');
        $db->table('categories')->insert(['id' => 3, 'name' => '学習ログ', 'slug' => 'log']);
        $db->table('tags')->insert([['id' => 5, 'name' => 'PHP', 'slug' => 'php'], ['id' => 6, 'name' => 'Laravel', 'slug' => 'laravel']]);
        $db->table('posts')->insert([
            ['id' => 10, 'admin_id' => 1, 'category_id' => 3, 'title' => '公開記事', 'body' => '本文', 'thumbnail_path' => 'uploads/posts/thumb.jpg', 'slug' => 'first', 'status' => 'published', 'created_at' => '2026-06-01 10:00:00'],
            ['id' => 11, 'admin_id' => 1, 'category_id' => null, 'title' => '下書き', 'body' => '本文', 'thumbnail_path' => null, 'slug' => 'draft', 'status' => 'draft', 'created_at' => '2026-06-02 10:00:00'],
        ]);
        $db->table('works')->insert(['id' => 20, 'admin_id' => 1, 'category_id' => 3, 'title' => 'アプリ', 'description' => '説明', 'thumbnail_path' => 'uploads/works/thumb.png', 'slug' => 'app', 'status' => 'published', 'created_at' => '2026-06-03 10:00:00']);
        $db->table('work_images')->insert([
            ['id' => 30, 'work_id' => 20, 'file_path' => 'uploads/works/gallery.png', 'sort_order' => 0],
            ['id' => 31, 'work_id' => 20, 'file_path' => null, 'sort_order' => 0],
        ]);
        $db->table('post_tag')->insert([['post_id' => 10, 'tag_id' => 5], ['post_id' => 10, 'tag_id' => 6]]);
        $db->table('tag_work')->insert(['work_id' => 20, 'tag_id' => 6]);
    }

    private function import(array $options = []): PendingCommand
    {
        return $this->artisan('app:import-legacy', ['--uploads' => $this->uploads, ...$options]);
    }

    public function test_imports_all_tables_keeping_ids(): void
    {
        $admin = User::factory()->create();
        $this->seedLegacy();

        $this->import()->assertSuccessful();

        $post = Post::with(['tags', 'category'])->findOrFail(10);
        $this->assertSame($admin->id, $post->user_id);
        $this->assertSame('学習ログ', $post->category->name);
        $this->assertSame(PublishStatus::Published, $post->status);
        $this->assertSame(['laravel', 'php'], $post->tags->pluck('slug')->sort()->values()->all());
        $this->assertSame('2026-06-01 10:00:00', $post->created_at->format('Y-m-d H:i:s'));
        $this->assertEquals($post->created_at, $post->updated_at);
        $this->assertNull(Post::findOrFail(11)->category_id);

        $work = Work::with(['images', 'tags'])->findOrFail(20);
        $this->assertSame(['works/gallery.png'], $work->images->pluck('file_path')->all());
        $this->assertSame(['laravel'], $work->tags->pluck('slug')->all());
    }

    public function test_image_paths_are_converted_and_files_copied(): void
    {
        User::factory()->create();
        $this->seedLegacy();

        $this->import()->assertSuccessful();

        $this->assertSame('posts/thumb.jpg', Post::findOrFail(10)->thumbnail_path);
        $this->assertSame('works/thumb.png', Work::findOrFail(20)->thumbnail_path);
        Storage::disk('public')->assertExists(['posts/thumb.jpg', 'works/thumb.png', 'works/gallery.png']);
        $this->assertSame('gallery image', Storage::disk('public')->get('works/gallery.png'));
    }

    public function test_gallery_rows_without_images_are_skipped_and_reported(): void
    {
        User::factory()->create();
        $this->seedLegacy();

        $this->import()
            ->expectsOutputToContain('work_images #31（works #20）は取り込みません（file_path が空）')
            ->assertSuccessful();

        $this->assertDatabaseMissing('work_images', ['id' => 31]);
    }

    public function test_gallery_rows_with_empty_file_name_are_skipped(): void
    {
        User::factory()->create();
        $this->seedLegacy();
        DB::connection('legacy')->table('work_images')->where('id', 31)->update(['file_path' => 'uploads/works/']);

        $this->import()
            ->expectsOutputToContain('work_images #31（works #20）は取り込みません（ファイル名が空：uploads/works/）')
            ->assertSuccessful();

        $this->assertDatabaseMissing('work_images', ['id' => 31]);
    }

    public function test_missing_thumbnail_file_is_imported_without_image(): void
    {
        User::factory()->create();
        $this->seedLegacy();
        File::delete(base_path("{$this->uploads}/posts/thumb.jpg"));

        $this->import()
            ->expectsOutputToContain('posts #10 は画像なしで取り込みます（サムネイルのファイルが見つからない：uploads/posts/thumb.jpg）')
            ->assertSuccessful();

        $this->assertNull(Post::findOrFail(10)->thumbnail_path);
    }

    public function test_odd_slugs_and_statuses_are_reported(): void
    {
        User::factory()->create();
        $this->seedLegacy();
        DB::connection('legacy')->table('posts')->where('id', 11)->update(['slug' => '下書き', 'status' => 'archived']);

        $this->import()
            ->expectsOutputToContain('posts #11 の slug「下書き」')
            ->expectsOutputToContain('posts #11 の status「archived」は不明なため下書きにします')
            ->assertSuccessful();

        $this->assertSame(PublishStatus::Draft, Post::findOrFail(11)->status);
    }

    public function test_dry_run_writes_nothing(): void
    {
        User::factory()->create();
        $this->seedLegacy();

        $this->import(['--dry-run' => true])
            ->expectsOutputToContain('--dry-run のため、書き込みはしていません。')
            ->assertSuccessful();

        $this->assertDatabaseCount('posts', 0);
        $this->assertDatabaseCount('categories', 0);
        $this->assertEmpty(Storage::disk('public')->allFiles());
    }

    public function test_stops_when_target_already_has_data(): void
    {
        User::factory()->create();
        $this->seedLegacy();
        Post::factory()->create();

        $this->import()->expectsOutputToContain('移行先にデータがあるため中止しました')->assertFailed();

        $this->assertDatabaseMissing('posts', ['id' => 10]);
    }

    public function test_stops_without_admin(): void
    {
        $this->seedLegacy();

        $this->import()->expectsOutputToContain('管理者がいません')->assertFailed();

        $this->assertDatabaseCount('posts', 0);
    }
}
