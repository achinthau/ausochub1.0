<?php

namespace Database\Seeders;

use App\Models\DialerCallStatusOption;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DialerCallStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DialerCallStatusOption::query()->truncate();
        DialerCallStatusOption::create([
            'option'=>'Promised to pay',
            'type'=>'1',
        ]);
        DialerCallStatusOption::create([
            'option'=>'Not agreed to pay',
            'type'=>'1',
        ]);
        DialerCallStatusOption::create([
            'option'=>'Online payment done',
            'type'=>'1',
        ]);
        DialerCallStatusOption::create([
            'option'=>'Cheque given',
            'type'=>'1',
        ]);
        DialerCallStatusOption::create([
            'option'=>'Negotiate with branch',
            'type'=>'1',
        ]);
        DialerCallStatusOption::create([
            'option'=>'Wrong Person',
            'type'=>'1',
        ]);
        DialerCallStatusOption::create([
            'option'=>'Disconnected phone number',
            'type'=>'1',
        ]);
        DialerCallStatusOption::create([
            'option'=>'Ringing no answer',
            'type'=>'2',
        ]);
        DialerCallStatusOption::create([
            'option'=>'Not reachable',
            'type'=>'2',
        ]);
        DialerCallStatusOption::create([
            'option'=>'Not responding',
            'type'=>'2',
        ]);
        DialerCallStatusOption::create([
            'option'=>'Customer not available',
            'type'=>'2',
        ]);
        DialerCallStatusOption::create([
            'option'=>'Phone switched off',
            'type'=>'2',
        ]);
        DialerCallStatusOption::create([
            'option'=>'Call drop',
            'type'=>'2',
        ]);
        DialerCallStatusOption::create([
            'option'=>'Skip',
            'type'=>'3',
        ]);
    }
}
