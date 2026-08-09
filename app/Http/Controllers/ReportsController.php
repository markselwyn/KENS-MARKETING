<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\Report;
use App\Models\Product; 
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportsController extends Controller
{
    public function index()
    {
        $labels = [];
        $revenueData = [];
        $profitData = [];

        // 1. 6-MONTH CHART DATA
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::today()->startOfMonth()->subMonths($i);
            $label = $month->format('F');
            if ($i == 0) $label .= ' (Current)';
            $labels[] = $label;

            $monthlyRevenue = Sale::whereYear('created_at', $month->year)->whereMonth('created_at', $month->month)->sum('total_amount');
            $revenueData[] = $monthlyRevenue;
            $profitData[] = $monthlyRevenue * 0.30; 
        }

        // ==========================================
        // 2. DSS MACRO ANALYSIS: STAGNANT CAPITAL
        // Exactly matches the algorithm from Inventory and Insights modules
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
        // Compares this month's sales to last month's sales
        // ==========================================
        $thisMonthSales = Sale::whereYear('created_at', now()->year)->whereMonth('created_at', now()->month)->sum('total_amount');
        $lastMonthSales = Sale::whereYear('created_at', now()->subMonth()->year)->whereMonth('created_at', now()->subMonth()->month)->sum('total_amount');
        
        $revenueGrowth = 0;
        if ($lastMonthSales > 0) {
            $revenueGrowth = (($thisMonthSales - $lastMonthSales) / $lastMonthSales) * 100;
        }

        $archives = Report::orderBy('created_at', 'desc')->get();
        
        return view('reports', compact('labels', 'revenueData', 'profitData', 'archives', 'stagnantProduct', 'thisMonthSales', 'revenueGrowth'));
    }

    public function generate(Request $request)
    {
        $request->validate([
            'report_type' => 'required|string',
            'timeframe' => 'required|string',
            'format' => 'required|in:pdf,excel',
        ]);

        $typeNames = [
            'sales_summary' => 'Sales & Revenue Summary',
            'inventory_audit' => 'Inventory & Stock Audit',
            'fast_slow' => 'Fast/Slow Moving Products',
            'profit_margin' => 'Profit Margin Analysis'
        ];

        // Format timeframe for the title (e.g., 'last_month' becomes 'Last Month')
        $timeLabel = ucwords(str_replace('_', ' ', $request->timeframe));
        $reportName = $timeLabel . ' - ' . $typeNames[$request->report_type];

        Report::create([
            'report_name' => $reportName,
            'report_type' => $request->report_type,
            'format' => $request->format,
            'prepared_by' => auth()->check() ? auth()->user()->name : 'Admin (System)',
        ]);

        return back()->with('success', "Success! Your " . strtoupper($request->format) . " report is ready. Click the Open PDF or Download icon in the archive below to access it.");
    }

    /**
     * THE UPGRADED "BRAIN": Now flawlessly detects all Timeframes
     */
    private function getAccurateReportData($report)
    {
        $now = Carbon::now();
        $name = strtolower($report->report_name);

        // 1. BULLETPROOF DATE BOUNDARIES
        if (str_contains($name, 'today')) {
            $start = $now->copy()->startOfDay();
            $end = $now->copy()->endOfDay();
        } elseif (str_contains($name, 'this week')) {
            $start = $now->copy()->startOfWeek();
            $end = $now->copy()->endOfWeek();
        } elseif (str_contains($name, 'last month')) {
            $start = $now->copy()->subMonth()->startOfMonth();
            $end = $now->copy()->subMonth()->endOfMonth();
        } elseif (str_contains($name, 'q1') || str_contains($name, 'quarter 1')) {
            // Your dropdown is Jan-Apr (Months 1 to 4)
            $start = $now->copy()->month(1)->startOfMonth();
            $end = $now->copy()->month(4)->endOfMonth();
        } else {
            // Default to This Month
            $start = $now->copy()->startOfMonth();
            $end = $now->copy()->endOfMonth();
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

    public function viewArchive($id)
    {
        $report = Report::findOrFail($id);
        $reportData = $this->getAccurateReportData($report);

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
        $report->delete();

        return back()->with('success', 'Report successfully deleted from the archive.');
    }
}