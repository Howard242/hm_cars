<?php

namespace Database\Seeders;

use App\Models\Vehicle;
use Illuminate\Database\Seeder;

class VehicleSeeder extends Seeder
{
    public function run(): void
    {
        $vehicles = [
            [
                'make' => 'Toyota', 'model' => 'Fielder', 'year' => 2018,
                'price' => 1250000, 'location' => 'Nairobi',
                'transmission' => 'Automatic', 'fuel_type' => 'Petrol',
                'mileage_km' => 78000, 'slug' => 'toyota-fielder-2018-demo',
                'status' => 'published',
                'description' => 'Demo listing for local development. Replace with a real, verified listing before launch.',
                'seller_name' => 'Demo Seller', 'seller_phone' => '0700000000',
            ],
            [
                'make' => 'Mazda', 'model' => 'Demio', 'year' => 2017,
                'price' => 780000, 'location' => 'Mombasa',
                'transmission' => 'Automatic', 'fuel_type' => 'Petrol',
                'mileage_km' => 65000, 'slug' => 'mazda-demio-2017-demo',
                'status' => 'published',
                'description' => 'Demo listing for local development. Vehicle details are illustrative only.',
                'seller_name' => 'Demo Seller', 'seller_phone' => '0700000000',
            ],
            [
                'make' => 'Honda', 'model' => 'Vezel', 'year' => 2020,
                'price' => 2350000, 'location' => 'Kisumu',
                'transmission' => 'Automatic', 'fuel_type' => 'Hybrid',
                'mileage_km' => 52000, 'slug' => 'honda-vezel-2020-demo',
                'status' => 'published',
                'description' => 'Demo listing for local development. Confirm all specifications and documents for real listings.',
                'seller_name' => 'Demo Seller', 'seller_phone' => '0700000000',
            ],
        ];

        foreach ($vehicles as $vehicle) {
            Vehicle::updateOrCreate(['slug' => $vehicle['slug']], $vehicle);
        }
    }
}
