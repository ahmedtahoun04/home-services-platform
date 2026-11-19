<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPagesTest extends TestCase
{
    use RefreshDatabase;

    private function makeUser(string $role): User
    {
        $user = User::factory()->create();
        $user->forceFill(['role' => $role])->save();

        return $user;
    }

    public function test_guest_is_redirected_from_services_page(): void
    {
        $this->get('/admin/manage-services')->assertRedirect(route('login'));
    }

    public function test_customer_cannot_open_services_page(): void
    {
        $this->actingAs($this->makeUser(User::ROLE_CUSTOMER))
            ->get('/admin/manage-services')
            ->assertForbidden();
    }

    public function test_admin_can_open_services_page(): void
    {
        $this->actingAs($this->makeUser(User::ROLE_ADMIN))
            ->get('/admin/manage-services')
            ->assertOk()
            ->assertSee('Manage Services')
            ->assertSee('csrf-token', false);
    }

    public function test_services_page_route_name_and_url(): void
    {
        $this->assertSame('/admin/manage-services', route('admin.pages.services', [], false));
    }

    public function test_dashboard_uses_admin_layout_with_services_link(): void
    {
        $this->actingAs($this->makeUser(User::ROLE_ADMIN))
            ->get('/admin/dashboard')
            ->assertOk()
            ->assertSee('Admin Dashboard')
            ->assertSee(route('admin.pages.services'), false);
    }
}
