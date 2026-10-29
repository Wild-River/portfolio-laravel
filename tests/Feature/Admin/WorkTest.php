<?php

namespace Tests\Feature\Admin;

use App\Models\Category;
use App\Models\Tag;
use App\Models\Technology;
use App\Models\User;
use App\Models\Work;
use App\Models\WorkImage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class WorkTest extends TestCase
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
            'title' => 'ポートフォリオサイト',
            'description' => '説明文です。',
            'demo_url' => 'https://example.com/app',
            'github_url' => 'https://github.com/Wild-River/app',
            'slug' => 'portfolio',
            'status' => 'published',
            'published_at' => '2026-10-01',
            ...$overrides,
        ];
    }

    /**
     * Create a work whose thumbnail and gallery images exist on the fake disk.
     */
    private function workWithImages(int $imageCount = 2): Work
    {
        $work = Work::factory()->create([
            'slug' => 'portfolio',
            'thumbnail_path' => UploadedFile::fake()->image('thumb.png')->store('works', 'public'),
        ]);

        for ($sortOrder = 0; $sortOrder < $imageCount; $sortOrder++) {
            $work->images()->create([
                'file_path' => UploadedFile::fake()->image("image{$sortOrder}.png")->store('works', 'public'),
                'sort_order' => $sortOrder,
            ]);
        }

        return $work;
    }

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get('/admin/works')->assertRedirect('/login');
        $this->post('/admin/works', $this->validInput())->assertRedirect('/login');

        $this->assertDatabaseCount('works', 0);
    }

    public function test_index_lists_works(): void
    {
        $work = $this->workWithImages();

        $this->actingAs(User::factory()->create())
            ->get('/admin/works')
            ->assertOk()
            ->assertSee($work->title)
            ->assertSee('2枚');
    }

    public function test_create_screen_can_be_rendered(): void
    {
        $this->actingAs(User::factory()->create())
            ->get('/admin/works/create')
            ->assertOk();
    }

    public function test_work_can_be_created_with_gallery_images(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();
        $tag = Tag::factory()->create();
        $technology = Technology::factory()->create();

        $this->actingAs($user)
            ->post('/admin/works', $this->validInput([
                'thumbnail' => UploadedFile::fake()->image('thumb.png'),
                'images' => [
                    UploadedFile::fake()->image('first.png'),
                    UploadedFile::fake()->image('second.webp'),
                ],
                'category_id' => $category->id,
                'tags' => [$tag->id],
                'technologies' => [$technology->id],
            ]))
            ->assertSessionHasNoErrors()
            ->assertRedirect('/admin/works');

        $work = Work::with('images')->firstOrFail();
        $this->assertSame($user->id, $work->user_id);
        $this->assertSame([$tag->id], $work->tags->pluck('id')->all());
        $this->assertSame([$technology->id], $work->technologies->pluck('id')->all());
        $this->assertSame([0, 1], $work->images->pluck('sort_order')->all());
        Storage::disk('public')->assertExists($work->thumbnail_path);
        foreach ($work->images as $image) {
            $this->assertStringStartsWith('works/', $image->file_path);
            Storage::disk('public')->assertExists($image->file_path);
        }
    }

    public function test_work_can_be_created_without_images(): void
    {
        $this->actingAs(User::factory()->create())
            ->post('/admin/works', $this->validInput())
            ->assertSessionHasNoErrors();

        $this->assertDatabaseCount('works', 1);
        $this->assertDatabaseCount('work_images', 0);
    }

    public function test_demo_url_is_required_and_github_url_is_optional(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post('/admin/works', $this->validInput(['demo_url' => '', 'github_url' => '']))
            ->assertSessionHasErrors(['demo_url' => '確認用URLを入力してください。'])
            ->assertSessionDoesntHaveErrors('github_url');

        $this->actingAs($user)
            ->post('/admin/works', $this->validInput(['demo_url' => 'javascript:alert(1)', 'github_url' => 'not a url']))
            ->assertSessionHasErrors(['demo_url', 'github_url']);

        $this->actingAs($user)
            ->post('/admin/works', $this->validInput(['github_url' => '']))
            ->assertSessionHasNoErrors();

        $work = Work::firstOrFail();
        $this->assertSame('https://example.com/app', $work->demo_url);
        $this->assertNull($work->github_url);
    }

    public function test_gallery_images_are_validated(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post('/admin/works', $this->validInput(['images' => [
                UploadedFile::fake()->image('ok.png'),
                UploadedFile::fake()->create('doc.pdf', 10, 'application/pdf'),
            ]]))
            ->assertSessionHasErrors('images.1');

        $this->actingAs($user)
            ->post('/admin/works', $this->validInput([
                'images' => array_map(fn ($i) => UploadedFile::fake()->image("{$i}.png"), range(1, 11)),
            ]))
            ->assertSessionHasErrors('images');

        $this->assertDatabaseCount('works', 0);
        $this->assertEmpty(Storage::disk('public')->allFiles());
    }

    public function test_gallery_errors_are_shown_on_the_form(): void
    {
        $this->actingAs(User::factory()->create())
            ->from('/admin/works/create')
            ->followingRedirects()
            ->post('/admin/works', $this->validInput(['images' => [UploadedFile::fake()->create('doc.pdf', 10, 'application/pdf')]]))
            ->assertOk()
            ->assertSee('ギャラリー画像には画像ファイルを指定してください。');
    }

    public function test_edit_screen_shows_gallery_images(): void
    {
        $work = $this->workWithImages();

        $this->actingAs(User::factory()->create())
            ->get('/admin/works/portfolio/edit')
            ->assertOk()
            ->assertSee($work->images->first()->url)
            ->assertSee('この画像を削除');
    }

    public function test_update_adds_images_after_existing_ones(): void
    {
        $work = $this->workWithImages();

        $this->actingAs(User::factory()->create())
            ->put('/admin/works/portfolio', $this->validInput(['images' => [UploadedFile::fake()->image('new.png')]]))
            ->assertSessionHasNoErrors()
            ->assertRedirect('/admin/works');

        $this->assertSame([0, 1, 2], $work->images()->pluck('sort_order')->all());
        $this->assertCount(4, Storage::disk('public')->allFiles());
    }

    public function test_checked_images_are_deleted_with_their_files(): void
    {
        $work = $this->workWithImages();
        [$deleted, $kept] = $work->images()->get()->all();

        $this->actingAs(User::factory()->create())
            ->put('/admin/works/portfolio', $this->validInput(['delete_images' => [$deleted->id]]))
            ->assertSessionHasNoErrors();

        $this->assertModelMissing($deleted);
        $this->assertModelExists($kept);
        Storage::disk('public')->assertMissing($deleted->file_path);
        Storage::disk('public')->assertExists($kept->file_path);
        Storage::disk('public')->assertExists($work->thumbnail_path);
    }

    public function test_images_of_other_works_cannot_be_deleted(): void
    {
        $this->workWithImages();
        $other = Work::factory()->create();
        $otherImage = $other->images()->create([
            'file_path' => UploadedFile::fake()->image('other.png')->store('works', 'public'),
        ]);

        $this->actingAs(User::factory()->create())
            ->put('/admin/works/portfolio', $this->validInput(['delete_images' => [$otherImage->id]]))
            ->assertSessionHasNoErrors();

        $this->assertModelExists($otherImage);
        Storage::disk('public')->assertExists($otherImage->file_path);
    }

    public function test_replacing_the_thumbnail_deletes_the_old_file(): void
    {
        $work = $this->workWithImages(0);
        $oldPath = $work->thumbnail_path;

        $this->actingAs(User::factory()->create())
            ->put('/admin/works/portfolio', $this->validInput(['thumbnail' => UploadedFile::fake()->image('new.png')]))
            ->assertSessionHasNoErrors();

        Storage::disk('public')->assertMissing($oldPath);
        Storage::disk('public')->assertExists($work->fresh()->thumbnail_path);
    }

    public function test_removing_the_thumbnail_deletes_the_file(): void
    {
        $work = $this->workWithImages(0);
        $path = $work->thumbnail_path;

        $this->actingAs(User::factory()->create())
            ->put('/admin/works/portfolio', $this->validInput(['remove_thumbnail' => '1']))
            ->assertSessionHasNoErrors();

        $this->assertNull($work->fresh()->thumbnail_path);
        Storage::disk('public')->assertMissing($path);
    }

    public function test_deleting_a_work_deletes_all_its_files(): void
    {
        $work = $this->workWithImages();
        $work->tags()->attach(Tag::factory()->create());

        $this->actingAs(User::factory()->create())
            ->delete('/admin/works/portfolio')
            ->assertRedirect('/admin/works');

        $this->assertModelMissing($work);
        $this->assertSame(0, WorkImage::count());
        $this->assertDatabaseCount('tag_work', 0);
        $this->assertEmpty(Storage::disk('public')->allFiles());
    }
}
