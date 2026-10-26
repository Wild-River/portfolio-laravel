<?php

namespace Tests\Feature\Admin;

use App\Enums\PublishStatus;
use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PostTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
    }

    /**
     * Get valid form input, overridden by the given values.
     *
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function validInput(array $overrides = []): array
    {
        return [
            'title' => 'はじめての記事',
            'body' => '本文です。',
            'slug' => 'first-post',
            'status' => 'published',
            ...$overrides,
        ];
    }

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get('/admin/posts')->assertRedirect('/login');
        $this->post('/admin/posts', $this->validInput())->assertRedirect('/login');

        $this->assertDatabaseCount('posts', 0);
    }

    public function test_index_lists_posts_including_drafts(): void
    {
        $draft = Post::factory()->create();
        $published = Post::factory()->published()->create();

        $this->actingAs(User::factory()->create())
            ->get('/admin/posts')
            ->assertOk()
            ->assertSee($draft->title)
            ->assertSee($published->title);
    }

    public function test_create_screen_can_be_rendered(): void
    {
        Category::factory()->create();
        Tag::factory()->create();

        $this->actingAs(User::factory()->create())
            ->get('/admin/posts/create')
            ->assertOk();
    }

    public function test_post_can_be_created_with_thumbnail_category_and_tags(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();
        $tags = Tag::factory()->count(2)->create();

        $this->actingAs($user)
            ->post('/admin/posts', $this->validInput([
                'thumbnail' => UploadedFile::fake()->image('thumb.png'),
                'category_id' => $category->id,
                'tags' => $tags->pluck('id')->all(),
            ]))
            ->assertSessionHasNoErrors()
            ->assertRedirect('/admin/posts');

        $post = Post::firstOrFail();
        $this->assertSame($user->id, $post->user_id);
        $this->assertSame($category->id, $post->category_id);
        $this->assertSame(PublishStatus::Published, $post->status);
        $this->assertEqualsCanonicalizing($tags->pluck('id')->all(), $post->tags->pluck('id')->all());
        $this->assertStringStartsWith('posts/', $post->thumbnail_path);
        Storage::disk('public')->assertExists($post->thumbnail_path);
    }

    public function test_post_can_be_created_without_thumbnail_or_category(): void
    {
        $this->actingAs(User::factory()->create())
            ->post('/admin/posts', $this->validInput(['category_id' => '']))
            ->assertSessionHasNoErrors();

        $post = Post::firstOrFail();
        $this->assertNull($post->thumbnail_path);
        $this->assertNull($post->category_id);
    }

    public function test_thumbnail_must_be_an_allowed_image_up_to_2mb(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post('/admin/posts', $this->validInput(['thumbnail' => UploadedFile::fake()->create('doc.pdf', 10, 'application/pdf')]))
            ->assertSessionHasErrors('thumbnail');

        $this->actingAs($user)
            ->post('/admin/posts', $this->validInput(['thumbnail' => UploadedFile::fake()->image('big.jpg')->size(2049)]))
            ->assertSessionHasErrors('thumbnail');

        $this->assertDatabaseCount('posts', 0);
        $this->assertEmpty(Storage::disk('public')->allFiles());
    }

    public function test_invalid_status_category_and_tags_are_rejected(): void
    {
        $this->actingAs(User::factory()->create())
            ->post('/admin/posts', $this->validInput([
                'status' => 'archived',
                'category_id' => 999,
                'tags' => [999],
            ]))
            ->assertSessionHasErrors(['status', 'category_id', 'tags.0']);
    }

    public function test_slug_must_be_unique(): void
    {
        Post::factory()->create(['slug' => 'first-post']);

        $this->actingAs(User::factory()->create())
            ->post('/admin/posts', $this->validInput())
            ->assertSessionHasErrors('slug');
    }

    public function test_edit_screen_is_found_by_slug(): void
    {
        $post = Post::factory()->create(['slug' => 'first-post']);

        $this->actingAs(User::factory()->create())
            ->get('/admin/posts/first-post/edit')
            ->assertOk()
            ->assertSee($post->title);
    }

    public function test_update_keeps_thumbnail_when_no_new_file_is_chosen(): void
    {
        $path = UploadedFile::fake()->image('old.png')->store('posts', 'public');
        $post = Post::factory()->create(['slug' => 'first-post', 'thumbnail_path' => $path]);
        $tag = Tag::factory()->create();
        $post->tags()->attach($tag);

        $this->actingAs(User::factory()->create())
            ->put('/admin/posts/first-post', $this->validInput(['title' => '更新後']))
            ->assertSessionHasNoErrors()
            ->assertRedirect('/admin/posts');

        $post->refresh();
        $this->assertSame('更新後', $post->title);
        $this->assertSame($path, $post->thumbnail_path);
        $this->assertCount(0, $post->tags);
        Storage::disk('public')->assertExists($path);
    }

    public function test_replacing_the_thumbnail_deletes_the_old_file(): void
    {
        $oldPath = UploadedFile::fake()->image('old.png')->store('posts', 'public');
        $post = Post::factory()->create(['slug' => 'first-post', 'thumbnail_path' => $oldPath]);

        $this->actingAs(User::factory()->create())
            ->put('/admin/posts/first-post', $this->validInput(['thumbnail' => UploadedFile::fake()->image('new.png')]))
            ->assertSessionHasNoErrors();

        $post->refresh();
        $this->assertNotSame($oldPath, $post->thumbnail_path);
        Storage::disk('public')->assertMissing($oldPath);
        Storage::disk('public')->assertExists($post->thumbnail_path);
    }

    public function test_removing_the_thumbnail_deletes_the_file(): void
    {
        $path = UploadedFile::fake()->image('old.png')->store('posts', 'public');
        $post = Post::factory()->create(['slug' => 'first-post', 'thumbnail_path' => $path]);

        $this->actingAs(User::factory()->create())
            ->put('/admin/posts/first-post', $this->validInput(['remove_thumbnail' => '1']))
            ->assertSessionHasNoErrors();

        $this->assertNull($post->fresh()->thumbnail_path);
        Storage::disk('public')->assertMissing($path);
    }

    public function test_failed_validation_keeps_the_old_thumbnail(): void
    {
        $path = UploadedFile::fake()->image('old.png')->store('posts', 'public');
        $post = Post::factory()->create(['slug' => 'first-post', 'thumbnail_path' => $path]);

        $this->actingAs(User::factory()->create())
            ->put('/admin/posts/first-post', $this->validInput(['title' => '', 'thumbnail' => UploadedFile::fake()->image('new.png')]))
            ->assertSessionHasErrors('title');

        $this->assertSame($path, $post->fresh()->thumbnail_path);
        $this->assertSame([$path], Storage::disk('public')->allFiles());
    }

    public function test_deleting_a_post_deletes_its_thumbnail_and_tag_links(): void
    {
        $path = UploadedFile::fake()->image('old.png')->store('posts', 'public');
        $post = Post::factory()->hasAttached(Tag::factory())->create(['thumbnail_path' => $path]);

        $this->actingAs(User::factory()->create())
            ->delete("/admin/posts/{$post->slug}")
            ->assertRedirect('/admin/posts');

        $this->assertModelMissing($post);
        $this->assertDatabaseCount('post_tag', 0);
        $this->assertDatabaseCount('tags', 1);
        Storage::disk('public')->assertMissing($path);
    }
}
