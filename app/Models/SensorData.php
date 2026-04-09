<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SensorData extends Model
{
    protected $fillable = ['biopond', 'harvest', 'temp', 'hum', 'soil', 'ammonia'];

    // Casting field biopond dari JSON ke Array otomatis
    protected $casts = [
        'biopond' => 'array',
    ];
}
