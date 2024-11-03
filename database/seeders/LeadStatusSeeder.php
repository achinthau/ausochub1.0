<?php

namespace Database\Seeders;

use App\Models\LeadStatus;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LeadStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * php artisan db:seed --class=LeadStatusSeeder
     * @return void
     */
    public function run()
    {
        LeadStatus::query()->truncate();
        LeadStatus::create([
            'title'=>'Incomplete'
        ]);
        LeadStatus::create([
            'title'=>'Completed'
        ]);
    }
}
