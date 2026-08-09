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
        // ==========================================
        // 1. DSS ALGORITHM: FAST-SELLING PRODUCT
        // Identifies the product with the highest sales volume in the last 30 days
        // ==========================================
        $fastMover = DB::table('products')
            ->join('sales', 'products.id', '=', 'sales.product_id')
            ->where('sales.created_at', '>=', now()->subDays(30))
            ->select('products.product_name', 'products.in_stock', DB::raw('SUM(sales.quantity_sold) as total_sold'))
            ->groupBy('products.id', 'products.product_name', 'products.in_stock')
            ->orderBy('total_sold', 'desc')
            ->first();

        // ==========================================
        // 2. DSS ALGORITHM: STAGNANT CAPITAL
        // Identifies items with stock but ZERO sales in the last 45 days, ranked by highest money tied up
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
            ->orderByRaw('(products.in_stock * products.unit_price) DESC') // Prioritize highest financial impact
            ->first();

        // ==========================================
        // 3. DSS ALGORITHM: CRITICAL RESTOCK
        // Identifies the most severely depleted item
        // ==========================================
        $criticalRestock = Product::whereColumn('in_stock', '<=', 'reorder_point')
            ->orderBy('in_stock', 'asc')
            ->first();

        // Pass the calculated data to the view
        return view('dss-insights', compact('fastMover', 'stagnantProduct', 'criticalRestock'));
    }
}