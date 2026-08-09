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
    public function index()
    {
        // 1. Get basic data
        $products = Product::where('in_stock', '>', 0)->orderBy('product_name', 'asc')->get();
        $recentSales = Sale::with('product')->orderBy('created_at', 'desc')->limit(50)->get();

        // 2. Top Metric Cards Math
        $todaySales = Sale::whereDate('created_at', Carbon::today())->sum('total_amount');
        $weekSales = Sale::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->sum('total_amount');
        $monthSales = Sale::whereYear('created_at', Carbon::now()->year)->whereMonth('created_at', Carbon::now()->month)->sum('total_amount');

        // 3. Top Sellers (Grouped by Product, counted by Quantity)
        $topSellers = Sale::with('product')
            ->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
            ->select('product_id', DB::raw('SUM(quantity_sold) as total_qty'), DB::raw('SUM(total_amount) as total_revenue'))
            ->groupBy('product_id')
            ->orderByDesc('total_qty')
            ->take(3)
            ->get();

        // ==========================================
        // 4. DSS ALGORITHM: WEEKEND FORECAST
        // Finds the hottest item this week to predict weekend volume
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
            // Predict a 20% surge on the weekend for the top selling item
            $projectedRevenue = $forecastItem->total_revenue * 1.20;
            $forecastValue = '₱' . number_format($projectedRevenue, 0);
            $forecastTitle = $forecastItem->product->product_name;
            $forecastText = "High volume predicted for {$forecastItem->product->product_name}. Recommend ensuring 2 extra staff members are scheduled for Saturday.";
        }

        // 5. Peak Hour Tracer Logic (Grouping today's sales into 2-hour blocks)
        $chartData = [0, 0, 0, 0, 0, 0]; // Represents: [8AM, 10AM, 12PM, 2PM, 4PM, 6PM]
        $salesToday = Sale::whereDate('created_at', Carbon::today())->get();
        
        foreach($salesToday as $sale) {
            $hour = $sale->created_at->format('H'); // Gets hour in 24h format (00-23)
            if($hour >= 7 && $hour < 9) $chartData[0] += $sale->total_amount;
            elseif($hour >= 9 && $hour < 11) $chartData[1] += $sale->total_amount;
            elseif($hour >= 11 && $hour < 13) $chartData[2] += $sale->total_amount;
            elseif($hour >= 13 && $hour < 15) $chartData[3] += $sale->total_amount;
            elseif($hour >= 15 && $hour < 17) $chartData[4] += $sale->total_amount;
            elseif($hour >= 17 && $hour <= 23) $chartData[5] += $sale->total_amount;
        }

        // Send everything to the view!
        return view('sales', compact('products', 'recentSales', 'todaySales', 'weekSales', 'monthSales', 'topSellers', 'chartData', 'forecastTitle', 'forecastText', 'forecastValue'));
    }

    /**
     * Process a new sale, deduct stock, and trigger DSS alerts.
     */
    public function store(Request $request)
    {
        // 1. Validate the cashier's input
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity_sold' => 'required|integer|min:1',
        ]);

        // 2. Find the exact product being sold
        $product = Product::findOrFail($request->product_id);

        // 3. Security: Prevent selling more stock than you actually have!
        if ($request->quantity_sold > $product->in_stock) {
            return back()->withErrors(['error' => 'Not enough stock! You only have ' . $product->in_stock . ' left.']);
        }

        // 4. Calculate the total revenue for this sale
        $totalAmount = $product->unit_price * $request->quantity_sold;

        // 5. Record the sale in the ledger
        Sale::create([
            'product_id' => $product->id,
            'quantity_sold' => $request->quantity_sold,
            'total_amount' => $totalAmount,
            // Carbon::now() will use your local Bicol time based on your config/app.php timezone setup
            'created_at' => Carbon::now(), 
            'updated_at' => Carbon::now(),
        ]);

        // 6. DEDUCT THE STOCK
        $newStockLevel = $product->in_stock - $request->quantity_sold;

        // 7. DSS AUTOMATION: Recalculate the status based on the new stock level
        $status = 'Healthy';
        if ($newStockLevel == 0) {
            $status = 'Out of Stock';
        } elseif ($newStockLevel <= $product->reorder_point) {
            $status = 'Low Stock';
        }

        // 8. Save the updated stock and status back to the inventory
        $product->update([
            'in_stock' => $newStockLevel,
            'status' => $status,
        ]);

        // 9. Send the cashier back to the POS screen with a success message
        return back()->with('success', 'Sale processed successfully! Inventory deducted.');
    }
}