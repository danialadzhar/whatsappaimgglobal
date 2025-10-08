<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class ProductController extends Controller
{
    /**
     * Display product management page
     */
    public function index(Request $request)
    {
        $showArchived = $request->boolean('show_archived', false);

        $query = $showArchived ? Product::onlyTrashed() : Product::query();

        // Apply search filter
        if ($request->filled('search')) {
            $query->search($request->search);
        }

        // Apply category filter
        if ($request->filled('category')) {
            $query->category($request->category);
        }

        // Apply sorting
        $sortBy = $request->get('sort_by', 'name');
        switch ($sortBy) {
            case 'price':
                $query->orderBy('price');
                break;
            case 'date':
                $query->orderBy('created_at', 'desc');
                break;
            default:
                $query->orderBy('name');
                break;
        }

        $products = $query->paginate(10);

        // Add image_url to each product
        $products->getCollection()->transform(function ($product) {
            $product->image_url = $product->image_url;
            return $product;
        });

        return Inertia::render('Products/Index', [
            'products' => $products,
            'filters' => $request->only(['search', 'category', 'sort_by', 'show_archived']),
            'showArchived' => $showArchived,
        ]);
    }

    /**
     * Store a new product
     */
    public function store(Request $request)
    {
        // Validate request
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'is_active' => 'boolean',
            'brand' => 'nullable|string|max:255',
            'sku' => 'nullable|string|max:255|unique:products,sku',
            'original_price' => 'nullable|numeric|min:0',
            'colors' => 'nullable|array',
            'specifications' => 'nullable|array',
            'tags' => 'nullable|string',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Check if product_images folder exists, create if not
            $productImagesPath = storage_path('app/public/product_images');
            if (!file_exists($productImagesPath)) {
                mkdir($productImagesPath, 0755, true);
            }

            $image = $request->file('image');
            $imageName = \time() . '_' . $image->getClientOriginalName();
            $imagePath = $image->storeAs('product_images', $imageName, 'public');
            $validated['image'] = $imagePath;
        }

        // Generate SKU if not provided
        if (empty($validated['sku'])) {
            $validated['sku'] = 'PRD-' . \strtoupper(\substr(\md5($validated['name'] . \time()), 0, 8));
        }

        Product::create($validated);

        return redirect()->route('products.index')->with('success', 'Product created successfully');
    }

    /**
     * Update product
     */
    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        // Validate request
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'is_active' => 'boolean',
            'brand' => 'nullable|string|max:255',
            'sku' => 'nullable|string|max:255|unique:products,sku,' . $id,
            'original_price' => 'nullable|numeric|min:0',
            'colors' => 'nullable|array',
            'specifications' => 'nullable|array',
            'tags' => 'nullable|string',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Check if product_images folder exists, create if not
            $productImagesPath = storage_path('app/public/product_images');
            if (!file_exists($productImagesPath)) {
                mkdir($productImagesPath, 0755, true);
            }

            // Delete old image if exists
            if ($product->image && Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }

            $image = $request->file('image');
            $imageName = \time() . '_' . $image->getClientOriginalName();
            $imagePath = $image->storeAs('product_images', $imageName, 'public');
            $validated['image'] = $imagePath;
        }

        $product->update($validated);

        return redirect()->route('products.index')->with('success', 'Product updated successfully');
    }

    /**
     * Soft delete product (archive)
     */
    public function destroy($id)
    {
        $product = Product::findOrFail($id);

        // Soft delete the product (archive it)
        $product->delete();

        return redirect()->route('products.index')->with('success', 'Product archived successfully');
    }

    /**
     * Restore archived product
     */
    public function restore($id)
    {
        $product = Product::withTrashed()->findOrFail($id);
        $product->restore();

        return redirect()->route('products.index')->with('success', 'Product restored successfully');
    }

    /**
     * Force delete product (permanent delete)
     */
    public function forceDelete($id)
    {
        $product = Product::withTrashed()->findOrFail($id);

        // Check if product has orders
        $hasOrders = \App\Models\OrderItem::where('product_id', $id)->exists();

        if ($hasOrders) {
            return redirect()->route('products.index')
                ->with('error', 'Cannot permanently delete product. Product has existing orders. Consider archiving instead.');
        }

        // Delete image file if exists
        if ($product->image && Storage::disk('public')->exists($product->image)) {
            Storage::disk('public')->delete($product->image);
        }

        $product->forceDelete();

        return redirect()->route('products.index')->with('success', 'Product permanently deleted');
    }
}
