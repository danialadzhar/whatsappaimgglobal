<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Inertia\Inertia;

class EcommerceController extends Controller
{


    /**
     * API endpoint untuk mendapatkan detail produk
     */
    public function apiShow($id)
    {
        $product = Product::findOrFail($id);

        $productData = [
            'id' => $product->id,
            'name' => $product->name,
            'description' => $product->description,
            'price' => (float) $product->price,
            'original_price' => $product->original_price ? (float) $product->original_price : null,
            'image' => $product->image_url ?? 'https://placehold.co/300x300',
            'rating' => (float) $product->rating,
            'reviews' => (int) $product->reviews,
            'category' => $product->category,
            'colors' => $product->colors ?? [],
            'stock' => (int) $product->stock,
            'brand' => $product->brand,
            'sku' => $product->sku,
            'is_active' => (bool) $product->is_active,
            'specifications' => $product->specifications ?? [],
            'tags' => $product->tags,
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'product' => $productData,
            ],
            'message' => 'Product retrieved successfully'
        ]);
    }

    /**
     * API endpoint untuk mendapatkan senarai produk
     */
    public function apiIndex(Request $request)
    {
        $query = Product::query()->active();

        if ($request->filled('search')) {
            $query->search($request->input('search'));
        }

        if ($request->filled('category') && $request->input('category') !== 'all') {
            $query->category($request->input('category'));
        }

        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->input('min_price'));
        }

        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->input('max_price'));
        }

        switch ($request->input('sort_by')) {
            case 'price-low':
                $query->orderBy('price');
                break;
            case 'price-high':
                $query->orderByDesc('price');
                break;
            case 'rating':
                $query->orderByDesc('rating');
                break;
            case 'newest':
                $query->latest();
                break;
            default:
                $query->orderBy('name');
        }

        $products = $query->get()->map(function (Product $product) {
            $isNew = $product->created_at ? $product->created_at->greaterThanOrEqualTo(now()->subDays(30)) : false;

            return [
                'id' => $product->id,
                'name' => $product->name,
                'description' => $product->description,
                'price' => (float) $product->price,
                'original_price' => $product->original_price ? (float) $product->original_price : null,
                'image' => $product->image_url ?? 'https://placehold.co/300x300',
                'rating' => (float) $product->rating,
                'reviews' => (int) $product->reviews,
                'category' => $product->category,
                'colors' => $product->colors ?? [],
                'color' => $product->color,
                'storage' => $product->storage,
                'is_new' => $isNew,
                'stock' => (int) $product->stock,
                'brand' => $product->brand,
                'sku' => $product->sku,
                'is_active' => (bool) $product->is_active,
                // optional to help FE derive storage from specs when needed
                'specifications' => $product->specifications ?? [],
            ];
        })->values();

        // Filter berdasarkan parameter request
        $filters = [
            'search' => $request->input('search'),
            'category' => $request->input('category'),
            'min_price' => $request->input('min_price'),
            'max_price' => $request->input('max_price'),
            'sort_by' => $request->input('sort_by', 'featured'),
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'products' => $products,
                'filters' => $filters,
            ],
            'message' => 'Products retrieved successfully'
        ]);
    }

    // [Removed] Public ecommerce pages (Index, Show, Checkout) per request
}
