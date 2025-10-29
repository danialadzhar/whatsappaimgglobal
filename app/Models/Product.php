<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'category_id',
        'price',
        'normal_price',
        'original_price',
        'image',
        'rating',
        'reviews',
        'colors',
        'color',
        'storage',
        'stock',
        'is_active',
        'sku',
        'brand',
        'specifications',
        'tags',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'colors' => 'array',
        'specifications' => 'array',
        'is_active' => 'boolean',
        'price' => 'decimal:2',
        'normal_price' => 'decimal:2',
        'original_price' => 'decimal:2',
        'rating' => 'decimal:2',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['image_url'];

    /**
     * Scope a query to only include active products.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to filter by category.
     */
    public function scopeCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    /**
     * Get the category that owns the product.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Scope a query to search products by name or description.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%")
                ->orWhere('brand', 'like', "%{$search}%");
        });
    }

    /**
     * Check if product is in stock.
     */
    public function isInStock()
    {
        return $this->stock > 0;
    }

    /**
     * Check if product is on sale.
     */
    public function isOnSale()
    {
        return $this->original_price && $this->original_price > $this->price;
    }

    /**
     * Get the discount percentage.
     */
    public function getDiscountPercentage()
    {
        if (!$this->isOnSale()) {
            return 0;
        }

        return \round((($this->original_price - $this->price) / $this->original_price) * 100);
    }

    /**
     * Get the image URL.
     */
    public function getImageUrlAttribute()
    {
        if ($this->image) {
            // If it's already a full URL (like placeholder URLs), return as is
            if (filter_var($this->image, FILTER_VALIDATE_URL)) {
                return $this->image;
            }
            // If it's a storage path, generate the full URL
            return url('storage/' . $this->image);
        }
        return null;
    }
}
