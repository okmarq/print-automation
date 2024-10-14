<?php

namespace Database\Seeders;

use App\Models\AdminSetting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            AdminSettingSeeder::class,
            RoleSeeder::class,
        ]);
        // User::factory(10)->create();
        $user = User::factory()->create([
            'firstname' => 'AdminF',
            'lastname' => 'AdminL',
            'email' => 'test@example.com',
            'password' => Hash::make('password')
        ]);
        $user->attachRole(config('constants.role.admin'));
    }
}
