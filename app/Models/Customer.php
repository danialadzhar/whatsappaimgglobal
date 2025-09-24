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
        'services_mode',
    ];

    /**
     * The attributes that should be cast.
     * Cast attributes untuk data types
     */
    protected $casts = [
        'services_mode' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get message logs for this customer
     * Dapatkan message logs untuk customer ini
     */
    public function messageLogs()
    {
        return $this->hasMany(MessageLog::class);
    }
}
