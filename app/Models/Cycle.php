<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Cycle extends Model
{
    use HasFactory;

    protected $fillable = [
        'batch_id', 
        'start_date', 
        'end_date', 
        'initial_seed_mass',
        'total_waste_input', 
        'harvest_mass', 
        'residue_mass',
        'wri_result', 
        'eci_result', 
        'status'
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    // Accessor khusus untuk menghitung sudah berapa hari siklus berjalan
    public function getDaysElapsedAttribute()
    {
        $end = $this->end_date ? $this->end_date : now();
        // Pakai max(1, ...) agar jika baru mulai (hari ke-0), perhitungan WRI tidak dibagi 0
        return max(1, (int) $this->start_date->diffInDays($end));
    }
}