<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'product_id',
        'product_name',
        'product_price',
        'quantity',
        'color',
        'subtotal',
    ];

    protected $casts = [
        'product_price' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    /**
     * Relationship dengan order
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Relationship dengan product
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Calculate subtotal untuk item ini
     */
    public function calculateSubtotal(): float
    {
        return $this->product_price * $this->quantity;
    }
}
