<?php

namespace Database\Seeders;

use App\Models\CrmDepartment;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CrmDepartmentsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        CrmDepartment::create([
            'name'=>'Development'
        ]);
        CrmDepartment::create([
            'name'=>'Support'
        ]);
        CrmDepartment::create([
            'name'=>'Network'
        ]);
        CrmDepartment::create([
            'name'=>'System'
        ]);
    }
}
