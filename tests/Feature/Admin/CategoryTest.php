<?php

namespace Tests\Feature\Admin;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get('/admin/categories')->assertRedirect('/login');
        $this->post('/admin/categories', ['name' => 'PHP', 'slug' => 'php'])->assertRedirect('/login');

        $this->assertDatabaseCount('categories', 0);
    }

    public function test_index_lists_categories(): void
    {
        $category = Category::factory()->create();

        $this->actingAs(User::factory()->create())
            ->get('/admin/categories')
            ->assertOk()
            ->assertSee($category->name);
    }

    public function test_create_screen_can_be_rendered(): void
    {
        $this->actingAs(User::factory()->create())
            ->get('/admin/categories/create')
            ->assertOk();
    }

    public function test_category_can_be_created(): void
    {
        $this->actingAs(User::factory()->create())
            ->post('/admin/categories', ['name' => 'PHP', 'slug' => 'php'])
            ->assertSessionHasNoErrors()
            ->assertRedirect('/admin/categories');

        $this->assertDatabaseHas('categories', ['name' => 'PHP', 'slug' => 'php']);
    }

    public function test_slug_must_be_unique_and_url_safe(): void
    {
        Category::factory()->create(['slug' => 'php']);
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post('/admin/categories', ['name' => 'PHP', 'slug' => 'php'])
            ->assertSessionHasErrors('slug');

        $this->actingAs($user)
            ->post('/admin/categories', ['name' => 'PHP', 'slug' => 'ピーエイチピー'])
            ->assertSessionHasErrors('slug');

        $this->actingAs($user)
            ->post('/admin/categories', ['name' => '', 'slug' => ''])
            ->assertSessionHasErrors(['name', 'slug']);

        $this->assertDatabaseCount('categories', 1);
    }

    public function test_edit_screen_is_found_by_slug(): void
    {
        $category = Category::factory()->create(['slug' => 'php']);

        $this->actingAs(User::factory()->create())
            ->get('/admin/categories/php/edit')
            ->assertOk()
            ->assertSee($category->name);
    }

    public function test_category_can_be_updated_with_its_own_slug(): void
    {
        $category = Category::factory()->create(['slug' => 'php']);

        $this->actingAs(User::factory()->create())
            ->put('/admin/categories/php', ['name' => 'PHP 8', 'slug' => 'php'])
            ->assertSessionHasNoErrors()
            ->assertRedirect('/admin/categories');

        $this->assertSame('PHP 8', $category->fresh()->name);
    }

    public function test_category_cannot_take_another_categorys_slug(): void
    {
        Category::factory()->create(['slug' => 'php']);
        $category = Category::factory()->create(['slug' => 'laravel']);

        $this->actingAs(User::factory()->create())
            ->put('/admin/categories/laravel', ['name' => 'Laravel', 'slug' => 'php'])
            ->assertSessionHasErrors('slug');

        $this->assertSame('laravel', $category->fresh()->slug);
    }

    public function test_deleting_a_category_leaves_its_posts_uncategorized(): void
    {
        $category = Category::factory()->create();
        $post = Post::factory()->for($category)->create();

        $this->actingAs(User::factory()->create())
            ->delete("/admin/categories/{$category->slug}")
            ->assertRedirect('/admin/categories');

        $this->assertModelMissing($category);
        $this->assertNull($post->fresh()->category_id);
    }
}
