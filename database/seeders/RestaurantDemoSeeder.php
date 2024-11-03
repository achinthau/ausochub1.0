<?php

namespace Database\Seeders;

use App\Models\Item;
use App\Models\Outlet;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RestaurantDemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * php artisan db:seed --class=RestaurantDemoSeeder
     * @return void
     */
    public function run()
    {
        /* Item::query()->truncate();
        Item::create(
            [
                'title' => 'Roast Chicken Kottu',
                'description' => 'Roti kottu with roast chicken',
            ]
        );
        Item::create(
            [
                'title' => 'Chicken Cheese Kottu',
                'description' => 'Roti kottu with chicken and imported cheese',
            ]
        );
        Item::create(
            [
                'title' => 'Sinhala Arya Kottu',
                'description' => 'Signature dish of Mr. Kottu',
            ]
        ); */

        /* Outlet::query()->truncate();

        Outlet::create(
            [
                'title' => 'Pannipitiya',
                'contact_no' => '001'
            ]
        );
        Outlet::create(
            [
                'title' => 'Rajagiriya',
                'contact_no' => '003'
            ]
        );
        Outlet::create(
            [
                'title' => 'Kotte',
                'contact_no' => '004'
            ]
        );
        Outlet::create(
            [
                'title' => 'Battaramulla',
                'contact_no' => '005'
            ]
        );
        Outlet::create(
            [
                'title' => 'Maharagama',
                'contact_no' => '006'
            ]
        );
        Outlet::create(
            [
                'title' => 'Kaduwela',
                'contact_no' => '007'
            ]
        );
        Outlet::create(
            [
                'title' => 'Nugegoda',
                'contact_no' => '008'
            ]
        );
        Outlet::create(
            [
                'title' => 'Piliyandala',
                'contact_no' => '009'
            ]
        ); */

        Outlet::create(
            [
                'title' => 'Godagama',
                'contact_no' => '010'
            ]
        );
    }
}
