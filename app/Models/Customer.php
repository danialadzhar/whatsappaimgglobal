<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    /**
     * The attributes that are mass assignable.
     * Field yang boleh diisi secara mass assignment
     */
    protected $fillable = [
        'name',
        'phone_number',
    ];

    /**
     * The attributes that should be cast.
     * Cast attributes untuk data types
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
