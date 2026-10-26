<?php

namespace Tests\Feature\Admin;

use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TagTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get('/admin/tags')->assertRedirect('/login');
        $this->post('/admin/tags', ['name' => 'PHP', 'slug' => 'php'])->assertRedirect('/login');

        $this->assertDatabaseCount('tags', 0);
    }

    public function test_index_lists_tags(): void
    {
        $tag = Tag::factory()->create();

        $this->actingAs(User::factory()->create())
            ->get('/admin/tags')
            ->assertOk()
            ->assertSee($tag->name);
    }

    public function test_create_screen_can_be_rendered(): void
    {
        $this->actingAs(User::factory()->create())
            ->get('/admin/tags/create')
            ->assertOk();
    }

    public function test_tag_can_be_created(): void
    {
        $this->actingAs(User::factory()->create())
            ->post('/admin/tags', ['name' => 'PHP', 'slug' => 'php'])
            ->assertSessionHasNoErrors()
            ->assertRedirect('/admin/tags');

        $this->assertDatabaseHas('tags', ['name' => 'PHP', 'slug' => 'php']);
    }

    public function test_slug_must_be_unique_and_url_safe(): void
    {
        Tag::factory()->create(['slug' => 'php']);
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post('/admin/tags', ['name' => 'PHP', 'slug' => 'php'])
            ->assertSessionHasErrors('slug');

        $this->actingAs($user)
            ->post('/admin/tags', ['name' => 'PHP', 'slug' => 'php/8'])
            ->assertSessionHasErrors('slug');

        $this->assertDatabaseCount('tags', 1);
    }

    public function test_tag_can_be_updated_with_its_own_slug(): void
    {
        $tag = Tag::factory()->create(['slug' => 'php']);

        $this->actingAs(User::factory()->create())
            ->put('/admin/tags/php', ['name' => 'PHP 8', 'slug' => 'php'])
            ->assertSessionHasNoErrors()
            ->assertRedirect('/admin/tags');

        $this->assertSame('PHP 8', $tag->fresh()->name);
    }

    public function test_deleting_a_tag_detaches_it_from_posts(): void
    {
        $tag = Tag::factory()->create();
        $post = Post::factory()->hasAttached($tag)->create();

        $this->actingAs(User::factory()->create())
            ->delete("/admin/tags/{$tag->slug}")
            ->assertRedirect('/admin/tags');

        $this->assertModelMissing($tag);
        $this->assertModelExists($post);
        $this->assertDatabaseCount('post_tag', 0);
    }
}
