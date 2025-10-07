<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class EcommerceController extends Controller
{
    /**
     * Paparkan halaman utama e-commerce dengan senarai produk
     */
    public function index(Request $request)
    {
        // Sample data produk - nanti boleh ganti dengan data dari database
        $products = [
            [
                'id' => 1,
                'name' => 'Wireless Earbuds, IPX8',
                'description' => 'Organic Cotton, fairtrade certified',
                'price' => 60,
                'original_price' => null,
                'image' => 'https://placehold.co/300x300',
                'rating' => 4.5,
                'reviews' => 738,
                'category' => 'Earbuds',
                'colors' => ['black', 'blue'],
                'is_new' => false,
            ],
            [
                'id' => 2,
                'name' => 'AirPods Max',
                'description' => 'A perfect balance of high-fidelity audio',
                'price' => 2535,
                'original_price' => 2999,
                'image' => 'https://placehold.co/300x300',
                'rating' => 4.8,
                'reviews' => 121,
                'category' => 'Headphones',
                'colors' => ['silver', 'pink'],
                'is_new' => false,
            ],
            [
                'id' => 3,
                'name' => 'Bose BT Earphones',
                'description' => 'Table with air purifier, stained veneer/black',
                'price' => 2535,
                'original_price' => null,
                'image' => 'https://placehold.co/300x300',
                'rating' => 4.7,
                'reviews' => 801,
                'category' => 'Earphones',
                'colors' => ['black'],
                'is_new' => false,
            ],
            [
                'id' => 4,
                'name' => 'UNEYFOX Headphones',
                'description' => 'Wired folded headphones with mic',
                'price' => 135,
                'original_price' => null,
                'image' => 'https://placehold.co/300x300',
                'rating' => 4.2,
                'reviews' => 94,
                'category' => 'Headphones',
                'colors' => ['red'],
                'is_new' => false,
            ],
            [
                'id' => 5,
                'name' => 'JBL TUNE 500BT/NC',
                'description' => 'On-Ear Wireless Headphones with Purebass Sound',
                'price' => 49,
                'original_price' => null,
                'image' => 'https://placehold.co/300x300',
                'rating' => 4.6,
                'reviews' => 556,
                'category' => 'Headphones',
                'colors' => ['black'],
                'is_new' => false,
            ],
            [
                'id' => 6,
                'name' => 'TAOTV Bluetooth',
                'description' => 'TAOTV M41 IPX7 Waterproof Earbuds',
                'price' => 109,
                'original_price' => null,
                'image' => 'https://placehold.co/300x300',
                'rating' => 4.3,
                'reviews' => 243,
                'category' => 'Earbuds',
                'colors' => ['black', 'blue', 'cyan'],
                'is_new' => false,
            ],
            [
                'id' => 7,
                'name' => 'Monster MNPLEX',
                'description' => 'Monster MNPLEX Wireless Earphones',
                'price' => 69,
                'original_price' => null,
                'image' => 'https://placehold.co/300x300',
                'rating' => 4.4,
                'reviews' => 187,
                'category' => 'Earphones',
                'colors' => ['black'],
                'is_new' => false,
            ],
            [
                'id' => 8,
                'name' => 'Mpow CH6',
                'description' => 'Kids Headphones with Microphone',
                'price' => 660,
                'original_price' => null,
                'image' => 'https://placehold.co/300x300',
                'rating' => 4.9,
                'reviews' => 892,
                'category' => 'Headphones',
                'colors' => ['blue'],
                'is_new' => false,
            ],
        ];

        // Filter berdasarkan parameter request
        $filters = [
            'search' => $request->input('search'),
            'category' => $request->input('category'),
            'min_price' => $request->input('min_price'),
            'max_price' => $request->input('max_price'),
            'sort_by' => $request->input('sort_by', 'featured'),
        ];

        return Inertia::render('Ecommerce/Index', [
            'products' => $products,
            'filters' => $filters,
        ]);
    }

    /**
     * Paparkan halaman detail produk
     */
    public function show($id)
    {
        // Sample data - nanti boleh ganti dengan data dari database
        $product = [
            'id' => $id,
            'name' => 'Wireless Earbuds, IPX8',
            'description' => 'Organic Cotton, fairtrade certified',
            'price' => 60,
            'original_price' => null,
            'image' => 'https://placehold.co/300x300',
            'rating' => 4.5,
            'reviews' => 738,
            'category' => 'Earbuds',
            'colors' => ['black', 'blue'],
        ];

        return Inertia::render('Ecommerce/Show', [
            'product' => $product,
        ]);
    }
}
