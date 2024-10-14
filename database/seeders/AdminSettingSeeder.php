<?php

namespace Database\Seeders;

use App\Models\AdminSetting;
use Illuminate\Database\Seeder;

class AdminSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        AdminSetting::create([
            'version' => 1,
            'cost_bw_page' => 20,
            'cost_color_page' => 25,
            'cost_pixel_image' => 0.00005,
        ]);
    }
}
