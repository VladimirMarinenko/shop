<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            [
                'email' => 'admin@shop.test',
            ],
            [
                'name' => 'Администратор',
                'password' => bcrypt('Roman1123!'),
                'role' => 'admin',
            ]
        );
    }
}
