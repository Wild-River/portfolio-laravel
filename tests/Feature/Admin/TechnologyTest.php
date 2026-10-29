<?php

namespace Tests\Feature\Admin;

use App\Enums\TechnologyCategory;
use App\Models\Technology;
use App\Models\User;
use App\Models\Work;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TechnologyTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Get valid form input, overridden by the given values.
     *
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function validInput(array $overrides = []): array
    {
        return [
            'name' => 'React',
            'category' => 'frontend',
            'icon' => 'react',
            'color' => '#61DAFB',
            'sort_order' => 10,
            ...$overrides,
        ];
    }

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get('/admin/technologies')->assertRedirect('/login');
        $this->post('/admin/technologies', $this->validInput())->assertRedirect('/login');

        $this->assertDatabaseCount('technologies', 0);
    }

    public function test_index_lists_technologies(): void
    {
        $technology = Technology::factory()->create();

        $this->actingAs(User::factory()->create())
            ->get('/admin/technologies')
            ->assertOk()
            ->assertSee($technology->name);
    }

    public function test_create_screen_can_be_rendered(): void
    {
        $this->actingAs(User::factory()->create())
            ->get('/admin/technologies/create')
            ->assertOk();
    }

    public function test_technology_can_be_created(): void
    {
        $this->actingAs(User::factory()->create())
            ->post('/admin/technologies', $this->validInput())
            ->assertSessionHasNoErrors()
            ->assertRedirect('/admin/technologies');

        $this->assertDatabaseHas('technologies', ['name' => 'React', 'category' => 'frontend', 'icon' => 'react', 'color' => '#61DAFB']);
    }

    public function test_icon_and_color_are_optional(): void
    {
        $this->actingAs(User::factory()->create())
            ->post('/admin/technologies', $this->validInput(['icon' => null, 'color' => null]))
            ->assertSessionHasNoErrors();

        $this->assertNull(Technology::firstOrFail()->icon_url);
    }

    public function test_icon_must_be_a_slug_and_color_a_hex_code(): void
    {
        $this->actingAs(User::factory()->create())
            ->post('/admin/technologies', $this->validInput(['icon' => '../react', 'color' => 'red', 'category' => 'unknown']))
            ->assertSessionHasErrors(['icon', 'color', 'category']);

        $this->assertDatabaseCount('technologies', 0);
    }

    public function test_icon_can_be_a_file_name_in_the_icon_folder(): void
    {
        $this->actingAs(User::factory()->create())
            ->post('/admin/technologies', $this->validInput(['name' => 'さくらのレンタルサーバー', 'icon' => 'sakura-internet.svg', 'color' => null]))
            ->assertSessionHasNoErrors();

        $technology = Technology::firstOrFail();
        $this->assertTrue($technology->has_icon_file);
        $this->assertSame(asset('images/technologies/sakura-internet.svg'), $technology->icon_url);
    }

    public function test_icon_file_name_cannot_leave_the_icon_folder(): void
    {
        $user = User::factory()->create();

        foreach (['../secret.svg', 'sub/icon.svg', 'icon.php', '.svg'] as $icon) {
            $this->actingAs($user)
                ->post('/admin/technologies', $this->validInput(['icon' => $icon]))
                ->assertSessionHasErrors('icon');
        }

        $this->assertDatabaseCount('technologies', 0);
    }

    public function test_technology_can_be_updated(): void
    {
        $technology = Technology::factory()->create();

        $this->actingAs(User::factory()->create())
            ->put("/admin/technologies/{$technology->id}", $this->validInput(['name' => 'React 19']))
            ->assertSessionHasNoErrors()
            ->assertRedirect('/admin/technologies');

        $this->assertSame('React 19', $technology->fresh()->name);
    }

    public function test_deleting_a_technology_detaches_it_from_works(): void
    {
        $technology = Technology::factory()->create();
        $work = Work::factory()->hasAttached($technology)->create();

        $this->actingAs(User::factory()->create())
            ->delete("/admin/technologies/{$technology->id}")
            ->assertRedirect('/admin/technologies');

        $this->assertModelMissing($technology);
        $this->assertModelExists($work);
        $this->assertDatabaseCount('technology_work', 0);
    }

    public function test_technologies_are_ordered_by_category_then_sort_order(): void
    {
        Technology::factory()->create(['name' => 'Vercel', 'category' => TechnologyCategory::Tool, 'sort_order' => 1]);
        Technology::factory()->create(['name' => 'Vite', 'category' => TechnologyCategory::Frontend, 'sort_order' => 20]);
        Technology::factory()->create(['name' => 'PHP', 'category' => TechnologyCategory::Backend, 'sort_order' => 1]);
        Technology::factory()->create(['name' => 'React', 'category' => TechnologyCategory::Frontend, 'sort_order' => 10]);

        $this->assertSame(['React', 'Vite', 'PHP', 'Vercel'], Technology::ordered()->pluck('name')->all());
    }
}
