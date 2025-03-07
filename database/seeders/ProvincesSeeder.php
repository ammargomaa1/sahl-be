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
            ['id' => 1, 'name_en' => 'Cairo', 'name_ar' => 'القاهرة'],
            ['id' => 2, 'name_en' => 'Giza', 'name_ar' => 'الجيزة'],
            ['id' => 3, 'name_en' => 'Alexandria', 'name_ar' => 'الأسكندرية'],
            ['id' => 4, 'name_en' => 'Dakahlia', 'name_ar' => 'الدقهلية'],
            ['id' => 5, 'name_en' => 'Red Sea', 'name_ar' => 'البحر الأحمر'],
            ['id' => 6, 'name_en' => 'Beheira', 'name_ar' => 'البحيرة'],
            ['id' => 7, 'name_en' => 'Fayoum', 'name_ar' => 'الفيوم'],
            ['id' => 8, 'name_en' => 'Gharbiya', 'name_ar' => 'الغربية'],
            ['id' => 9, 'name_en' => 'Ismailia', 'name_ar' => 'الإسماعلية'],
            ['id' => 10, 'name_en' => 'Menofia', 'name_ar' => 'المنوفية'],
            ['id' => 11, 'name_en' => 'Minya', 'name_ar' => 'المنيا'],
            ['id' => 12, 'name_en' => 'Qaliubiya', 'name_ar' => 'القليوبية'],
            ['id' => 13, 'name_en' => 'New Valley', 'name_ar' => 'الوادي الجديد'],
            ['id' => 14, 'name_en' => 'Suez', 'name_ar' => 'السويس'],
            ['id' => 15, 'name_en' => 'Aswan', 'name_ar' => 'اسوان'],
            ['id' => 16, 'name_en' => 'Assiut', 'name_ar' => 'اسيوط'],
            ['id' => 17, 'name_en' => 'Beni Suef', 'name_ar' => 'بني سويف'],
            ['id' => 18, 'name_en' => 'Port Said', 'name_ar' => 'بورسعيد'],
            ['id' => 19, 'name_en' => 'Damietta', 'name_ar' => 'دمياط'],
            ['id' => 20, 'name_en' => 'Sharkia', 'name_ar' => 'الشرقية'],
            ['id' => 21, 'name_en' => 'South Sinai', 'name_ar' => 'جنوب سيناء'],
            ['id' => 22, 'name_en' => 'Kafr Al sheikh', 'name_ar' => 'كفر الشيخ'],
            ['id' => 23, 'name_en' => 'Matrouh', 'name_ar' => 'مطروح'],
            ['id' => 24, 'name_en' => 'Luxor', 'name_ar' => 'الأقصر'],
            ['id' => 25, 'name_en' => 'Qena', 'name_ar' => 'قنا'],
            ['id' => 26, 'name_en' => 'North Sinai', 'name_ar' => 'شمال سيناء'],
            ['id' => 27, 'name_en' => 'Sohag', 'name_ar' => 'سوهاج'],
        ];

        DB::table('provinces')->insert($provinces);
    }
}
