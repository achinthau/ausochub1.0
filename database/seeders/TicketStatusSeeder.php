<?php

namespace Database\Seeders;

use App\Models\TicketStatus;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TicketStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * php artisan db:seed --class=TicketStatusSeeder
     * @return void
     */
    public function run()
    {
        TicketStatus::query()->truncate();

        TicketStatus::create([
            'title'=>'New',
            'step'=>1,
            'color'=>'blue'
        ]);
        TicketStatus::create([
            'title'=>'Open',
            'step'=>2,
            'color'=>'green'
        ]);
        TicketStatus::create([
            'title'=>'Overdue',
            'step'=>3,
            'color'=>'orange'
        ]);
        TicketStatus::create([
            'title'=>'Closed',
            'step'=>4,
            'color'=>'stone'
        ]);
    }
}
