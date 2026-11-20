<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Booking>
 */
class BookingFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'service_id' => Service::factory(),
            'booking_date' => now()->addDays(3)->toDateString(),
            'booking_time' => '10:00',
            'address' => fake()->streetAddress(),
            'notes' => fake()->sentence(),
            'status' => Booking::STATUS_PENDING,
        ];
    }
}
