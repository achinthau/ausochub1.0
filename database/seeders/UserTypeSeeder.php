<?php

namespace Database\Seeders;

use App\Models\UserType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * php artisan db:seed --class=UserTypeSeeder
     * @return void
     */
    public function run()
    {
        UserType::query()->truncate();

        UserType::create([
            'title'=>'Super Admin'
        ]);
        UserType::create([
            'title'=>'Admin'
        ]);
        UserType::create([
            'title'=>'Supervisor'
        ]);
        UserType::create([
            'title'=>'Agent'
        ]);
        UserType::create([
            'title'=>'Outlet Supervisor'
        ]);
        UserType::create([
            'title'=>'Outlet User'
        ]);
        UserType::create([
            'title'=>'Client Admin'
        ]);
        UserType::create([
            'title'=>'Client Report User'
        ]);
        UserType::create([
            'title'=>'CRM User'
        ]);
    }
}
