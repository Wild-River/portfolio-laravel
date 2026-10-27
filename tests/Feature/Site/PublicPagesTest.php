<?php

namespace Tests\Feature\Site;

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use App\Models\Work;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_shows_latest_three_published_posts(): void
    {
        $old = Post::factory()->published()->create(['title' => '古い記事', 'created_at' => now()->subDays(10)]);
        $latest = Post::factory()->published()->count(3)->sequence(
            fn ($sequence) => ['title' => "新しい記事{$sequence->index}", 'created_at' => now()->subDays($sequence->index)],
        )->create();
        $draft = Post::factory()->create(['title' => '下書きの記事']);

        $response = $this->get('/')->assertOk();

        foreach ($latest as $post) {
            $response->assertSee($post->title);
        }
        $response->assertDontSee($old->title)->assertDontSee($draft->title);
        $response->assertSeeInOrder(['新しい記事0', '新しい記事1', '新しい記事2']);
    }

    public function test_home_shows_latest_six_published_works(): void
    {
        $old = Work::factory()->published()->create(['title' => '古いアプリ', 'created_at' => now()->subDays(10)]);
        Work::factory()->published()->count(6)->create();
        $draft = Work::factory()->create(['title' => '下書きのアプリ']);

        $this->get('/')
            ->assertOk()
            ->assertDontSee($old->title)
            ->assertDontSee($draft->title);
    }

    public function test_posts_index_lists_only_published_posts_with_tags(): void
    {
        $tag = Tag::factory()->create(['name' => 'Laravel']);
        $published = Post::factory()->published()->hasAttached($tag)->create();
        $draft = Post::factory()->create();

        $this->get('/posts')
            ->assertOk()
            ->assertSee($published->title)
            ->assertSee('#Laravel')
            ->assertDontSee($draft->title);
    }

    public function test_post_is_shown_by_slug(): void
    {
        $category = Category::factory()->create(['name' => '学習ログ']);
        $post = Post::factory()->published()->for($category)->create(['slug' => 'first-post', 'body' => '<script>alert(1)</script>']);

        $this->get('/posts/first-post')
            ->assertOk()
            ->assertSee($post->title)
            ->assertSee('学習ログ')
            ->assertSee('&lt;script&gt;', false)
            ->assertDontSee('<script>alert(1)</script>', false);
    }

    public function test_draft_or_missing_post_is_not_found(): void
    {
        Post::factory()->create(['slug' => 'draft-post']);

        $this->get('/posts/draft-post')->assertNotFound();
        $this->get('/posts/missing')->assertNotFound();
    }

    public function test_works_index_lists_only_published_works(): void
    {
        $published = Work::factory()->published()->create();
        $draft = Work::factory()->create();

        $this->get('/works')
            ->assertOk()
            ->assertSee($published->title)
            ->assertDontSee($draft->title);
    }

    public function test_work_is_shown_with_gallery_in_sort_order(): void
    {
        $work = Work::factory()->published()->create(['slug' => 'portfolio']);
        $work->images()->createMany([
            ['file_path' => 'works/second.png', 'sort_order' => 2],
            ['file_path' => 'works/first.png', 'sort_order' => 1],
        ]);

        $this->get('/works/portfolio')
            ->assertOk()
            ->assertSee($work->title)
            ->assertSee('id="lightbox"', false)
            ->assertSeeInOrder(['works/first.png', 'works/second.png']);
    }

    public function test_work_without_gallery_has_no_lightbox(): void
    {
        Work::factory()->published()->create(['slug' => 'portfolio']);

        $this->get('/works/portfolio')
            ->assertOk()
            ->assertDontSee('id="lightbox"', false);
    }

    public function test_draft_work_is_not_found(): void
    {
        Work::factory()->create(['slug' => 'draft-work']);

        $this->get('/works/draft-work')->assertNotFound();
    }
}
