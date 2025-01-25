<?php

// app/Models/SensorData.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SensorData extends Model
{
    use HasFactory;

    protected $fillable = [
        'gas_value',  // Pastikan kolom yang ingin disimpan ada di sini
    ];
}
