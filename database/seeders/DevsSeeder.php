<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DevsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Admin::create([
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
