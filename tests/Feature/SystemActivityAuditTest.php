<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Activitylog\Models\Activity;
use Tests\TestCase;

class SystemActivityAuditTest extends TestCase
{
    use RefreshDatabase;

    public function test_staff_sale_records_the_actor_and_specific_transaction_details(): void
    {
        $staff = User::factory()->create([
            'name' => 'Sales Staff',
            'role' => 'staff',
            'is_approved' => true,
        ]);
        $product = $this->product();

        $this->actingAs($staff)->post(route('sales.store'), [
            'product_id' => $product->id,
            'quantity_sold' => 2,
            'customer_name' => 'Maria Cruz',
        ])->assertRedirect();

        $activity = Activity::where('description', 'like', 'Recorded sale RC-%')->sole();

        $this->assertTrue($activity->causer->is($staff));
        $this->assertSame('Sales', $activity->properties->get('module'));
        $this->assertSame('staff', $activity->properties->get('actor_role'));
        $this->assertStringContainsString('2 x Audit Product (AUD-001)', $activity->description);
        $this->assertStringContainsString('customer Maria Cruz', $activity->description);
        $this->assertStringContainsString('8 units remaining', $activity->description);
    }

    public function test_admin_inventory_and_report_actions_have_specific_audit_entries(): void
    {
        $admin = User::factory()->create([
            'name' => 'System Admin',
            'role' => 'admin',
            'is_approved' => true,
        ]);
        $product = $this->product();

        $this->actingAs($admin)->post(route('inventory.restock'), [
            'product_id' => $product->id,
            'quantity_added' => 5,
            'supplier' => 'North Supplier',
            'reference_no' => 'DR-1001',
        ])->assertRedirect();

        $this->actingAs($admin)->post(route('reports.generate'), [
            'report_type' => 'inventory_audit',
            'timeframe' => 'this_month',
            'format' => 'pdf',
        ])->assertRedirect();

        $restock = Activity::where('description', 'like', 'Restocked Audit Product%')->sole();
        $report = Activity::where('description', 'like', 'Generated pdf report%')->sole();

        $this->assertSame('admin', $restock->properties->get('actor_role'));
        $this->assertStringContainsString('stock 10 -> 15', $restock->description);
        $this->assertStringContainsString('supplier North Supplier', $restock->description);
        $this->assertStringContainsString('reference DR-1001', $restock->description);
        $this->assertSame('Reports', $report->properties->get('module'));
        $this->assertTrue($report->causer->is($admin));
        $this->assertDatabaseCount('reports', 1);
    }

    public function test_security_hub_shows_actor_role_module_and_detailed_action(): void
    {
        $admin = User::factory()->create([
            'name' => 'Viewing Admin',
            'role' => 'admin',
            'is_approved' => true,
        ]);
        $product = $this->product();

        $this->actingAs($admin)->post(route('sales.store'), [
            'product_id' => $product->id,
            'quantity_sold' => 1,
            'customer_name' => 'Walk-in',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.security'))
            ->assertOk()
            ->assertSee('Viewing Admin')
            ->assertSee('admin')
            ->assertSee('Sales')
            ->assertSee('Recorded sale RC-');
    }

    public function test_security_hub_filters_activity_by_search_role_module_and_period(): void
    {
        $admin = User::factory()->create([
            'name' => 'Filter Admin',
            'email' => 'filter.admin@example.com',
            'role' => 'admin',
            'is_approved' => true,
        ]);
        $staff = User::factory()->create([
            'name' => 'Filter Staff',
            'email' => 'filter.staff@example.com',
            'role' => 'staff',
            'is_approved' => true,
        ]);

        activity('system')
            ->causedBy($staff)
            ->withProperties(['module' => 'Sales', 'actor_name' => $staff->name, 'actor_role' => 'staff'])
            ->log('Recorded unique customer sale.');

        $oldInventoryActivity = activity('system')
            ->causedBy($admin)
            ->withProperties(['module' => 'Inventory', 'actor_name' => $admin->name, 'actor_role' => 'admin'])
            ->log('Updated archived inventory item.');
        $oldInventoryActivity->forceFill(['created_at' => now()->subDays(45)])->save();

        $this->actingAs($admin)
            ->get(route('admin.security', ['role' => 'staff']))
            ->assertOk()
            ->assertSee('Recorded unique customer sale.')
            ->assertDontSee('Updated archived inventory item.');

        $this->actingAs($admin)
            ->get(route('admin.security', ['module' => 'Inventory']))
            ->assertOk()
            ->assertSee('Updated archived inventory item.')
            ->assertDontSee('Recorded unique customer sale.');

        $this->actingAs($admin)
            ->get(route('admin.security', ['search' => 'unique customer']))
            ->assertOk()
            ->assertSee('Recorded unique customer sale.')
            ->assertDontSee('Updated archived inventory item.');

        $this->actingAs($admin)
            ->get(route('admin.security', ['search' => 'Inventory']))
            ->assertOk()
            ->assertSee('Updated archived inventory item.')
            ->assertDontSee('Recorded unique customer sale.');

        $this->actingAs($admin)
            ->get(route('admin.security', ['period' => '30_days']))
            ->assertOk()
            ->assertSee('Recorded unique customer sale.')
            ->assertDontSee('Updated archived inventory item.');
    }

    public function test_security_hub_filters_submit_automatically_without_an_apply_button(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'is_approved' => true,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.security'))
            ->assertOk()
            ->assertSee('id="auditFilterForm"', false)
            ->assertSee("select.addEventListener('change'", false)
            ->assertSee("auditSearch?.addEventListener('input'", false)
            ->assertSee('Filters update automatically.')
            ->assertDontSee('Apply Filters');
    }

    public function test_account_management_card_is_sticky_on_desktop(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'is_approved' => true,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.security'))
            ->assertOk()
            ->assertSee('class="lg:col-span-1 lg:sticky lg:top-6 lg:self-start"', false);
    }

    private function product(): Product
    {
        return Product::create([
            'sku' => 'AUD-001',
            'product_name' => 'Audit Product',
            'category' => 'Test',
            'unit_price' => 25,
            'in_stock' => 10,
            'reorder_point' => 2,
            'status' => 'Available',
        ]);
    }
}
