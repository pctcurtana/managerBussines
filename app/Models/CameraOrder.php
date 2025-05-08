<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CameraOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'camera_name',
        'cost_price',
        'selling_price',
        'profit',
        'order_date',
        'is_sold'
    ];

    protected $casts = [
        'order_date' => 'date',
        'cost_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'profit' => 'decimal:2',
        'is_sold' => 'boolean'
    ];
} 