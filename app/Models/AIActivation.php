<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class AIActivation extends Model
{
    /**
     * The table associated with the model.
     * Nama table yang berkaitan dengan model
     */
    protected $table = 'ai_activations';

    /**
     * The attributes that are mass assignable.
     * Field yang boleh diisi secara mass assignment
     */
    protected $fillable = [
        'is_active',
        'name',
        'description',
        'last_updated_at',
    ];

    /**
     * The attributes that should be cast.
     * Field yang perlu di-cast kepada type tertentu
     */
    protected $casts = [
        'is_active' => 'boolean',
        'last_updated_at' => 'datetime',
    ];

    /**
     * Get chatbot activation status
     * Dapatkan status aktivasi chatbot
     */
    public static function getChatbotStatus(): bool
    {
        $activation = self::where('name', 'chatbot')->first();

        if (!$activation) {
            // Create default record if not exists
            // Cipta record default jika tidak wujud
            $activation = self::create([
                'name' => 'chatbot',
                'is_active' => true,
                'description' => 'Main chatbot AI activation status',
                'last_updated_at' => Carbon::now(),
            ]);
        }

        return $activation->is_active;
    }

    /**
     * Toggle chatbot activation status
     * Toggle status aktivasi chatbot
     */
    public static function toggleChatbotStatus(bool $status): self
    {
        $activation = self::where('name', 'chatbot')->first();

        if (!$activation) {
            // Create new record if not exists
            // Cipta record baru jika tidak wujud
            $activation = self::create([
                'name' => 'chatbot',
                'is_active' => $status,
                'description' => 'Main chatbot AI activation status',
                'last_updated_at' => Carbon::now(),
            ]);
        } else {
            // Update existing record
            // Kemas kini record yang sedia ada
            $activation->update([
                'is_active' => $status,
                'last_updated_at' => Carbon::now(),
            ]);
        }

        return $activation;
    }

    /**
     * Scope untuk filter by active status
     * Scope untuk tapis mengikut status aktif
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope untuk filter by name
     * Scope untuk tapis mengikut nama
     */
    public function scopeByName($query, $name)
    {
        return $query->where('name', $name);
    }
}
