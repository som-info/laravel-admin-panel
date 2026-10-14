<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get('/')->assertRedirect('/login');
        $this->get('/login')->assertOk()->assertSee('admin@example.com');
    }

    public function test_user_can_log_in_and_out(): void
    {
        $user = User::factory()->create(['password' => 'secret123']);

        $this->post('/login', ['email' => $user->email, 'password' => 'secret123'])->assertRedirect('/');
        $this->assertAuthenticatedAs($user);
        $this->assertDatabaseHas('activity_logs', ['user_id' => $user->id, 'action' => 'login']);

        $this->post('/logout')->assertRedirect('/login');
        $this->assertGuest();
    }

    public function test_invalid_credentials_are_rejected(): void
    {
        $user = User::factory()->create();

        $this->from('/login')->post('/login', ['email' => $user->email, 'password' => 'wrong'])
            ->assertRedirect('/login')->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_inactive_users_cannot_log_in(): void
    {
        $user = User::factory()->create(['password' => 'secret123', 'is_active' => false]);

        $this->post('/login', ['email' => $user->email, 'password' => 'secret123'])->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_locale_can_be_switched(): void
    {
        $this->get('/locale/en')->assertRedirect();
        $this->get('/login')->assertSee('dir="ltr"', false)->assertSee('Welcome back');

        $this->get('/locale/fa');
        $this->get('/login')->assertSee('dir="rtl"', false)->assertSee('خوش آمدید');
    }
}
