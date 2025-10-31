<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EcommerceSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'countdown_enabled',
        'countdown_days',
        'countdown_hours',
        'countdown_minutes',
        'urgency_text',
        'background_color',
    ];

    protected $casts = [
        'countdown_enabled' => 'boolean',
        'countdown_days' => 'integer',
        'countdown_hours' => 'integer',
        'countdown_minutes' => 'integer',
    ];
}
