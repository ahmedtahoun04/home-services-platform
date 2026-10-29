<?php

namespace Tests\Feature;

use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiErrorFormatTest extends TestCase
{
    use RefreshDatabase;

    private function makeUser(string $role): User
    {
        $user = User::factory()->create();
        $user->forceFill(['role' => $role])->save();

        return $user;
    }

    public function test_missing_record_returns_unified_404(): void
    {
        $this->getJson('/api/services/9999')
            ->assertNotFound()
            ->assertExactJson(['message' => 'Resource not found.']);
    }

    public function test_unknown_api_route_returns_unified_404(): void
    {
        $this->getJson('/api/does-not-exist')
            ->assertNotFound()
            ->assertExactJson(['message' => 'Resource not found.']);
    }

    public function test_wrong_http_method_returns_unified_405(): void
    {
        $this->postJson('/api/services', [])
            ->assertStatus(405)
            ->assertExactJson(['message' => 'Method not allowed.']);
    }

    public function test_guest_gets_unified_401_on_admin_api(): void
    {
        $this->getJson('/admin/services')
            ->assertUnauthorized()
            ->assertExactJson(['message' => 'Unauthenticated.']);
    }

    public function test_customer_gets_unified_403_on_admin_api(): void
    {
        $this->actingAs($this->makeUser(User::ROLE_CUSTOMER))
            ->getJson('/admin/services')
            ->assertForbidden()
            ->assertExactJson(['message' => 'This area is restricted to administrators.']);
    }

    public function test_validation_error_has_message_and_errors(): void
    {
        $this->actingAs($this->makeUser(User::ROLE_ADMIN))
            ->postJson('/admin/services', [])
            ->assertUnprocessable()
            ->assertJsonStructure(['message', 'errors' => ['name', 'category', 'price']]);
    }

    public function test_create_response_has_data_and_message(): void
    {
        $this->actingAs($this->makeUser(User::ROLE_ADMIN))
            ->postJson('/admin/services', [
                'name' => 'Water Heater Repair',
                'category' => 'Plumbing',
                'price' => 200,
            ])
            ->assertCreated()
            ->assertJsonStructure(['data' => ['id', 'name', 'category', 'price', 'rating', 'is_active'], 'message'])
            ->assertJsonPath('message', 'Service created successfully.');
    }

    public function test_update_response_has_data_and_message(): void
    {
        $service = Service::factory()->create();

        $this->actingAs($this->makeUser(User::ROLE_ADMIN))
            ->putJson("/admin/services/{$service->id}", ['price' => 99])
            ->assertOk()
            ->assertJsonStructure(['data' => ['id', 'name'], 'message'])
            ->assertJsonPath('message', 'Service updated successfully.');
    }

    public function test_normal_web_404_is_not_json(): void
    {
        $this->get('/no-such-page')->assertNotFound();
    }
}
