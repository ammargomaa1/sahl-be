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
            'name' => 'Sahl Admin',
            'email' => 'info.sahlonline@gmail.com',
            'password' => 'Sahl12345',
        ]);

        Admin::create([
            'name' => 'Ammar Gomaa Dev',
            'email' => 'ammargomaa1@gmail.com',
            'password' => 'AmmarGomaa97',
        ]);
    }
}
