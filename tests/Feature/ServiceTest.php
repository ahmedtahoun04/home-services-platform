<?php

namespace Tests\Feature;

use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServiceTest extends TestCase
{
    use RefreshDatabase;

    private function makeUser(string $role): User
    {
        $user = User::factory()->create();
        $user->forceFill(['role' => $role])->save();

        return $user;
    }

    private function validPayload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Water Heater Repair',
            'category' => 'Plumbing',
            'description' => 'Repair and service of water heaters.',
            'price' => 200,
            'rating' => 4.5,
        ], $overrides);
    }

    // ---------- Public catalog ----------

    public function test_public_list_returns_only_active_services(): void
    {
        Service::factory()->count(2)->create();
        Service::factory()->create(['is_active' => false]);

        $this->getJson('/api/services')
            ->assertOk()
            ->assertJsonCount(2, 'data');
    }

    public function test_public_list_can_be_searched(): void
    {
        Service::factory()->create(['name' => 'Pipe Repair']);
        Service::factory()->create(['name' => 'Home Cleaning']);

        $this->getJson('/api/services?search=Pipe')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.name', 'Pipe Repair');
    }

    public function test_public_list_can_be_filtered_by_category(): void
    {
        Service::factory()->create(['category' => 'Plumbing']);
        Service::factory()->create(['category' => 'HVAC']);

        $this->getJson('/api/services?category=HVAC')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.category', 'HVAC');
    }

    public function test_inactive_service_is_hidden_from_public_show(): void
    {
        $service = Service::factory()->create(['is_active' => false]);

        $this->getJson("/api/services/{$service->id}")->assertNotFound();
    }

    // ---------- Admin CRUD ----------

    public function test_guest_cannot_create_service(): void
    {
        $this->postJson('/admin/services', $this->validPayload())->assertUnauthorized();
    }

    public function test_customer_cannot_create_service(): void
    {
        $this->actingAs($this->makeUser(User::ROLE_CUSTOMER))
            ->postJson('/admin/services', $this->validPayload())
            ->assertForbidden();

        $this->assertDatabaseCount('services', 0);
    }

    public function test_admin_can_create_service(): void
    {
        $this->actingAs($this->makeUser(User::ROLE_ADMIN))
            ->postJson('/admin/services', $this->validPayload())
            ->assertCreated();

        $this->assertDatabaseHas('services', ['name' => 'Water Heater Repair']);
    }

    public function test_service_validation_errors(): void
    {
        $this->actingAs($this->makeUser(User::ROLE_ADMIN))
            ->postJson('/admin/services', [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['name', 'category', 'price']);
    }

    public function test_admin_can_update_service(): void
    {
        $service = Service::factory()->create(['price' => 100]);

        $this->actingAs($this->makeUser(User::ROLE_ADMIN))
            ->putJson("/admin/services/{$service->id}", ['price' => 150])
            ->assertOk();

        $this->assertDatabaseHas('services', ['id' => $service->id, 'price' => 150]);
    }

    public function test_admin_can_delete_service(): void
    {
        $service = Service::factory()->create();

        $this->actingAs($this->makeUser(User::ROLE_ADMIN))
            ->deleteJson("/admin/services/{$service->id}")
            ->assertNoContent();

        $this->assertDatabaseMissing('services', ['id' => $service->id]);
    }

    // ---------- Home page ----------

    public function test_home_page_shows_services_from_database(): void
    {
        Service::factory()->create(['name' => 'Zebra Plumbing Special', 'category' => 'Plumbing']);

        $this->get('/')->assertOk()->assertSee('Zebra Plumbing Special');
    }

    public function test_home_page_filters_by_category(): void
    {
        Service::factory()->create(['name' => 'Zebra Plumbing Special', 'category' => 'Plumbing']);
        Service::factory()->create(['name' => 'Alpha Cleaning Deluxe', 'category' => 'Cleaning']);

        $this->get('/?category=Plumbing')
            ->assertOk()
            ->assertSee('Zebra Plumbing Special')
            ->assertDontSee('Alpha Cleaning Deluxe');
    }
}
