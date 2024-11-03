<?php

namespace Database\Seeders;

use App\Models\Agent;
use App\Models\Outlet;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * php artisan db:seed --class=UserSeeder
     * @return void
     */
    public function run()
    {
        User::query()->truncate();

        User::create([
            'name' => 'admin',
            'email' => 'achintha@ausoworld.com',
            'password' => Hash::make('admin'),
            'user_type_id' => 1,
        ]);

        User::create([
            'name' => 'Ashwin Corera',
            'email' => 'macorera@gmail.com',
            'password' => Hash::make('dev@1234'),
            'user_type_id' => 1,
        ]);

        User::create([
            'name' => 'Nipuna Fernando',
            'email' => 'nipuna@init-tech.com',
            'password' => Hash::make('nipuna@1234'),
            'user_type_id' => 1,
        ]);


        


        $agents = Agent::users()->get();

        foreach ($agents as $agent) {
            User::create([
                'name' => $agent->full_name,
                'email' => $agent->email,
                'password' => Hash::make($agent->password),
                'agent_id' => $agent->id,
                'user_type_id' => 3,
            ]);
        }


        $outlets = Outlet::all();


        foreach ($outlets as $key => $outlet) {
            User::create([
                'name' => $outlet->title." Outlet",
                'email' => $outlet->title."@mrkottu.com",
                'password' => Hash::make($outlet->title."@1234"),
                'user_type_id' => 6,
                'outlet_id' => $outlet->id,
            ]);
        }

    }
}
