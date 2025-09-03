<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MessageLog extends Model
{
    /**
     * The attributes that are mass assignable.
     * Field yang boleh diisi secara mass assignment
     */
    protected $fillable = [
        'customer_messages',
        'ai_messages',
        'customer_id',
    ];

    /**
     * The attributes that should be cast.
     * Cast attributes untuk data type yang betul
     */
    protected $casts = [
        'customer_messages' => 'string',
        'ai_messages' => 'string',
        'customer_id' => 'integer',
    ];

    /**
     * Get the customer that owns the message log.
     * Dapatkan customer yang memiliki message log ini
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}
