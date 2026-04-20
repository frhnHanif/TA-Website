<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeviceControl extends Model
{
    use HasFactory;

    // Mengizinkan pengisian data massal untuk 2 kolom ini
    protected $fillable = [
        'is_manual',
        'mist',
        'fan',
    ];

    // Memberi tahu Laravel bahwa 'mist' harus diperlakukan sebagai Array
    protected $casts = [
        'mist' => 'array',
        'is_manual' => 'boolean',
    ];
}