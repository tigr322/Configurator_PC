<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class ConfigurationComponentsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        DB::table('configuration_components')->insert([
            ['configuration_id' => 2, 'component_id' => 1],
            ['configuration_id' => 2, 'component_id' => 2],
            ['configuration_id' => 2, 'component_id' => 3],
            ['configuration_id' => 2, 'component_id' => 4],
            ['configuration_id' => 2, 'component_id' => 5],
        ]);
    }
}
