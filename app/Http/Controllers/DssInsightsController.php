<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Sale;
use Illuminate\Support\Facades\DB;

class DssInsightsController extends Controller
{
    public function index()
    {
        try {
            // ==========================================
            // 1. DSS ALGORITHM: FAST-SELLING PRODUCTS
            // ==========================================
            $allFastMovers = DB::table('products')
                ->join('sales', 'products.id', '=', 'sales.product_id')
                ->where('sales.created_at', '>=', now()->subDays(30))
                ->select('products.product_name', 'products.in_stock', DB::raw('SUM(sales.quantity_sold) as total_sold'))
                ->groupBy('products.id', 'products.product_name', 'products.in_stock')
                ->orderBy('total_sold', 'desc')
                ->get(); // FETCH THE ENTIRE LIST
            
            // Extract just the #1 item for the main card display
            $fastMover = $allFastMovers->first();

            // ==========================================
            // 2. DSS ALGORITHM: STAGNANT CAPITAL (UPGRADED TO ELOQUENT)
            // ==========================================
            // Step A: Get IDs of products that HAVE sold in the last 45 days
            $recentlySoldProductIds = DB::table('sales')
                ->where('created_at', '>=', now()->subDays(45))
                ->pluck('product_id');

            // Step B: Find stagnant products (Not in the sold list, > 0 stock, and grace period passed)
            $allStagnantProducts = Product::where('in_stock', '>', 0)
                ->whereNotIn('id', $recentlySoldProductIds)
                ->where(function ($query) {
                    // NEW: THE 14-DAY GRACE PERIOD FILTER
                    $query->whereNull('promo_applied_at')
                          ->orWhere('promo_applied_at', '<', now()->subDays(14));
                })
                ->orderByRaw('(in_stock * unit_price) DESC') 
                ->get(); 
            
            // Extract just the #1 item for the main card display
            $stagnantProduct = $allStagnantProducts->first();

            // ==========================================
            // 3. DSS ALGORITHM: CRITICAL RESTOCK
            // ==========================================
            $allCriticalRestocks = Product::whereColumn('in_stock', '<=', 'reorder_point')
                ->orderBy('in_stock', 'asc')
                ->get(); // FETCH THE ENTIRE LIST
            
            // Extract just the #1 item for the main card display
            $criticalRestock = $allCriticalRestocks->first();

            // ==========================================
            // 4. DSS ENGINE: "WHAT-IF" SIMULATOR DATA
            // ==========================================
            $simulatorProducts = Product::where('status', '!=', 'Out of Stock')
                ->orderBy('product_name', 'asc')
                ->get();

            // Pass both the single items (for cards) AND the full lists (for modals) to the view
            return view('dss-insights', compact(
                'fastMover', 'allFastMovers', 
                'stagnantProduct', 'allStagnantProducts', 
                'criticalRestock', 'allCriticalRestocks', 
                'simulatorProducts'
            ));

        } catch (\Exception $e) {
            // ==========================================
            // SYSTEM FAILSAFE
            // ==========================================
            $fastMover = null;
            $allFastMovers = collect();
            
            $stagnantProduct = null;
            $allStagnantProducts = collect();
            
            $criticalRestock = null;
            $allCriticalRestocks = collect();
            
            $simulatorProducts = collect(); 

            return view('dss-insights', compact(
                'fastMover', 'allFastMovers', 
                'stagnantProduct', 'allStagnantProducts', 
                'criticalRestock', 'allCriticalRestocks', 
                'simulatorProducts'
            ));
        }
    }

    // ==========================================
    // 5. APPLY WHAT-IF SIMULATOR DISCOUNT TO DB
    // ==========================================
    public function applyDiscount(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'new_price' => 'required|numeric|min:0',
            'discount_percent' => 'required|integer'
        ]);

        $product = Product::findOrFail($request->product_id);
        $oldPrice = $product->unit_price;
        
        // Permanent database update + Start the 14-day Grace Period
        // Using explicit property assignment to bypass mass assignment constraints
        $product->unit_price = $request->new_price;
        $product->promo_applied_at = now();
        $product->save();

        return redirect()->back()->with('success', "Price for '{$product->product_name}' updated from ₱" . number_format($oldPrice, 2) . " to ₱" . number_format($request->new_price, 2) . " ({$request->discount_percent}% Markdown applied). System will monitor performance for 14 days.");
    }
}