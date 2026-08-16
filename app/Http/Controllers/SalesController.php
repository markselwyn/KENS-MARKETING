<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Sale;
use Carbon\Carbon; 
use Illuminate\Support\Facades\DB; 

class SalesController extends Controller
{
    /**
     * Load the Sales UI and send it the products to sell.
     */
    public function index(Request $request)
    {
        // ==========================================
        // 1. GET BASIC DATA 
        // ==========================================
        $products = Product::where('in_stock', '>', 0)->orderBy('product_name', 'asc')->get();
        
        // ==========================================
        // UNIFIED SEARCH LOGIC FOR SALES LEDGER
        // ==========================================
        $query = Sale::with('product');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                // Search by Customer Name
                $q->where('customer_name', 'like', "%{$search}%")
                  // Or Search by Product Name (via relationship)
                  ->orWhereHas('product', function($subQuery) use ($search) {
                      $subQuery->where('product_name', 'like', "%{$search}%");
                  })
                  // Or Search by Receipt Number
                  ->orWhere('id', 'like', "%{$search}%"); 
            });
        }

        $recentSales = $query->orderBy('created_at', 'desc')->paginate(10)->appends($request->all());

        // ==========================================
        // 2. TOP METRIC CARDS MATH
        // ==========================================
        $todaySales = Sale::whereDate('created_at', Carbon::today())->sum('total_amount');
        $weekSales = Sale::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->sum('total_amount');
        $monthSales = Sale::whereYear('created_at', Carbon::now()->year)->whereMonth('created_at', Carbon::now()->month)->sum('total_amount');

        // ==========================================
        // 3. TOP SELLERS
        // ==========================================
        $topSellers = Sale::with('product')
            ->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
            ->select('product_id', DB::raw('SUM(quantity_sold) as total_qty'), DB::raw('SUM(total_amount) as total_revenue'))
            ->groupBy('product_id')
            ->orderByDesc('total_qty')
            ->take(3)
            ->get();

        // ==========================================
        // 4. DSS ALGORITHM: WEEKEND FORECAST
        // ==========================================
        $forecastItem = Sale::with('product')
            ->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
            ->select('product_id', DB::raw('SUM(quantity_sold) as total_qty'), DB::raw('SUM(total_amount) as total_revenue'))
            ->groupBy('product_id')
            ->orderByDesc('total_qty')
            ->first();
            
        $forecastTitle = "Insufficient Data";
        $forecastText = "Record more sales this week to generate a reliable weekend staff and inventory forecast.";
        $forecastValue = "₱0";
        
        if ($forecastItem && $forecastItem->product) {
            $projectedRevenue = $forecastItem->total_revenue * 1.20;
            $forecastValue = '₱' . number_format($projectedRevenue, 0);
            $forecastTitle = $forecastItem->product->product_name;
            $forecastText = "High volume predicted for {$forecastItem->product->product_name}. Recommend ensuring 2 extra staff members are scheduled for Saturday.";
        }

        // ==========================================
        // 5. PEAK HOUR TRACER LOGIC
        // ==========================================
        $chartData = [0, 0, 0, 0, 0, 0]; 
        $salesToday = Sale::whereDate('created_at', Carbon::today())->get();
        
        foreach($salesToday as $sale) {
            $hour = $sale->created_at->format('H'); 
            if($hour >= 7 && $hour < 9) $chartData[0] += $sale->total_amount;
            elseif($hour >= 9 && $hour < 11) $chartData[1] += $sale->total_amount;
            elseif($hour >= 11 && $hour < 13) $chartData[2] += $sale->total_amount;
            elseif($hour >= 13 && $hour < 15) $chartData[3] += $sale->total_amount;
            elseif($hour >= 15 && $hour < 17) $chartData[4] += $sale->total_amount;
            elseif($hour >= 17 && $hour <= 23) $chartData[5] += $sale->total_amount;
        }

        return view('sales', compact('products', 'recentSales', 'todaySales', 'weekSales', 'monthSales', 'topSellers', 'chartData', 'forecastTitle', 'forecastText', 'forecastValue'));
    }

    /**
     * Process a new sale, deduct stock, and trigger DSS alerts.
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity_sold' => 'required|integer|min:1',
            'customer_name' => 'nullable|string|max:255' // New Validation
        ], [
            'product_id.required' => 'Please select a valid product from the inventory.',
            'quantity_sold.min' => 'You must sell at least 1 item.'
        ]);

        try {
            DB::transaction(function () use ($request) {
                
                $product = Product::lockForUpdate()->findOrFail($request->product_id);

                if ($product->in_stock < $request->quantity_sold) {
                    throw new \Exception("Transaction blocked: Insufficient stock. Only {$product->in_stock} units of {$product->product_name} available.");
                }

                $secureTotalAmount = $product->unit_price * $request->quantity_sold;
                $newStockLevel = $product->in_stock - $request->quantity_sold;

                $status = 'Healthy';
                if ($newStockLevel == 0) {
                    $status = 'Out of Stock';
                } elseif ($newStockLevel <= $product->reorder_point) {
                    $status = 'Low Stock';
                }

                $product->update([
                    'in_stock' => $newStockLevel,
                    'status' => $status,
                ]);

                Sale::create([
                    'product_id' => $product->id,
                    'customer_name' => $request->customer_name ?: 'Walk-in', // Save name or default to Walk-in
                    'quantity_sold' => $request->quantity_sold,
                    'total_amount' => $secureTotalAmount,
                    'created_at' => Carbon::now(), 
                    'updated_at' => Carbon::now(),
                ]);
            });

            return redirect()->back()->with('success', 'Sale processed successfully! Inventory deducted.');

        } catch (\Exception $e) {
            return redirect()->back()->withErrors([$e->getMessage()]);
        }
    }
}