<?php

namespace Database\Seeders;

use App\Models\BreakType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BreakTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * php artisan db:seed --class=BreakTypeSeeder
     * @return void
     */
    public function run()
    {
        BreakType::query()->truncate();
        BreakType::create([
            'title'=>'Lunch'
        ]);
        BreakType::create([
            'title'=>'Tea'
        ]);
        BreakType::create([
            'title'=>'Other'
        ]);
    }
}
