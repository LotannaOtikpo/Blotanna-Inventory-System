<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Admin User
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@blotanna.com',
            'password' => Hash::make('Freeplay@42'),
            'email_verified_at' => now(),
        ]);

        // 2. Create Categories
        $electronics = Category::create(['name' => 'Electronics']);
        $fashion = Category::create(['name' => 'Fashion']);
        $home = Category::create(['name' => 'Home & Living']);

        // 3. Create Sample Products
        Product::create([
            'name' => 'Wireless Noise-Canceling Headphones',
            'sku' => 'AUD-WH1000',
            'category_id' => $electronics->id,
            'price' => 299.99,
            'quantity' => 45,
            'description' => 'Premium wireless headphones with industry-leading noise cancellation.',
        ]);

        Product::create([
            'name' => 'Smart Watch Series 5',
            'sku' => 'WBL-SW500',
            'category_id' => $electronics->id,
            'price' => 399.00,
            'quantity' => 12,
            'description' => 'Advanced health monitoring and fitness tracking.',
        ]);

        Product::create([
            'name' => 'Cotton Crew Neck T-Shirt',
            'sku' => 'APP-TS001',
            'category_id' => $fashion->id,
            'price' => 24.50,
            'quantity' => 150,
            'description' => '100% organic cotton basic tee.',
        ]);

        Product::create([
            'name' => 'Ceramic Coffee Mug Set',
            'sku' => 'HOM-MG004',
            'category_id' => $home->id,
            'price' => 35.00,
            'quantity' => 8, // Low stock example
            'description' => 'Set of 4 minimalist ceramic mugs.',
        ]);

        // 4. Ensure Settings exist
        if (DB::table('settings')->count() === 0) {
            DB::table('settings')->insert([
                ['key' => 'business_name', 'value' => 'Blotanna Nig Ltd'],
                ['key' => 'low_stock_threshold', 'value' => '10'],
                ['key' => 'currency_symbol', 'value' => '$'],
            ]);
        }
    }
}