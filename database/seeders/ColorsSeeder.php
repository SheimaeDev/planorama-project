<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Illuminate\Support\Facades\DB;

class ColorsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
         DB::table('colors')->insert([
            ['name' => 'Soft Blue', 'hex' => '#A7C7E7'],
            ['name' => 'Peach', 'hex' => '#FFDAB9'],
            ['name' => 'Mint Green', 'hex' => '#B2F7EF'],
            ['name' => 'Lavender', 'hex' => '#E6E6FA'],
            ['name' => 'Pale Yellow', 'hex' => '#FFFACD'],
            ['name' => 'Rose Pink', 'hex' => '#F4C2C2'],
            ['name' => 'Light Gray', 'hex' => '#D3D3D3'],
        ]);
    }
}
