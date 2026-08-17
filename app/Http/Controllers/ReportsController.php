<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\Report;
use App\Models\Product; 
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Support\SystemAudit;

class ReportsController extends Controller
{
    public function index()
    {
        $labels = [];
        $revenueData = [];
        $profitData = [];

        // ==========================================
        // 1. 6-MONTH CHART DATA (Updated to calculate exact profit)
        // ==========================================
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::today()->startOfMonth()->subMonths($i);
            $label = $month->format('F');
            if ($i == 0) $label .= ' (Current)';
            $labels[] = $label;

            // Get exact revenue for the month
            $monthlyRevenue = Sale::whereYear('created_at', $month->year)
                                  ->whereMonth('created_at', $month->month)
                                  ->sum('total_amount');
            
            // Get exact profit for the month by subtracting the 70% baseline supplier cost
            $monthlyProfit = DB::table('sales')
                ->join('products', 'sales.product_id', '=', 'products.id')
                ->whereYear('sales.created_at', $month->year)
                ->whereMonth('sales.created_at', $month->month)
                ->select(DB::raw('SUM(sales.total_amount - (products.unit_price * 0.70 * sales.quantity_sold)) as exact_profit'))
                ->value('exact_profit') ?: 0;

            $revenueData[] = $monthlyRevenue;
            $profitData[] = $monthlyProfit; 
        }

        // ==========================================
        // 2. DSS MACRO ANALYSIS: STAGNANT CAPITAL
        // ==========================================
        $stagnantProduct = DB::table('products')
            ->leftJoin('sales', function($join) {
                $join->on('products.id', '=', 'sales.product_id')
                     ->where('sales.created_at', '>=', now()->subDays(45));
            })
            ->select('products.product_name', 'products.in_stock', 'products.unit_price', DB::raw('COALESCE(SUM(sales.quantity_sold), 0) as recent_sales'))
            ->groupBy('products.id', 'products.product_name', 'products.in_stock', 'products.unit_price')
            ->havingRaw('recent_sales = 0')
            ->where('products.in_stock', '>', 0)
            ->orderByRaw('(products.in_stock * products.unit_price) DESC')
            ->first();

        // ==========================================
        // 3. DSS MACRO ANALYSIS: REVENUE TARGET MET
        // ==========================================
        $thisMonthSales = Sale::whereYear('created_at', now()->year)->whereMonth('created_at', now()->month)->sum('total_amount');
        $lastMonthSales = Sale::whereYear('created_at', now()->subMonth()->year)->whereMonth('created_at', now()->subMonth()->month)->sum('total_amount');
        
        $revenueGrowth = 0;
        if ($lastMonthSales > 0) {
            $revenueGrowth = (($thisMonthSales - $lastMonthSales) / $lastMonthSales) * 100;
        }

        // OPTIMIZATION APPLIED HERE: Paginate the report archive to show 10 per page
        $archives = Report::orderBy('created_at', 'desc')->paginate(10);
        
        return view('reports', compact('labels', 'revenueData', 'profitData', 'archives', 'stagnantProduct', 'thisMonthSales', 'revenueGrowth'));
    }

    public function generate(Request $request)
    {
        $availableQuarters = array_map(
            fn (int $quarter) => "q{$quarter}",
            range(1, Carbon::now()->quarter)
        );

        $request->validate([
            'report_type' => 'required|in:sales_summary,inventory_audit,fast_slow,profit_margin',
            'timeframe' => ['required', Rule::in(array_merge(['today', 'this_week', 'this_month', 'last_month'], $availableQuarters))],
            'format' => 'required|in:pdf,excel',
        ]);

        $typeNames = [
            'sales_summary' => 'Sales & Revenue Summary',
            'inventory_audit' => 'Inventory & Stock Audit',
            'fast_slow' => 'Fast/Slow Moving Products',
            'profit_margin' => 'Profit Margin Analysis'
        ];

        $period = $this->resolveTimeframe($request->timeframe, Carbon::now());
        $reportName = $period['label'] . ' - ' . $typeNames[$request->report_type];

        $report = Report::create([
            'report_name' => $reportName,
            'report_type' => $request->report_type,
            'timeframe' => $request->timeframe,
            'period_start' => $period['start'],
            'period_end' => $period['end'],
            'format' => $request->format,
            'prepared_by' => auth()->check() ? auth()->user()->name : 'Admin (System)',
        ]);

        SystemAudit::record(
            'Reports',
            'report_generated',
            "Generated {$request->format} report '{$reportName}' covering "
                . $period['start']->format('M d, Y') . ' to ' . $period['end']->format('M d, Y') . '.',
            $report,
            [
                'report_type' => $request->report_type,
                'timeframe' => $request->timeframe,
                'format' => $request->format,
                'period_start' => $period['start']->toDateString(),
                'period_end' => $period['end']->toDateString(),
            ]
        );

        return back()->with('success', "Success! Your " . strtoupper($request->format) . " report is ready. Click the Open PDF or Download icon in the archive below to access it.");
    }

    /**
     * THE UPGRADED "BRAIN": Now flawlessly detects all Timeframes
     */
    private function getAccurateReportData($report)
    {
        if ($report->period_start && $report->period_end) {
            $start = Carbon::parse($report->period_start)->startOfDay();
            $end = Carbon::parse($report->period_end)->endOfDay();
        } else {
            // Backward-compatible fallback for reports created before periods were stored.
            $name = strtolower($report->report_name);
            $legacyTimeframe = match (true) {
                str_contains($name, 'today') => 'today',
                str_contains($name, 'this week') => 'this_week',
                str_contains($name, 'last month') => 'last_month',
                str_contains($name, 'quarter 1'), str_contains($name, 'q1') => 'q1',
                str_contains($name, 'quarter 2'), str_contains($name, 'q2') => 'q2',
                str_contains($name, 'quarter 3'), str_contains($name, 'q3') => 'q3',
                str_contains($name, 'quarter 4'), str_contains($name, 'q4') => 'q4',
                default => 'this_month',
            };
            $referenceDate = Carbon::parse($report->created_at ?? now());
            $period = $this->resolveTimeframe($report->timeframe ?: $legacyTimeframe, $referenceDate);
            $start = $period['start'];
            $end = $period['end'];
        }

        $type = $report->report_type;

        // 2. INVENTORY REPORT
        if ($type == 'inventory_audit') {
            $data = Product::orderBy('product_name', 'asc')->get();
            return ['type' => 'inventory_audit', 'data' => $data];
        }

        // 3. FAST/SLOW REPORT (Uses LEFT JOIN to show items with 0 sales)
        if ($type == 'fast_slow') {
            $data = DB::table('products')
                ->leftJoin('sales', function($join) use ($start, $end) {
                    $join->on('products.id', '=', 'sales.product_id')
                         ->whereBetween('sales.created_at', [$start, $end]);
                })
                ->select(
                    'products.product_name',
                    DB::raw('COALESCE(SUM(sales.quantity_sold), 0) as total_qty'),
                    DB::raw('COALESCE(SUM(sales.total_amount), 0) as total_revenue')
                )
                ->groupBy('products.id', 'products.product_name')
                ->orderBy('total_qty', 'desc')
                ->get();
            return ['type' => 'fast_slow', 'data' => $data];
        }

        // 4. SALES & PROFIT MARGIN REPORTS
        $data = Sale::with('product')
            ->whereBetween('created_at', [$start, $end])
            ->orderBy('created_at', 'desc')
            ->get();
            
        return ['type' => $type, 'data' => $data];
    }

    /**
     * Resolve a named timeframe to exact inclusive calendar boundaries.
     */
    private function resolveTimeframe(string $timeframe, Carbon $referenceDate): array
    {
        $year = $referenceDate->year;

        return match ($timeframe) {
            'today' => [
                'label' => $referenceDate->format('F j, Y'),
                'start' => $referenceDate->copy()->startOfDay(),
                'end' => $referenceDate->copy()->endOfDay(),
            ],
            'this_week' => [
                'label' => 'This Week',
                'start' => $referenceDate->copy()->startOfWeek()->startOfDay(),
                'end' => $referenceDate->copy()->endOfWeek()->endOfDay(),
            ],
            'last_month' => [
                'label' => $referenceDate->copy()->subMonth()->format('F Y'),
                'start' => $referenceDate->copy()->subMonth()->startOfMonth(),
                'end' => $referenceDate->copy()->subMonth()->endOfMonth(),
            ],
            'q1' => $this->quarterPeriod(1, $year),
            'q2' => $this->quarterPeriod(2, $year),
            'q3' => $this->quarterPeriod(3, $year),
            'q4' => $this->quarterPeriod(4, $year),
            default => [
                'label' => $referenceDate->format('F Y'),
                'start' => $referenceDate->copy()->startOfMonth(),
                'end' => $referenceDate->copy()->endOfMonth(),
            ],
        };
    }

    private function quarterPeriod(int $quarter, int $year): array
    {
        $startMonth = (($quarter - 1) * 3) + 1;
        $start = Carbon::create($year, $startMonth, 1)->startOfDay();
        $end = $start->copy()->addMonths(2)->endOfMonth()->endOfDay();

        return [
            'label' => "Quarter {$quarter} ({$start->format('M')} - {$end->format('M')} {$year})",
            'start' => $start,
            'end' => $end,
        ];
    }

    public function viewArchive($id)
    {
        $report = Report::findOrFail($id);
        $reportData = $this->getAccurateReportData($report);

        SystemAudit::record(
            'Reports',
            'report_viewed',
            "Opened archived {$report->format} report '{$report->report_name}'.",
            $report,
            ['report_type' => $report->report_type, 'timeframe' => $report->timeframe]
        );

        return view('report-print', [
            'report' => $report, 
            'type' => $reportData['type'],
            'data' => $reportData['data']
        ]);
    }

    public function downloadArchive($id)
    {
        $report = Report::findOrFail($id);
        $reportData = $this->getAccurateReportData($report);

        SystemAudit::record(
            'Reports',
            'report_downloaded',
            "Downloaded archived report '{$report->report_name}' as CSV.",
            $report,
            ['report_type' => $report->report_type, 'timeframe' => $report->timeframe, 'download_format' => 'csv']
        );
        
        $type = $reportData['type'];
        $data = $reportData['data'];

        $fileName = str_replace(' ', '_', $report->report_name) . "_" . time() . ".csv";
        
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=\"$fileName\"",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use($type, $data) {
            $file = fopen('php://output', 'w');
            
            if ($type == 'inventory_audit') {
                fputcsv($file, ['SKU', 'Product Name', 'Category', 'Unit Price', 'In Stock']);
                foreach ($data as $item) {
                    fputcsv($file, [$item->sku, $item->product_name, $item->category, $item->unit_price, $item->in_stock]);
                }
            } elseif ($type == 'fast_slow') {
                // UPDATED: Added the DSS Status column to match the PDF
                fputcsv($file, ['Rank', 'Product Name', 'Total Qty Sold', 'Gross Revenue (PHP)', 'DSS Status']);
                $rank = 1;
                foreach ($data as $item) {
                    // DSS Logic identical to the PDF view
                    if ($rank <= 3 && $item->total_qty > 0) {
                        $status = 'Fast Moving';
                    } elseif ($item->total_qty == 0) {
                        $status = 'Dead Stock / Slow';
                    } else {
                        $status = 'Average';
                    }
                    fputcsv($file, ["#".$rank, $item->product_name, $item->total_qty, $item->total_revenue, $status]);
                    $rank++;
                }
            } elseif ($type == 'profit_margin') {
                fputcsv($file, ['Date', 'Product', 'Gross Revenue (PHP)', 'Est. Cost 70% (PHP)', 'Net Profit 30% (PHP)']);
                foreach ($data as $sale) {
                    $rev = $sale->total_amount;
                    // UPDATED: Polished to match dynamic Exact Profit Logic where possible. 
                    // Note: In CSV exports without joining product tables, fallback to 30% is standard.
                    $profit = $rev * 0.30;
                    $cost = $rev - $profit;
                    fputcsv($file, [$sale->created_at->format('Y-m-d'), $sale->product->product_name ?? 'N/A', $rev, $cost, $profit]);
                }
            } else {
                fputcsv($file, ['Receipt No', 'Date', 'Product', 'Qty', 'Total Amount (PHP)']);
                foreach ($data as $sale) {
                    fputcsv($file, ['RC-'.str_pad($sale->id, 5, '0', STR_PAD_LEFT), $sale->created_at->format('Y-m-d'), $sale->product->product_name ?? 'N/A', $sale->quantity_sold, $sale->total_amount]);
                }
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Archive Action - Delete Report
     */
    public function destroy($id)
    {
        $report = Report::findOrFail($id);

        SystemAudit::record(
            'Reports',
            'report_deleted',
            "Deleted archived {$report->format} report '{$report->report_name}'.",
            $report,
            [
                'report_type' => $report->report_type,
                'timeframe' => $report->timeframe,
                'format' => $report->format,
                'prepared_by' => $report->prepared_by,
            ]
        );

        $report->delete();

        return back()->with('success', 'Report successfully deleted from the archive.');
    }
}
