<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingTest extends TestCase
{
    use RefreshDatabase;

    private function makeUser(string $role = User::ROLE_CUSTOMER): User
    {
        $user = User::factory()->create();
        $user->forceFill(['role' => $role])->save();

        return $user;
    }

    private function payload(Service $service, array $overrides = []): array
    {
        return array_merge([
            'service_id' => $service->id,
            'booking_date' => now()->addDays(2)->toDateString(),
            'booking_time' => '10:30',
            'address' => 'Some Street 1, Istanbul',
            'notes' => 'Please call before arriving',
        ], $overrides);
    }

    // ---------- Customer: create ----------

    public function test_guest_cannot_create_booking(): void
    {
        $service = Service::factory()->create();

        $this->postJson('/bookings', $this->payload($service))->assertUnauthorized();
    }

    public function test_customer_can_create_booking(): void
    {
        $customer = $this->makeUser();
        $service = Service::factory()->create();

        $this->actingAs($customer)
            ->postJson('/bookings', $this->payload($service))
            ->assertCreated()
            ->assertJsonPath('data.status', 'Pending')
            ->assertJsonPath('data.service.id', $service->id)
            ->assertJsonPath('message', 'Booking created successfully.');

        $this->assertDatabaseHas('bookings', [
            'user_id' => $customer->id,
            'service_id' => $service->id,
            'status' => 'Pending',
        ]);
    }

    public function test_customer_cannot_choose_status_or_owner(): void
    {
        $customer = $this->makeUser();
        $other = $this->makeUser();
        $service = Service::factory()->create();

        $this->actingAs($customer)
            ->postJson('/bookings', $this->payload($service, [
                'status' => 'Completed',
                'user_id' => $other->id,
            ]))
            ->assertCreated();

        $this->assertDatabaseHas('bookings', ['user_id' => $customer->id, 'status' => 'Pending']);
        $this->assertDatabaseMissing('bookings', ['user_id' => $other->id]);
    }

    public function test_booking_validation_errors(): void
    {
        $this->actingAs($this->makeUser())
            ->postJson('/bookings', [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['service_id', 'booking_date', 'booking_time']);
    }

    public function test_cannot_book_inactive_service(): void
    {
        $service = Service::factory()->create(['is_active' => false]);

        $this->actingAs($this->makeUser())
            ->postJson('/bookings', $this->payload($service))
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['service_id']);
    }

    public function test_cannot_book_in_the_past(): void
    {
        $service = Service::factory()->create();

        $this->actingAs($this->makeUser())
            ->postJson('/bookings', $this->payload($service, [
                'booking_date' => now()->subDay()->toDateString(),
            ]))
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['booking_date']);
    }

    // ---------- Customer: my bookings ----------

    public function test_customer_sees_only_own_bookings(): void
    {
        $customer = $this->makeUser();
        $other = $this->makeUser();

        Booking::factory()->count(2)->create(['user_id' => $customer->id]);
        Booking::factory()->create(['user_id' => $other->id]);

        $this->actingAs($customer)
            ->getJson('/bookings')
            ->assertOk()
            ->assertJsonCount(2, 'data');
    }

    public function test_guest_cannot_list_bookings(): void
    {
        $this->getJson('/bookings')->assertUnauthorized();
    }

    // ---------- Admin: list ----------

    public function test_guest_and_customer_cannot_use_admin_bookings(): void
    {
        $this->getJson('/admin/bookings')->assertUnauthorized();

        $this->actingAs($this->makeUser())
            ->getJson('/admin/bookings')
            ->assertForbidden();
    }

    public function test_admin_can_list_and_filter_bookings(): void
    {
        Booking::factory()->count(2)->create(['status' => 'Pending']);
        Booking::factory()->create(['status' => 'Approved']);

        $admin = $this->makeUser(User::ROLE_ADMIN);

        $this->actingAs($admin)
            ->getJson('/admin/bookings')
            ->assertOk()
            ->assertJsonCount(3, 'data');

        $this->actingAs($admin)
            ->getJson('/admin/bookings?status=Approved')
            ->assertOk()
            ->assertJsonCount(1, 'data');
    }

    // ---------- Admin: status workflow ----------

    public function test_admin_can_approve_then_complete(): void
    {
        $booking = Booking::factory()->create(['status' => 'Pending']);
        $admin = $this->makeUser(User::ROLE_ADMIN);

        $this->actingAs($admin)
            ->patchJson("/admin/bookings/{$booking->id}/status", ['status' => 'Approved'])
            ->assertOk()
            ->assertJsonPath('data.status', 'Approved')
            ->assertJsonPath('message', 'Booking status updated successfully.');

        $this->actingAs($admin)
            ->patchJson("/admin/bookings/{$booking->id}/status", ['status' => 'Completed'])
            ->assertOk()
            ->assertJsonPath('data.status', 'Completed');

        $this->assertDatabaseHas('bookings', ['id' => $booking->id, 'status' => 'Completed']);
    }

    public function test_status_cannot_skip_a_step(): void
    {
        $booking = Booking::factory()->create(['status' => 'Pending']);

        $this->actingAs($this->makeUser(User::ROLE_ADMIN))
            ->patchJson("/admin/bookings/{$booking->id}/status", ['status' => 'Completed'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['status']);

        $this->assertDatabaseHas('bookings', ['id' => $booking->id, 'status' => 'Pending']);
    }

    public function test_status_cannot_go_backwards(): void
    {
        $booking = Booking::factory()->create(['status' => 'Completed']);

        $this->actingAs($this->makeUser(User::ROLE_ADMIN))
            ->patchJson("/admin/bookings/{$booking->id}/status", ['status' => 'Approved'])
            ->assertUnprocessable();

        $this->assertDatabaseHas('bookings', ['id' => $booking->id, 'status' => 'Completed']);
    }

    public function test_invalid_status_value_is_rejected(): void
    {
        $booking = Booking::factory()->create();

        $this->actingAs($this->makeUser(User::ROLE_ADMIN))
            ->patchJson("/admin/bookings/{$booking->id}/status", ['status' => 'banana'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['status']);
    }

    public function test_customer_cannot_change_status(): void
    {
        $booking = Booking::factory()->create();

        $this->actingAs($this->makeUser())
            ->patchJson("/admin/bookings/{$booking->id}/status", ['status' => 'Approved'])
            ->assertForbidden();

        $this->assertDatabaseHas('bookings', ['id' => $booking->id, 'status' => 'Pending']);
    }

    // ---------- Data safety ----------

    public function test_service_with_bookings_cannot_be_deleted(): void
    {
        $service = Service::factory()->create();
        Booking::factory()->create(['service_id' => $service->id]);

        $this->actingAs($this->makeUser(User::ROLE_ADMIN))
            ->deleteJson("/admin/services/{$service->id}")
            ->assertStatus(409)
            ->assertJsonPath('message', 'This service has bookings and cannot be deleted. Set it to inactive instead.');

        $this->assertDatabaseHas('services', ['id' => $service->id]);
    }
}
