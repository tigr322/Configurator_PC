<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class CategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        DB::table('categories')->insert([
            ['name' => 'Processors'],
            ['name' => 'Motherboards'],
            ['name' => 'Memory'],
            ['name' => 'Graphics Cards'],
            ['name' => 'Storage'],
        ]);
    }
}
