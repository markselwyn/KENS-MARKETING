<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            [
                'sku' => 'FUR-1001',
                'product_name' => 'L-Shape Sofa Set (Beige)',
                'category' => 'Furniture',
                'unit_price' => 24500.00,
                'in_stock' => 1,
            ],
            [
                'sku' => 'FUR-1004',
                'product_name' => 'Wooden Dining Set (6-Seater)',
                'category' => 'Furniture',
                'unit_price' => 18500.00,
                'in_stock' => 14,
            ],
            [
                'sku' => 'TV-5099',
                'product_name' => '43" Smart LED TV',
                'category' => 'Appliances',
                'unit_price' => 14400.00,
                'in_stock' => 3,
            ],
            [
                'sku' => 'FOM-2001',
                'product_name' => 'Queen Size Uratex Foam',
                'category' => 'Foams',
                'unit_price' => 5200.00,
                'in_stock' => 22,
            ],
            [
                'sku' => 'SPK-3001',
                'product_name' => 'Karaoke Speaker',
                'category' => 'Speakers',
                'unit_price' => 8500.00,
                'in_stock' => 8,
            ],
            [
                'sku' => 'ACC-4001',
                'product_name' => 'TV Wall Mount',
                'category' => 'Accessories',
                'unit_price' => 1200.00,
                'in_stock' => 15,
            ]
        ];

        foreach ($products as $product) {
            DB::table('products')->updateOrInsert(
                ['sku' => $product['sku']], // Avoids duplicate rows if run again
                [
                    'product_name' => $product['product_name'],
                    'category' => $product['category'],
                    'unit_price' => $product['unit_price'],
                    'in_stock' => $product['in_stock'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}