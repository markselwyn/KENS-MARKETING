<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Restock; 
use App\Models\Sale; // NEW: Needed to check sales history for stagnant items
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ProductsImport;
use Illuminate\Support\Facades\DB; // NEW: Needed for raw SQL queries

class InventoryController extends Controller
{
    /**
     * This loads your inventory.blade.php page 
     * and sends all the database products to your HTML table.
     */
    public function index()
    {
        // 1. Get all products for the main table
        $products = Product::orderBy('updated_at', 'desc')->get(); 
        
        // ==========================================
        // 2. DSS LOGIC: Find Stagnant Capital
        // Find items with high stock but low/no sales in the last 45 days
        // ==========================================
        
        // Let's find the product with the most stock that hasn't sold recently
        $stagnantProduct = DB::table('products')
            ->leftJoin('sales', function($join) {
                $join->on('products.id', '=', 'sales.product_id')
                     ->where('sales.created_at', '>=', now()->subDays(45));
            })
            ->select('products.id', 'products.product_name', 'products.in_stock', 'products.unit_price', 
                     DB::raw('COALESCE(SUM(sales.quantity_sold), 0) as recent_sales'))
            ->groupBy('products.id', 'products.product_name', 'products.in_stock', 'products.unit_price')
            ->havingRaw('recent_sales = 0') // Hasn't sold
            ->where('products.in_stock', '>', 5) // Has significant stock
            ->orderBy('products.in_stock', 'desc') // Get the one with the most stock
            ->first();

        // Prepare variables for the view
        $stagnantTitle = "Inventory Optimal";
        $stagnantMessage = "Capital allocation is healthy. No severely stagnant items detected.";
        $stagnantValue = 0;
        
        if ($stagnantProduct) {
            $tiedUpMoney = $stagnantProduct->in_stock * $stagnantProduct->unit_price;
            $formattedMoney = number_format($tiedUpMoney, 2);
            
            $stagnantTitle = $stagnantProduct->product_name;
            $stagnantMessage = "Stagnant Capital: 0 units sold in 45 days. ₱{$formattedMoney} tied up in inventory.";
            $stagnantValue = $tiedUpMoney;
        }

        // Send everything to the view
        return view('inventory', compact('products', 'stagnantTitle', 'stagnantMessage', 'stagnantValue'));
    }

    /**
     * Catch the form data from the "Add New Product" modal and save it.
     */
    public function store(Request $request)
    {
        // 1. Validate the form data
        $request->validate([
            'sku' => 'required|unique:products,sku',
            'product_name' => 'required|string|max:255',
            'category' => 'required|string',
            'unit_price' => 'required|numeric|min:0',
            'in_stock' => 'required|integer|min:0',
            'reorder_point' => 'required|integer|min:0',
        ]);

        // 2. DSS Logic: Automatically determine the stock status
        $status = 'Healthy';
        if ($request->in_stock == 0) {
            $status = 'Out of Stock';
        } elseif ($request->in_stock <= $request->reorder_point) {
            $status = 'Low Stock';
        }

        // 3. Save the new product to the database
        Product::create([
            'sku' => $request->sku,
            'product_name' => $request->product_name,
            'category' => $request->category,
            'unit_price' => $request->unit_price,
            'in_stock' => $request->in_stock,
            'reorder_point' => $request->reorder_point,
            'status' => $status,
        ]);

        // 4. Send them back to the inventory page
        return redirect()->route('inventory.index')->with('success', 'Product added successfully!');
    }

    /**
     * Catch the form data from the "Edit Product" modal and update it.
     */
    public function update(Request $request)
    {
        // 1. Validate the incoming data
        $request->validate([
            'id' => 'required|exists:products,id',
            'sku' => 'required|string|unique:products,sku,' . $request->id, 
            'product_name' => 'required|string|max:255',
            'category' => 'required|string',
            'unit_price' => 'required|numeric|min:0',
            'in_stock' => 'required|integer|min:0',
            'reorder_point' => 'required|integer|min:0',
        ]);

        // 2. Find the specific product in the database
        $product = Product::findOrFail($request->id);

        // 3. DSS Logic: Recalculate status in case they changed the stock level
        $status = 'Healthy';
        if ($request->in_stock == 0) {
            $status = 'Out of Stock';
        } elseif ($request->in_stock <= $request->reorder_point) {
            $status = 'Low Stock';
        }

        // 4. Update the database record
        $product->update([
            'sku' => $request->sku,
            'product_name' => $request->product_name,
            'category' => $request->category,
            'unit_price' => $request->unit_price,
            'in_stock' => $request->in_stock,
            'reorder_point' => $request->reorder_point,
            'status' => $status,
        ]);

        return redirect()->route('inventory.index')->with('success', 'Product updated successfully!');
    }

    /**
     * Delete a product from the database
     */
    public function destroy(Request $request)
    {
        // Make sure the ID exists before trying to delete it
        $request->validate(['id' => 'required|exists:products,id']);
        
        // Destroy it!
        Product::destroy($request->id);
        
        return redirect()->route('inventory.index')->with('success', 'Product deleted successfully!');
    }

    /**
     * This catches the Excel file when you click the upload button,
     * reads it, and saves it to the database.
     */
    public function importExcel(Request $request)
    {
        // 1. Make sure they actually uploaded an Excel file
        $request->validate([
            'excel_file' => 'required|mimes:xlsx,xls,csv|max:10240',
        ]);

        try {
            // 2. Use the package to read the file using our custom map
            Excel::import(new ProductsImport, $request->file('excel_file'));
            
            // 3. Security! Log this action for the Admin Audit Trail
            if (auth()->check()) {
                activity()
                    ->causedBy(auth()->user())
                    ->log("Imported bulk products from an Excel file.");
            }

            // 4. Send them back to the page 
            return back()->with('success', 'Excel file imported successfully!');
            
        } catch (\Exception $e) {
            // If something goes wrong, show the error
            return back()->withErrors(['error' => 'Failed to import: ' . $e->getMessage()]);
        }
    }

    /**
     * Process an incoming shipment, add stock, and recalculate DSS health status.
     */
    public function restock(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity_added' => 'required|integer|min:1',
            'supplier' => 'nullable|string|max:255',
            'reference_no' => 'nullable|string|max:100',
        ]);

        $product = Product::findOrFail($request->product_id);

        // 1. Log the incoming shipment in the ledger
        Restock::create([
            'product_id' => $product->id,
            'quantity_added' => $request->quantity_added,
            'supplier' => $request->supplier ?? 'Direct Supplier',
            'reference_no' => $request->reference_no ?? 'DR-' . time(),
        ]);

        // 2. INCREASE THE STOCK
        $newStockLevel = $product->in_stock + $request->quantity_added;

        // 3. DSS AUTOMATION: Recalculate status (bringing items back to normal!)
        $status = 'Healthy';
        if ($newStockLevel == 0) {
            $status = 'Out of Stock';
        } elseif ($newStockLevel <= $product->reorder_point) {
            $status = 'Low Stock';
        }

        // 4. Update the physical inventory
        $product->update([
            'in_stock' => $newStockLevel,
            'status' => $status,
        ]);

        return back()->with('success', 'Shipment logged! Added ' . $request->quantity_added . ' units to ' . $product->product_name . '. Status recalculated.');
    }
}