<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Seeder;

class BookingSeeder extends Seeder
{
    /** Demo bookings (one per status) so the admin dashboard has data to show. */
    public function run(): void
    {
        $customer = User::where('email', 'customer@homely.test')->first();
        $services = Service::active()->orderBy('id')->take(3)->get();

        if (! $customer || $services->isEmpty() || Booking::exists()) {
            return;
        }

        $statuses = [Booking::STATUS_PENDING, Booking::STATUS_APPROVED, Booking::STATUS_COMPLETED];

        foreach ($services as $i => $service) {
            (new Booking)->forceFill([
                'user_id' => $customer->id,
                'service_id' => $service->id,
                'booking_date' => now()->addDays($i + 1)->toDateString(),
                'booking_time' => '10:00',
                'address' => 'Demo Street '.($i + 1).', Istanbul',
                'notes' => 'Demo booking',
                'status' => $statuses[$i],
            ])->save();
        }
    }
}
