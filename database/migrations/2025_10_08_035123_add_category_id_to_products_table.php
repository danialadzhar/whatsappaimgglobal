<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, add the category_id column
        Schema::table('products', function (Blueprint $table) {
            $table->unsignedBigInteger('category_id')->nullable()->after('category');
        });

        // Migrate existing category data to new categories table
        $existingCategories = DB::table('products')
            ->select('category')
            ->whereNotNull('category')
            ->where('category', '!=', '')
            ->distinct()
            ->pluck('category');

        foreach ($existingCategories as $categoryName) {
            $categoryId = DB::table('categories')->insertGetId([
                'name' => $categoryName,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Update products with this category
            DB::table('products')
                ->where('category', $categoryName)
                ->update(['category_id' => $categoryId]);
        }

        // Now drop the old category column
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('category');
        });

        // Add foreign key constraint
        Schema::table('products', function (Blueprint $table) {
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove foreign key constraint
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
        });

        // Add back the category column
        Schema::table('products', function (Blueprint $table) {
            $table->string('category')->nullable()->after('description');
        });

        // Migrate data back from categories to products
        $productsWithCategories = DB::table('products')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->select('products.id', 'categories.name as category_name')
            ->get();

        foreach ($productsWithCategories as $product) {
            DB::table('products')
                ->where('id', $product->id)
                ->update(['category' => $product->category_name]);
        }

        // Drop the category_id column
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('category_id');
        });
    }
};
