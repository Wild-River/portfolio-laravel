<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RemovedRoutesTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_is_not_available(): void
    {
        $this->get('/register')->assertNotFound();
        $this->post('/register', [
            'name' => 'Intruder',
            'email' => 'intruder@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ])->assertNotFound();

        $this->assertDatabaseCount('users', 0);
    }

    public function test_password_reset_is_not_available(): void
    {
        $this->get('/forgot-password')->assertNotFound();
        $this->post('/forgot-password', ['email' => 'admin@example.com'])->assertNotFound();
        $this->get('/reset-password/token')->assertNotFound();
        $this->post('/reset-password')->assertNotFound();
    }

    public function test_email_verification_is_not_available(): void
    {
        $this->actingAs(User::factory()->unverified()->create());

        $this->get('/verify-email')->assertNotFound();
        $this->post('/email/verification-notification')->assertNotFound();
    }

    public function test_unverified_admin_can_open_the_dashboard(): void
    {
        $this->actingAs(User::factory()->unverified()->create())
            ->get('/dashboard')
            ->assertOk();
    }

    public function test_guests_are_redirected_from_dashboard_and_profile(): void
    {
        $this->get('/dashboard')->assertRedirect('/login');
        $this->get('/profile')->assertRedirect('/login');
    }

    public function test_login_page_has_no_links_to_removed_features(): void
    {
        $this->get('/login')
            ->assertOk()
            ->assertDontSee('/register')
            ->assertDontSee('/forgot-password');
    }
}
