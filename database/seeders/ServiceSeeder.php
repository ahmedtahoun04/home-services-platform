<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            ['Home Plumbing Repair', 'Plumbing', 'Fix leaks, pipe issues, and faucet installations with certified plumbers.', 150, 4.9],
            ['Electrical Repairs', 'Electrical', 'Outlet repairs, wiring inspection, and light fixture installation.', 120, 4.8],
            ['Home Cleaning', 'Cleaning', 'Standard room cleaning, dusting, and vacuuming for your home.', 80, 4.9],
            ['AC & Heating Service', 'HVAC', 'Keep your interior comfortable year-round with expert HVAC maintenance.', 140, 4.7],
            ['Drain & Pipe Cleaning', 'Plumbing', 'Clear stubborn clogs and maintain smooth drainage systems safely.', 110, 4.6],
            ['Deep Home Cleaning', 'Cleaning', 'An extra-thorough clean for hard-to-reach areas and heavy-duty spaces.', 180, 4.9],
        ];

        foreach ($services as [$name, $category, $description, $price, $rating]) {
            Service::updateOrCreate(
                ['name' => $name],
                [
                    'category' => $category,
                    'description' => $description,
                    'price' => $price,
                    'rating' => $rating,
                    'is_active' => true,
                ]
            );
        }
    }
}
