<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class ComponentsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        DB::table('components')->insert([
            ['category_id' => 1, 'name' => 'Intel Core i9-11900K', 'brand' => 'Intel', 'price' => 499.99, 'compatibility_data' => json_encode(['socket' => 'LGA1200', 'form_factor' => 'ATX'])],
            ['category_id' => 2, 'name' => 'MSI Z590-A PRO', 'brand' => 'MSI', 'price' => 149.99, 'compatibility_data' => json_encode(['socket' => 'LGA1200', 'form_factor' => 'ATX'])],
            ['category_id' => 3, 'name' => 'Corsair Vengeance LPX 16GB', 'brand' => 'Corsair', 'price' => 79.99, 'compatibility_data' => json_encode(['type' => 'DDR4', 'speed' => '3200MHz'])],
            ['category_id' => 4, 'name' => 'NVIDIA GeForce RTX 3080', 'brand' => 'NVIDIA', 'price' => 699.99, 'compatibility_data' => json_encode(['pci_express' => 'PCIe 4.0'])],
            ['category_id' => 5, 'name' => 'Samsung 970 EVO 1TB', 'brand' => 'Samsung', 'price' => 119.99, 'compatibility_data' => json_encode(['interface' => 'M.2', 'type' => 'NVMe'])],
        ]);
    }
}
