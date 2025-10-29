<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Color extends Model
{
    use HasFactory;

    /**
     * Medan yang boleh diisi secara mass-assignment.
     */
    protected $fillable = [
        'name',
    ];
}
