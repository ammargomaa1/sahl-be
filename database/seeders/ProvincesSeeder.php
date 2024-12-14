<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProvincesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $provinces = [
            ['name_en' => 'Riyadh', 'name_ar' => 'الرياض'],
            ['name_en' => 'Makkah', 'name_ar' => 'مكة المكرمة'],
            ['name_en' => 'Madinah', 'name_ar' => 'المدينة المنورة'],
            ['name_en' => 'Eastern Province', 'name_ar' => 'الشرقية'],
            ['name_en' => 'Asir', 'name_ar' => 'عسير'],
            ['name_en' => 'Qassim', 'name_ar' => 'القصيم'],
            ['name_en' => 'Tabuk', 'name_ar' => 'تبوك'],
            ['name_en' => 'Hail', 'name_ar' => 'حائل'],
            ['name_en' => 'Northern Borders', 'name_ar' => 'الحدود الشمالية'],
            ['name_en' => 'Jazan', 'name_ar' => 'جازان'],
            ['name_en' => 'Najran', 'name_ar' => 'نجران'],
            ['name_en' => 'Al Bahah', 'name_ar' => 'الباحة'],
            ['name_en' => 'Al Jawf', 'name_ar' => 'الجوف'],
        ];

        DB::table('provinces')->insert($provinces);
    }
}
