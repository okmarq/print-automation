<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'version',
        'cost_bw_page',
        'cost_color_page',
        'cost_pixel_image',
        'is_preferred'
    ];
}
