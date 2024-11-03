<?php

namespace Database\Seeders;

use App\Models\Skill;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AgentSkillSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * php artisan db:seed --class=AgentSkillSeeder
     * @return void
     */
    public function run()
    {
        $users = User::agents()->get();

        foreach ($users as $user) {
            if ($user->skills) {
                $skillTitles = explode(',',$user->skills->skills);
                $skills = Skill::whereIn('skillname',$skillTitles)->get()->pluck( 'skillname','skillid');
                $user->skills->skill_ids=$skills;
                $user->skills->save();
            }
        }
    }
}
