<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CitiesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $cities = [
            // Riyadh Province Cities
            ['province_id' => 1, 'name_en' => 'Riyadh City', 'name_ar' => 'مدينة الرياض'],

            // Makkah Province Cities
            ['province_id' => 2, 'name_en' => 'Makkah City', 'name_ar' => 'مدينة مكة المكرمة'],

            // Madinah Province Cities
            ['province_id' => 3, 'name_en' => 'Madinah City', 'name_ar' => 'مدينة المدينة المنورة'],

            // Eastern Province Cities
            ['province_id' => 4, 'name_en' => 'Dammam', 'name_ar' => 'الدمام'],
            ['province_id' => 4, 'name_en' => 'Khobar', 'name_ar' => 'الخبر'],

            // Asir Province Cities
            ['province_id' => 5, 'name_en' => 'Abha', 'name_ar' => 'أبها'],

            // Qassim Province Cities
            ['province_id' => 6, 'name_en' => 'Buraidah', 'name_ar' => 'بريدة'],

            // Tabuk Province Cities
            ['province_id' => 7, 'name_en' => 'Tabuk City', 'name_ar' => 'مدينة تبوك'],

            // Hail Province Cities
            ['province_id' => 8, 'name_en' => 'Hail City', 'name_ar' => 'مدينة حائل'],

            // Northern Borders Province Cities
            ['province_id' => 9, 'name_en' => 'Arar', 'name_ar' => 'عرعر'],

            // Jazan Province Cities
            ['province_id' => 10, 'name_en' => 'Jazan City', 'name_ar' => 'مدينة جازان'],

            // Najran Province Cities
            ['province_id' => 11, 'name_en' => 'Najran City', 'name_ar' => 'مدينة نجران'],

            // Al Bahah Province Cities
            ['province_id' => 12, 'name_en' => 'Al Bahah City', 'name_ar' => 'مدينة الباحة'],

            // Al Jawf Province Cities
            ['province_id' => 13, 'name_en' => 'Sakakah', 'name_ar' => 'سكاكا'],
        ];

        DB::table('cities')->insert($cities);
    }
}
