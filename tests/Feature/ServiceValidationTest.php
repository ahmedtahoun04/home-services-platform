<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServiceValidationTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        $user = User::factory()->create();
        $user->forceFill(['role' => User::ROLE_ADMIN])->save();

        return $user;
    }

    public function test_null_rating_is_a_validation_error_not_a_server_error(): void
    {
        $this->actingAs($this->admin())
            ->postJson('/admin/services', [
                'name' => 'Rating Test',
                'category' => 'Plumbing',
                'price' => 10,
                'rating' => null,
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['rating']);
    }

    public function test_service_can_be_created_without_a_rating(): void
    {
        $this->actingAs($this->admin())
            ->postJson('/admin/services', [
                'name' => 'No Rating Service',
                'category' => 'Plumbing',
                'price' => 10,
            ])
            ->assertCreated();

        $this->assertDatabaseHas('services', ['name' => 'No Rating Service', 'rating' => 0]);
    }
}
