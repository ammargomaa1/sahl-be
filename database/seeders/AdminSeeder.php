<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Admin::create([
            'name' => 'Eyad Sheta',
            'email' => 'eyad_sheta@gmail.com',
            'password' => 'JAR2w2oVk3punEm',
        ],[
            'name' => 'Khaled Dev',
            'email' => 'khaledDev@gmail.com',
            'password' => 'JAR2w2oVk3punEmPPOOQQ',
        ],[
            'name' => 'Ammar Dev',
            'email' => 'AmmarDev@gmail.com',
            'password' => 'JAR2w2oVk3punEmRRPPOOQQ',
        ]);
    }
}
