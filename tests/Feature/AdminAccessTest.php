<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAccessTest extends TestCase
{
    use RefreshDatabase;

    private function makeUser(string $role): User
    {
        $user = User::factory()->create();
        $user->forceFill(['role' => $role])->save();

        return $user;
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/admin/dashboard')->assertRedirect(route('login'));
    }

    public function test_customer_cannot_open_admin_dashboard(): void
    {
        $this->actingAs($this->makeUser(User::ROLE_CUSTOMER))
            ->get('/admin/dashboard')
            ->assertForbidden();
    }

    public function test_admin_can_open_admin_dashboard(): void
    {
        $this->actingAs($this->makeUser(User::ROLE_ADMIN))
            ->get('/admin/dashboard')
            ->assertOk()
            ->assertSee('Admin Dashboard');
    }

    public function test_admin_login_redirects_to_dashboard(): void
    {
        $admin = User::factory()->create(['password' => 'password123']);
        $admin->forceFill(['role' => User::ROLE_ADMIN])->save();

        $this->post('/login', ['email' => $admin->email, 'password' => 'password123'])
            ->assertRedirect(route('admin.dashboard'));
    }
}
