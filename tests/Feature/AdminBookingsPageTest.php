<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminBookingsPageTest extends TestCase
{
    use RefreshDatabase;

    private function makeUser(string $role): User
    {
        $user = User::factory()->create();
        $user->forceFill(['role' => $role])->save();

        return $user;
    }

    public function test_guest_is_redirected_from_bookings_page(): void
    {
        $this->get('/admin/manage-bookings')->assertRedirect(route('login'));
    }

    public function test_customer_cannot_open_bookings_page(): void
    {
        $this->actingAs($this->makeUser(User::ROLE_CUSTOMER))
            ->get('/admin/manage-bookings')
            ->assertForbidden();
    }

    public function test_admin_can_open_bookings_page(): void
    {
        $this->actingAs($this->makeUser(User::ROLE_ADMIN))
            ->get('/admin/manage-bookings')
            ->assertOk()
            ->assertSee('Manage Bookings')
            ->assertSee('csrf-token', false);
    }

    public function test_bookings_page_route_name_and_url(): void
    {
        $this->assertSame('/admin/manage-bookings', route('admin.pages.bookings', [], false));
    }

    public function test_admin_sidebar_links_to_bookings_page(): void
    {
        $this->actingAs($this->makeUser(User::ROLE_ADMIN))
            ->get('/admin/dashboard')
            ->assertOk()
            ->assertSee(route('admin.pages.bookings'), false);
    }
}
