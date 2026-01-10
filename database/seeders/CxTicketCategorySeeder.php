<?php

namespace Database\Seeders;

use App\Models\CxTicketCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CxTicketCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categories = ['Service', 'Repair', 'Installation'];

        foreach ($categories as $category) {
            CxTicketCategory::create(['name' => $category]);
        }
    }
}
