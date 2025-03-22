<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class CompatibilityRulesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        DB::table('compatibility_rules')->insert([
            'category1_id' => 5, // Processors
            'category2_id' => 2, // Motherboards
            'condition' => json_encode(["sata_ports"=>">="])
        ]);
    }
}
