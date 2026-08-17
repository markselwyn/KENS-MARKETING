<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Report;
use App\Models\Sale;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportsQuarterTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_reached_quarters_are_available_and_store_exact_boundaries(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-08-17 10:00:00'));
        $admin = $this->admin();
        $expected = [
            'q1' => ['2026-01-01', '2026-03-31'],
            'q2' => ['2026-04-01', '2026-06-30'],
            'q3' => ['2026-07-01', '2026-09-30'],
        ];

        $this->actingAs($admin)
            ->get(route('reports'))
            ->assertOk()
            ->assertSee('value="q1"', false)
            ->assertSee('value="q2"', false)
            ->assertSee('value="q3"', false)
            ->assertDontSee('value="q4"', false);

        foreach ($expected as $timeframe => [$start, $end]) {
            $this->actingAs($admin)->post(route('reports.generate'), [
                'report_type' => 'sales_summary',
                'timeframe' => $timeframe,
                'format' => 'pdf',
            ])->assertRedirect();

            $report = Report::where('timeframe', $timeframe)->firstOrFail();
            $this->assertSame($start, $report->period_start->format('Y-m-d'));
            $this->assertSame($end, $report->period_end->format('Y-m-d'));
        }

        $this->actingAs($admin)->post(route('reports.generate'), [
            'report_type' => 'sales_summary',
            'timeframe' => 'q4',
            'format' => 'pdf',
        ])->assertSessionHasErrors('timeframe');

        $this->assertDatabaseMissing('reports', ['timeframe' => 'q4']);

        Carbon::setTestNow();
    }

    public function test_each_report_type_uses_the_correct_quarter_data(): void
    {
        $admin = $this->admin();
        $product = $this->product('QTR-001', 'Quarter Product');
        $unusedProduct = $this->product('QTR-002', 'No Sales Product');

        Sale::create([
            'product_id' => $product->id,
            'customer_name' => 'Q1 Customer',
            'quantity_sold' => 2,
            'total_amount' => 100,
            'created_at' => Carbon::parse('2026-03-31 23:59:59'),
            'updated_at' => Carbon::parse('2026-03-31 23:59:59'),
        ]);
        Sale::create([
            'product_id' => $product->id,
            'customer_name' => 'Q2 Customer',
            'quantity_sold' => 3,
            'total_amount' => 200,
            'created_at' => Carbon::parse('2026-04-01 00:00:00'),
            'updated_at' => Carbon::parse('2026-04-01 00:00:00'),
        ]);

        $salesReport = $this->report('sales_summary', 'q1', '2026-01-01', '2026-03-31');
        $salesData = $this->actingAs($admin)->get(route('reports.view', $salesReport))->viewData('data');
        $this->assertCount(1, $salesData);
        $this->assertSame('Q1 Customer', $salesData->first()->customer_name);

        $profitReport = $this->report('profit_margin', 'q2', '2026-04-01', '2026-06-30');
        $profitData = $this->actingAs($admin)->get(route('reports.view', $profitReport))->viewData('data');
        $this->assertCount(1, $profitData);
        $this->assertSame('Q2 Customer', $profitData->first()->customer_name);

        $movementReport = $this->report('fast_slow', 'q1', '2026-01-01', '2026-03-31');
        $movementData = $this->actingAs($admin)->get(route('reports.view', $movementReport))->viewData('data');
        $this->assertEquals(2, $movementData->firstWhere('product_name', 'Quarter Product')->total_qty);
        $this->assertEquals(0, $movementData->firstWhere('product_name', 'No Sales Product')->total_qty);

        $inventoryReport = $this->report('inventory_audit', 'q1', '2026-01-01', '2026-03-31');
        $inventoryData = $this->actingAs($admin)->get(route('reports.view', $inventoryReport))->viewData('data');
        $this->assertTrue($inventoryData->contains($product));
        $this->assertTrue($inventoryData->contains($unusedProduct));
    }

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin', 'is_approved' => true]);
    }

    private function product(string $sku, string $name): Product
    {
        return Product::create([
            'sku' => $sku,
            'product_name' => $name,
            'category' => 'Test',
            'unit_price' => 50,
            'in_stock' => 10,
            'reorder_point' => 2,
            'status' => 'Healthy',
        ]);
    }

    private function report(string $type, string $timeframe, string $start, string $end): Report
    {
        return Report::create([
            'report_name' => strtoupper($timeframe).' Test Report',
            'report_type' => $type,
            'timeframe' => $timeframe,
            'period_start' => Carbon::parse($start)->startOfDay(),
            'period_end' => Carbon::parse($end)->endOfDay(),
            'format' => 'pdf',
            'prepared_by' => 'Test Admin',
        ]);
    }
}
