<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema; 
use App\Models\Transaction;
use App\Models\Product; 
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ==========================================
        // 1. WIPE OLD DATA CLEAN
        // ==========================================
        Schema::disableForeignKeyConstraints();

        User::truncate();         // Wipes the old admin@kensmarketing.com
        Transaction::truncate();  // Wipes old transactions
        Product::truncate();      // Wipes old products

        Schema::enableForeignKeyConstraints();
        // ==========================================


        // ==========================================
        // 2. CREATE YOUR REAL SECURE ADMIN
        // ==========================================
        User::create([
            'name' => 'System Admin',
            'email' => 'mselwynpesino@gmail.com', // <-- PUT YOUR REAL EMAIL HERE
            'password' => Hash::make('admin123'), // <-- PUT YOUR REAL PASSWORD HERE
            'role' => 'admin'
        ]);


        // ==========================================
        // 3. SEED FRESH PRODUCTS & TRANSACTIONS
        // ==========================================
        Product::insert([
            ['sku' => 'FURN-001', 'product_name' => 'L-Shape Sofa Set', 'category' => 'Furniture', 'unit_price' => 35000.00, 'in_stock' => 1, 'reorder_point' => 3, 'status' => 'Low Stock', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['sku' => 'FOAM-001', 'product_name' => 'Queen Size Uratex Foam', 'category' => 'Foams', 'unit_price' => 8500.00, 'in_stock' => 2, 'reorder_point' => 5, 'status' => 'Low Stock', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['sku' => 'APPL-001', 'product_name' => '43" Smart TV', 'category' => 'Appliances', 'unit_price' => 18000.00, 'in_stock' => 10, 'reorder_point' => 5, 'status' => 'Healthy', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['sku' => 'FURN-002', 'product_name' => 'Dining Table (6-Seater)', 'category' => 'Furniture', 'unit_price' => 12500.00, 'in_stock' => 4, 'reorder_point' => 4, 'status' => 'Low Stock', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['sku' => 'SPKR-001', 'product_name' => 'Sony Home Theater System', 'category' => 'Speakers', 'unit_price' => 15000.00, 'in_stock' => 8, 'reorder_point' => 3, 'status' => 'Healthy', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['sku' => 'APPL-002', 'product_name' => 'Inverter Refrigerator', 'category' => 'Appliances', 'unit_price' => 28000.00, 'in_stock' => 15, 'reorder_point' => 5, 'status' => 'Healthy', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['sku' => 'SPKR-002', 'product_name' => 'JBL Bluetooth Speaker', 'category' => 'Speakers', 'unit_price' => 4500.00, 'in_stock' => 20, 'reorder_point' => 10, 'status' => 'Healthy', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
        ]);

        Transaction::insert([
            ['transaction_code' => 'TRX-0092', 'customer_name' => 'Villanueva Residence', 'location' => 'Centro, Polangui', 'amount' => 12450.00, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['transaction_code' => 'TRX-0091', 'customer_name' => 'Walk-in Customer', 'location' => 'In-Store Purchase', 'amount' => 8500.00, 'created_at' => Carbon::now()->subHours(2), 'updated_at' => Carbon::now()->subHours(2)],
            ['transaction_code' => 'TRX-0090', 'customer_name' => 'Oas Restobar', 'location' => 'Oas, Albay', 'amount' => 45200.00, 'created_at' => Carbon::now()->subDays(1), 'updated_at' => Carbon::now()->subDays(1)],
            ['transaction_code' => 'TRX-0089', 'customer_name' => 'Ligao City Hall', 'location' => 'Ligao City, Albay', 'amount' => 25500.00, 'created_at' => Carbon::now()->subDays(3), 'updated_at' => Carbon::now()->subDays(3)],
            ['transaction_code' => 'TRX-0088', 'customer_name' => 'Libon Tech Hub', 'location' => 'Libon, Albay', 'amount' => 18000.00, 'created_at' => Carbon::now()->subDays(5), 'updated_at' => Carbon::now()->subDays(5)],
        ]);
        
        $this->command->info('Database wiped and securely re-seeded!');
    }
}