<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InventoryRestockValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_restock_quantity_uses_a_user_friendly_integer_message(): void
    {
        $user = User::factory()->create([
            'role' => 'admin',
            'is_approved' => true,
        ]);
        $product = Product::create([
            'sku' => 'TEST-001',
            'product_name' => 'Test Product',
            'category' => 'Test',
            'unit_price' => 10,
            'in_stock' => 5,
            'reorder_point' => 2,
            'status' => 'Available',
        ]);

        $this->actingAs($user)
            ->post(route('inventory.restock'), [
                'product_id' => $product->id,
                'quantity_added' => 1.5,
            ])
            ->assertSessionHasErrors([
                'quantity_added' => 'Please enter a whole number, like 1, 5, or 15. Do not use decimals.',
            ]);
    }

    public function test_oversized_restock_quantity_returns_a_friendly_error(): void
    {
        $user = User::factory()->create([
            'role' => 'admin',
            'is_approved' => true,
        ]);
        $product = Product::create([
            'sku' => 'TEST-002',
            'product_name' => 'Test Product',
            'category' => 'Test',
            'unit_price' => 10,
            'in_stock' => 5,
            'reorder_point' => 2,
            'status' => 'Available',
        ]);

        $this->actingAs($user)
            ->post(route('inventory.restock'), [
                'product_id' => $product->id,
                'quantity_added' => 999999999999,
            ])
            ->assertSessionHasErrors([
                'quantity_added' => 'That quantity is too large. Please enter a smaller number of items.',
            ]);

        $this->assertDatabaseCount('restocks', 0);
        $this->assertSame(5, $product->fresh()->in_stock);
    }

    public function test_restock_quantity_is_limited_by_the_products_current_stock(): void
    {
        $user = User::factory()->create([
            'role' => 'admin',
            'is_approved' => true,
        ]);
        $product = Product::create([
            'sku' => 'TEST-003',
            'product_name' => 'Nearly Full Product',
            'category' => 'Test',
            'unit_price' => 10,
            'in_stock' => 999995,
            'reorder_point' => 2,
            'status' => 'Available',
        ]);

        $this->actingAs($user)
            ->post(route('inventory.restock'), [
                'product_id' => $product->id,
                'quantity_added' => 10,
            ])
            ->assertSessionHasErrors([
                'quantity_added' => 'That delivery would make the total stock too large. You can add up to 5 items.',
            ]);

        $this->assertDatabaseCount('restocks', 0);
        $this->assertSame(999995, $product->fresh()->in_stock);
    }

    public function test_new_product_stock_cannot_exceed_one_million(): void
    {
        $user = User::factory()->create([
            'role' => 'admin',
            'is_approved' => true,
        ]);

        $this->actingAs($user)
            ->post(route('inventory.store'), [
                'sku' => 'TEST-004',
                'product_name' => 'Oversized Stock Product',
                'category' => 'Test',
                'unit_price' => 10,
                'in_stock' => 1000001,
                'reorder_point' => 2,
            ])
            ->assertSessionHasErrors([
                'in_stock' => 'Stock cannot be more than 1,000,000 units per product.',
            ]);

        $this->assertDatabaseMissing('products', ['sku' => 'TEST-004']);
    }
}
