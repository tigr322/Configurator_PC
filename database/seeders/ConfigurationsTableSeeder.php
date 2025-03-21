<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class ConfigurationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        DB::table('configurations')->insert([
            'user_id' => 1,
            'name' => 'Normal',
            'total_price' => 129.99
        ]);
    }
}
