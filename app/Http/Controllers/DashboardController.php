<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sale; 
use App\Models\Product; 
use App\Models\Report; 
use Carbon\Carbon; 
use Illuminate\Support\Facades\DB; 

class DashboardController extends Controller
{
    public function index()
    {
        // ==========================================
        // 1. REVENUE & PROFIT MATH 
        // ==========================================
        $grossRevenue = Sale::sum('total_amount'); 
        $totalOrders = Sale::count();
        $avgOrderValue = $totalOrders > 0 ? ($grossRevenue / $totalOrders) : 0;
        
        // ==========================================
        // FIXED: EXACT NET PROFIT CALCULATION
        // ==========================================
        // True 30% Profit Margin: Simply multiply Gross Revenue by 0.30
        $netProfit = $grossRevenue * 0.30;

        // ==========================================
        // 2. DSS INVENTORY LOGIC
        // ==========================================
        $lowStockCount = Product::whereColumn('in_stock', '<=', 'reorder_point')->count(); 
        $priorityItem = Product::whereColumn('in_stock', '<=', 'reorder_point')
                               ->orderBy('in_stock', 'asc')
                               ->first();
        
        if ($priorityItem) {
            $priorityRestock = "{$priorityItem->product_name} ({$priorityItem->in_stock} left)";
        } else {
            $priorityRestock = "All stock levels optimal";
        }

        // ==========================================
        // 3. RECENT TRANSACTIONS (Eager Loading applied)
        // ==========================================
        $recentTransactions = Sale::with('product')->latest()->take(3)->get();

        // ==========================================
        // 4. CHART LOGIC: 3 Dynamic Timeframes
        // ==========================================
        
        // --- A. Last 7 Days (Daily)
        $labels7 = []; $data7 = []; $total7 = 0;
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $labels7[] = $date->format('D'); 
            $sum = Sale::whereDate('created_at', $date)->sum('total_amount');
            $data7[] = $sum;
            $total7 += $sum;
        }

        // --- B. Last 30 Days (Grouped by 4 Weeks)
        $labels30 = []; $data30 = []; $total30 = 0;
        for ($i = 3; $i >= 0; $i--) {
            $start = Carbon::today()->subDays(($i * 7) + 6)->startOfDay();
            $end = Carbon::today()->subDays($i * 7)->endOfDay();
            $labels30[] = 'Week ' . (4 - $i);
            $sum = Sale::whereBetween('created_at', [$start, $end])->sum('total_amount');
            $data30[] = $sum;
            $total30 += $sum;
        }

        // --- C. This Year (Jan - Dec)
        $labelsYear = []; $dataYear = []; $totalYear = 0;
        $currentYear = Carbon::now()->year;
        for ($i = 1; $i <= 12; $i++) {
            $labelsYear[] = Carbon::create()->month($i)->format('M');
            $sum = Sale::whereYear('created_at', $currentYear)->whereMonth('created_at', $i)->sum('total_amount');
            $dataYear[] = $sum;
            $totalYear += $sum;
        }

        // ==========================================
        // 5. CATEGORY SALES VELOCITY
        // ==========================================
        $categoryVelocity = DB::table('products')
            ->join('sales', 'products.id', '=', 'sales.product_id')
            ->select('products.category', DB::raw('SUM(sales.total_amount) as total_sales'))
            ->groupBy('products.category')
            ->orderBy('total_sales', 'desc')
            ->take(4)
            ->get();
            
        $maxCategorySales = $categoryVelocity->max('total_sales') ?: 1;

        // ==========================================
        // 6. REAL DYNAMIC SMART INSIGHTS ENGINE (DSS)
        // ==========================================
        
        // --- Insight 1: Inventory Risk Assessment 
        $criticalItem = Product::whereColumn('in_stock', '<=', 'reorder_point')
            ->orderBy('in_stock', 'asc')
            ->first();

        if ($criticalItem) {
            $insight1Title = "Depletion Warning";
            if ($criticalItem->in_stock == 0) {
                $insight1Text = "<strong>{$criticalItem->product_name}</strong> is currently <strong>Out of Stock</strong>. Immediate replenishment required.";
                $insight1Badge = "Out of Stock";
                $insight1Color = "red";
            } else {
                $insight1Text = "<strong>{$criticalItem->product_name}</strong> has <strong>Limited Stock</strong> ({$criticalItem->in_stock} remaining). Projected to stock out soon based on reorder point ({$criticalItem->reorder_point}).";
                $insight1Badge = "Limited Stock";
                $insight1Color = "orange";
            }
            $insight1Btn = "Review Restock Priority";
            $insight1Link = url('/inventory?status=limited_stock');
        } else {
            $insight1Title = "Stock Levels Optimal";
            $insight1Text = "All products are currently <strong>Available</strong> and above their reorder points. No immediate replenishment risks detected.";
            $insight1Btn = "View Inventory";
            $insight1Link = url('/inventory');
            $insight1Badge = "Available";
            $insight1Color = "green";
        }

        // --- Insight 2: Category Revenue Lead & Velocity
        $topCategory = $categoryVelocity->first();

        if ($topCategory && $grossRevenue > 0) {
            $percentage = round(($topCategory->total_sales / $grossRevenue) * 100);
            $formattedAmount = number_format($topCategory->total_sales, 2);
            
            $insight2Title = "Sales Pattern Detected";
            $insight2Text = "<strong>{$topCategory->category}</strong> category accounts for <strong>{$percentage}%</strong> of total gross revenue (₱{$formattedAmount}). Prioritize marketing and display space for this category.";
            $insight2Btn = "View Sales Analytics";
            $insight2Link = url('/sales');
        } else {
            $insight2Title = "Sales Pattern Detected";
            $insight2Text = "Insufficient sales history to compute category trends. Record additional transactions in the Sales Module.";
            $insight2Btn = "Go to Sales Module";
            $insight2Link = url('/sales');
        }

        // Send EVERYTHING to the blade
        return view('dashboard', compact(
            'grossRevenue', 'netProfit', 'avgOrderValue', 'lowStockCount', 
            'priorityRestock', 'recentTransactions', 'totalOrders',
            'categoryVelocity', 'maxCategorySales',
            'labels7', 'data7', 'total7',
            'labels30', 'data30', 'total30',
            'labelsYear', 'dataYear', 'totalYear',
            'insight1Title', 'insight1Text', 'insight1Btn', 'insight1Link', 'insight1Badge', 'insight1Color',
            'insight2Title', 'insight2Text', 'insight2Btn', 'insight2Link'
        ));
    }

    /**
     * GLOBAL LIVE SEARCH ENGINE (AJAX)
     */
    public function globalSearch(Request $request)
    {
        $query = $request->input('query');
        
        if (!$query || strlen($query) < 2) {
            return response()->json(['products' => [], 'sales' => [], 'reports' => []]);
        }

        $products = Product::where('product_name', 'LIKE', "%{$query}%")
            ->orWhere('sku', 'LIKE', "%{$query}%")
            ->orWhere('category', 'LIKE', "%{$query}%")
            ->take(3)->get(['id', 'product_name', 'sku', 'in_stock']);

        $cleanId = preg_replace('/[^0-9]/', '', $query); 
        $sales = collect();
        if ($cleanId) {
            $sales = Sale::with('product')
                ->where('id', 'LIKE', "%{$cleanId}%")
                ->take(3)->get();
        }

        $reports = Report::where('report_name', 'LIKE', "%{$query}%")
            ->orWhere('report_type', 'LIKE', "%{$query}%")
            ->take(3)->get(['id', 'report_name', 'format', 'created_at']);

        return response()->json([
            'products' => $products,
            'sales' => $sales,
            'reports' => $reports
        ]);
    }
}