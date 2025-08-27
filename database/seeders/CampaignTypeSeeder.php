<?php

namespace Database\Seeders;

use App\Models\CampaignType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CampaignTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $types = ['Manual Dialer', 'Preview Dialer', 'Progressive Dialer', 'Predictive Dialer', 'Preview Progressive Dialer', 'Auto Dialer'];

        foreach ($types as $type) {
            CampaignType::create(['name' => $type]);
        }
    }
}
