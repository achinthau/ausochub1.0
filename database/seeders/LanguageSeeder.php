<?php

namespace Database\Seeders;

use App\Models\Language;
use Illuminate\Database\Seeder;

class LanguageSeeder extends Seeder
{
    public function run()
    {
        $languages = ['Sinhala', 'English', 'Tamil'];

        foreach ($languages as $lang) {
            Language::firstOrCreate(['name' => $lang]);
        }
    }
}
