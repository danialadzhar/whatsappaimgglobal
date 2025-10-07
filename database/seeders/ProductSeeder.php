<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            [
                'name' => 'Wireless Earbuds, IPX8',
                'description' => 'Organic Cotton, fairtrade certified',
                'price' => 60.00,
                'original_price' => null,
                'image' => 'https://placehold.co/300x300',
                'rating' => 4.5,
                'reviews' => 738,
                'category' => 'Earbuds',
                'colors' => ['black', 'blue'],
                'stock' => 150,
                'is_active' => true,
                'brand' => 'TechSound',
                'sku' => 'PRD-EARB001',
                'specifications' => [
                    'battery_life' => '8 hours',
                    'water_resistance' => 'IPX8',
                    'connectivity' => 'Bluetooth 5.0'
                ],
                'tags' => 'wireless, waterproof, bluetooth',
            ],
            [
                'name' => 'AirPods Max',
                'description' => 'A perfect balance of high-fidelity audio',
                'price' => 2535.00,
                'original_price' => 2999.00,
                'image' => 'https://placehold.co/300x300',
                'rating' => 4.8,
                'reviews' => 121,
                'category' => 'Headphones',
                'colors' => ['silver', 'pink'],
                'stock' => 85,
                'is_active' => true,
                'brand' => 'Apple',
                'sku' => 'PRD-APM001',
                'specifications' => [
                    'battery_life' => '20 hours',
                    'noise_cancellation' => 'Active',
                    'connectivity' => 'Bluetooth 5.0'
                ],
                'tags' => 'premium, noise-cancelling, apple',
            ],
            [
                'name' => 'Bose BT Earphones',
                'description' => 'Table with air purifier, stained veneer/black',
                'price' => 2535.00,
                'original_price' => null,
                'image' => 'https://placehold.co/300x300',
                'rating' => 4.7,
                'reviews' => 801,
                'category' => 'Earphones',
                'colors' => ['black'],
                'stock' => 45,
                'is_active' => true,
                'brand' => 'Bose',
                'sku' => 'PRD-BOSE001',
                'specifications' => [
                    'battery_life' => '6 hours',
                    'water_resistance' => 'IPX4',
                    'connectivity' => 'Bluetooth 5.1'
                ],
                'tags' => 'bose, premium, wireless',
            ],
            [
                'name' => 'UNEYFOX Headphones',
                'description' => 'Wired folded headphones with mic',
                'price' => 135.00,
                'original_price' => null,
                'image' => 'https://placehold.co/300x300',
                'rating' => 4.2,
                'reviews' => 94,
                'category' => 'Headphones',
                'colors' => ['red'],
                'stock' => 5,
                'is_active' => true,
                'brand' => 'UNEYFOX',
                'sku' => 'PRD-UNF001',
                'specifications' => [
                    'type' => 'Wired',
                    'foldable' => true,
                    'microphone' => true
                ],
                'tags' => 'wired, foldable, budget',
            ],
            [
                'name' => 'JBL TUNE 500BT/NC',
                'description' => 'On-Ear Wireless Headphones with Purebass Sound',
                'price' => 49.00,
                'original_price' => null,
                'image' => 'https://placehold.co/300x300',
                'rating' => 4.6,
                'reviews' => 556,
                'category' => 'Headphones',
                'colors' => ['black'],
                'stock' => 200,
                'is_active' => true,
                'brand' => 'JBL',
                'sku' => 'PRD-JBL001',
                'specifications' => [
                    'battery_life' => '16 hours',
                    'type' => 'On-Ear',
                    'connectivity' => 'Bluetooth 5.0'
                ],
                'tags' => 'jbl, bass, wireless',
            ],
            [
                'name' => 'TAOTV Bluetooth',
                'description' => 'TAOTV M41 IPX7 Waterproof Earbuds',
                'price' => 109.00,
                'original_price' => null,
                'image' => 'https://placehold.co/300x300',
                'rating' => 4.3,
                'reviews' => 243,
                'category' => 'Earbuds',
                'colors' => ['black', 'blue', 'cyan'],
                'stock' => 120,
                'is_active' => false,
                'brand' => 'TAOTV',
                'sku' => 'PRD-TTV001',
                'specifications' => [
                    'battery_life' => '5 hours',
                    'water_resistance' => 'IPX7',
                    'connectivity' => 'Bluetooth 5.0'
                ],
                'tags' => 'waterproof, sports, budget',
            ],
            [
                'name' => 'Monster MNPLEX',
                'description' => 'Monster MNPLEX Wireless Earphones',
                'price' => 69.00,
                'original_price' => null,
                'image' => 'https://placehold.co/300x300',
                'rating' => 4.4,
                'reviews' => 187,
                'category' => 'Earphones',
                'colors' => ['black'],
                'stock' => 75,
                'is_active' => true,
                'brand' => 'Monster',
                'sku' => 'PRD-MON001',
                'specifications' => [
                    'battery_life' => '7 hours',
                    'water_resistance' => 'IPX5',
                    'connectivity' => 'Bluetooth 5.0'
                ],
                'tags' => 'monster, wireless, sports',
            ],
            [
                'name' => 'Mpow CH6',
                'description' => 'Kids Headphones with Microphone',
                'price' => 660.00,
                'original_price' => null,
                'image' => 'https://placehold.co/300x300',
                'rating' => 4.9,
                'reviews' => 892,
                'category' => 'Headphones',
                'colors' => ['blue'],
                'stock' => 30,
                'is_active' => true,
                'brand' => 'Mpow',
                'sku' => 'PRD-MPW001',
                'specifications' => [
                    'type' => 'Kids',
                    'volume_limit' => '85dB',
                    'microphone' => true
                ],
                'tags' => 'kids, safe, volume-limited',
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
