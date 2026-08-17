<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Sale;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SalesPeakTracerTest extends TestCase
{
    use RefreshDatabase;

    public function test_daily_and_weekly_peak_tracer_data_use_correct_time_buckets(): void
    {
        Carbon::setTestNow(Carbon::parse('2026-08-19 14:00:00'));

        $user = User::factory()->create(['role' => 'staff', 'is_approved' => true]);
        $product = Product::create([
            'sku' => 'PEAK-001',
            'product_name' => 'Peak Test Product',
            'category' => 'Test',
            'unit_price' => 10,
            'in_stock' => 100,
            'reorder_point' => 5,
            'status' => 'Healthy',
        ]);

        $weekStart = Carbon::now()->startOfWeek();
        foreach ([100, 200, 300, 400, 500, 600, 700] as $dayIndex => $amount) {
            Sale::create([
                'product_id' => $product->id,
                'customer_name' => 'Walk-in',
                'quantity_sold' => 1,
                'total_amount' => $amount,
                'created_at' => $weekStart->copy()->addDays($dayIndex)->setTime(12, 0),
                'updated_at' => $weekStart->copy()->addDays($dayIndex)->setTime(12, 0),
            ]);
        }

        $response = $this->actingAs($user)->get(route('sales.index'));

        $response->assertOk()
            ->assertViewHas('weeklyChartData', [100.0, 200.0, 300.0, 400.0, 500.0, 600.0, 700.0])
            ->assertViewHas('chartData', [0, 0, 300, 0, 0, 0])
            ->assertSee('id="dailyPeakButton"', false)
            ->assertSee('id="weeklyPeakButton"', false);

        Carbon::setTestNow();
    }

    public function test_sales_product_dropdown_has_complete_dark_mode_states(): void
    {
        $user = User::factory()->create([
            'role' => 'staff',
            'is_approved' => true,
            'preferences' => [
                'theme' => 'dark',
                'landing_page' => 'sales.index',
                'sidebar_state' => 'expanded',
                'reduced_motion' => false,
            ],
        ]);

        $this->actingAs($user)
            ->get(route('sales.index'))
            ->assertOk()
            ->assertSee('.dark .select2-container .select2-selection--single', false)
            ->assertSee('.dark .select2-dropdown', false)
            ->assertSee('.dark .select2-search--dropdown .select2-search__field', false)
            ->assertSee('.dark .select2-container--default .select2-results__option[aria-selected="true"]', false)
            ->assertSee('.dark .select2-container--default .select2-results__option--highlighted[aria-selected]', false);
    }
}
