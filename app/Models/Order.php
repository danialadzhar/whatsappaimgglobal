<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'order_number',
        'customer_name',
        'customer_phone',
        'customer_email',
        'address_line1',
        'address_line2',
        'city',
        'state',
        'postcode',
        'delivery_method',
        'payment_method',
        'subtotal',
        'delivery_discount',
        'payment_discount',
        'total_amount',
        'status',
        'notes',
        // Billplz payment fields
        'billplz_bill_id',
        'billplz_collection_id',
        'payment_status',
        'paid_at',
        'idempotency_key',
        'payment_metadata',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'delivery_discount' => 'decimal:2',
        'payment_discount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'paid_at' => 'datetime',
        'payment_metadata' => 'array',
    ];

    /**
     * Relationship dengan order items
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Scope untuk order pending
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope untuk order completed
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope untuk filter by status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Generate order number yang unique
     */
    public static function generateOrderNumber(): string
    {
        $prefix = 'ORD-' . date('Ymd') . '-';
        $lastOrder = self::where('order_number', 'like', $prefix . '%')
            ->orderBy('order_number', 'desc')
            ->first();

        if ($lastOrder) {
            $lastNumber = (int) substr($lastOrder->order_number, -3);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Calculate total amount
     */
    public function calculateTotal(): float
    {
        return $this->subtotal - $this->delivery_discount - $this->payment_discount;
    }

    /**
     * Update order status
     */
    public function updateStatus(string $status): bool
    {
        $validStatuses = ['pending', 'processing', 'completed', 'cancelled'];

        if (!in_array($status, $validStatuses)) {
            return false;
        }

        $this->status = $status;
        return $this->save();
    }
}
