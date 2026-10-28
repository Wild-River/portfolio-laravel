<?php

namespace Tests\Feature\Site;

use App\Models\Category;
use App\Models\GitHubLanguageStat;
use App\Models\Post;
use App\Models\Tag;
use App\Models\Work;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class PublicPagesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Http::fake(['api.github.com/users/*' => Http::response([])]);
    }

    public function test_home_shows_latest_three_published_posts(): void
    {
        $old = Post::factory()->published()->create(['title' => '古い記事', 'published_at' => now()->subDays(10)]);
        $latest = Post::factory()->published()->count(3)->sequence(
            fn ($sequence) => ['title' => "新しい記事{$sequence->index}", 'published_at' => now()->subDays($sequence->index)],
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
        $old = Work::factory()->published()->create(['title' => '古いアプリ', 'published_at' => now()->subYears(2)]);
        Work::factory()->published()->count(6)->create();
        $draft = Work::factory()->create(['title' => '下書きのアプリ']);

        $this->get('/')
            ->assertOk()
            ->assertDontSee($old->title)
            ->assertDontSee($draft->title);
    }

    public function test_home_shows_github_language_share(): void
    {
        GitHubLanguageStat::create(['language' => 'PHP', 'bytes' => 750, 'fetched_at' => now()]);
        GitHubLanguageStat::create(['language' => 'Blade', 'bytes' => 250, 'fetched_at' => now()]);

        $this->get('/')
            ->assertOk()
            ->assertSeeInOrder(['GitHubリポジトリでの使用言語', 'PHP', '75%', 'Blade', '25%']);
    }

    public function test_home_work_cards_show_tags_like_the_works_list(): void
    {
        $tag = Tag::factory()->create(['name' => 'Laravel']);
        Work::factory()->published()->hasAttached($tag)->create();

        $this->get('/')->assertOk()->assertSee('<span class="tag-chip">#Laravel</span>', false);
        $this->get('/works')->assertOk()->assertSee('<span class="tag-chip">#Laravel</span>', false);
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

    public function test_post_shows_its_published_date_not_creation_date(): void
    {
        Post::factory()->published()->create(['slug' => 'first-post', 'published_at' => '2025-12-24', 'created_at' => '2026-07-20 12:00:00']);

        $this->get('/posts/first-post')
            ->assertOk()
            ->assertSee('2025.12.24')
            ->assertDontSee('2026.07.20');
    }

    public function test_urls_in_post_body_and_work_description_are_linked(): void
    {
        Post::factory()->published()->create(['slug' => 'first-post', 'body' => '詳しくは https://example.com/post を見てください。']);
        Work::factory()->published()->create(['slug' => 'portfolio', 'description' => '使ったもの：https://tailadmin.com/']);

        $this->get('/posts/first-post')
            ->assertOk()
            ->assertSee('<a href="https://example.com/post" target="_blank" rel="noopener noreferrer">', false);

        $this->get('/works/portfolio')
            ->assertOk()
            ->assertSee('<a href="https://tailadmin.com/" target="_blank" rel="noopener noreferrer">', false);
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

    public function test_work_links_are_shown_as_buttons_only_on_the_detail_page(): void
    {
        Work::factory()->published()->create([
            'slug' => 'portfolio',
            'demo_url' => 'https://example.com/app',
            'github_url' => 'https://github.com/Wild-River/app',
        ]);

        $this->get('/works/portfolio')
            ->assertOk()
            ->assertSee('href="https://example.com/app"', false)
            ->assertSee('アプリを見る')
            ->assertSee('href="https://github.com/Wild-River/app"', false)
            ->assertSee('コードを見る');

        foreach (['/', '/works'] as $page) {
            $this->get($page)
                ->assertOk()
                ->assertDontSee('href="https://example.com/app"', false)
                ->assertDontSee('href="https://github.com/Wild-River/app"', false);
        }
    }

    public function test_work_without_links_shows_no_link_area(): void
    {
        Work::factory()->published()->create(['slug' => 'portfolio', 'demo_url' => null, 'github_url' => null]);

        $this->get('/works/portfolio')->assertOk()->assertDontSee('class="work-links', false);
    }

    public function test_draft_work_is_not_found(): void
    {
        Work::factory()->create(['slug' => 'draft-work']);

        $this->get('/works/draft-work')->assertNotFound();
    }
}
