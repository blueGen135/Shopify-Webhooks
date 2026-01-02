<?php

namespace Database\Seeders;

use App\Models\Warehouse;
use Illuminate\Database\Seeder;

class WarehouseSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    $warehouses = [
      [
        'name' => '789 Global Logistics Hub Innovation Park, San Alto',
        'address' => '600 Pennsylvania Avenue NW',
        'city' => 'San Alto',
        'state' => 'CA',
        'postal_code' => '94301',
        'country_code' => 'US',
        'latitude' => 37.4419,
        'longitude' => -122.1430,
        'status' => true,
        'priority' => 100,
      ],
      [
        'name' => '456 Sierra Distribution Center, Discovery Gardens',
        'address' => '1455 Market Street',
        'city' => 'San Francisco',
        'state' => 'CA',
        'postal_code' => '94103',
        'country_code' => 'US',
        'latitude' => 37.7749,
        'longitude' => -122.4194,
        'status' => true,
        'priority' => 90,
      ],
      [
        'name' => '123 Enterprise Logistics Hub, Innovation District',
        'address' => '350 5th Avenue',
        'city' => 'San Jose',
        'state' => 'CA',
        'postal_code' => '95112',
        'country_code' => 'US',
        'latitude' => 37.3382,
        'longitude' => -121.8863,
        'status' => true,
        'priority' => 80,
      ],
      [
        'name' => 'Pacific Coast Distribution Center',
        'address' => '2500 Ocean Avenue',
        'city' => 'Los Angeles',
        'state' => 'CA',
        'postal_code' => '90001',
        'country_code' => 'US',
        'latitude' => 34.0522,
        'longitude' => -118.2437,
        'status' => true,
        'priority' => 70,
      ],
      [
        'name' => 'Central Valley Logistics Hub',
        'address' => '1800 Industrial Parkway',
        'city' => 'Sacramento',
        'state' => 'CA',
        'postal_code' => '94203',
        'country_code' => 'US',
        'latitude' => 38.5816,
        'longitude' => -121.4944,
        'status' => true,
        'priority' => 60,
      ],
    ];

    foreach ($warehouses as $warehouse) {
      Warehouse::create($warehouse);
    }
  }
}
