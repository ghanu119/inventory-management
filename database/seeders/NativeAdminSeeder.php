<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class NativeAdminSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@windows.app'],
            [
                'name' => 'Inventory System',
                'password' => Hash::make('Strong@123'),
            ]
        );
    }
}
