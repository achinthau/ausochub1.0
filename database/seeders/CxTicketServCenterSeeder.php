<?php

namespace Database\Seeders;

use App\Models\CxTicketServCenter;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CxTicketServCenterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $serviceCenters = ['Kurunegala', 'Alawwa', 'Narammala', 'Kegalle', 'Polgahawela'];

        foreach ($serviceCenters as $center) {
            CxTicketServCenter::create(['name' => $center]);
        }
    }
}
