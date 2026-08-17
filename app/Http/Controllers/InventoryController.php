<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Restock; 
use App\Models\Sale; 
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ProductsImport;
use Illuminate\Support\Facades\DB; 
use App\Support\SystemAudit;

class InventoryController extends Controller
{
    private const MAX_STOCK = 1000000;

    /**
     * This loads your inventory.blade.php page 
     * and handles global server-side searching/filtering.
     */
    public function index(Request $request)
    {
        // 1. Start a database query instead of loading all at once
        $query = Product::query();

        // 2. SERVER-SIDE SEARCH: Filter by Search Term globally
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('sku', 'like', "%{$search}%")
                  ->orWhere('product_name', 'like', "%{$search}%");
            });
        }

        // 3. SERVER-SIDE FILTER: Filter by Category globally
        if ($request->filled('category') && $request->category !== 'all') {
            $query->where('category', $request->category);
        }

        // 4. NEW STATUS FILTER (Available, Limited Stock, Out of Stock)
        if ($request->filled('status') && $request->status !== 'all') {
            if ($request->status === 'available') {
                $query->whereColumn('in_stock', '>', 'reorder_point');
            } elseif ($request->status === 'limited_stock') {
                $query->where('in_stock', '>', 0)->whereColumn('in_stock', '<=', 'reorder_point');
            } elseif ($request->status === 'out_of_stock') {
                $query->where('in_stock', '<=', 0);
            }
        }

        // OPTIMIZATION: Paginate the filtered results & append URL parameters so "Next" remembers the search
        $products = $query->orderBy('updated_at', 'desc')->paginate(15)->appends($request->all()); 
        
        // Fetch all unique categories from the database for the dynamic dropdown
        $categories = Product::pluck('category')->unique()->filter()->sort();
        
        // ==========================================
        // 5. DSS LOGIC: Find Stagnant Capital (UPGRADED TO ELOQUENT)
        // ==========================================
        
        // Step A: Get IDs of products that HAVE sold in the last 45 days
        $recentlySoldProductIds = DB::table('sales')
            ->where('created_at', '>=', now()->subDays(45))
            ->pluck('product_id');

        // Step B: Find stagnant products (Not in the sold list, > 0 stock, and grace period passed)
        // Using Eloquent (Product::where) instead of DB::table ensures we get the true Model with the ID
        $stagnantProduct = Product::where('in_stock', '>', 0)
            ->whereNotIn('id', $recentlySoldProductIds)
            ->where(function ($query) {
                // Ignore products that just had a promo applied in the last 14 days
                $query->whereNull('promo_applied_at')
                      ->orWhere('promo_applied_at', '<', now()->subDays(14));
            })
            ->orderByRaw('(in_stock * unit_price) DESC') // Prioritize highest capital tied up
            ->first();

        // Prepare variables for the view
        $stagnantTitle = "Inventory Optimal";
        $stagnantMessage = "Capital allocation is healthy. No severely stagnant items detected.";
        $stagnantValue = 0;
        $stagnantId = null; // Track exact ID to prevent duplicate conflicts
        
        if ($stagnantProduct) {
            $tiedUpMoney = $stagnantProduct->in_stock * $stagnantProduct->unit_price;
            $formattedMoney = number_format($tiedUpMoney, 2);
            
            $stagnantTitle = $stagnantProduct->product_name;
            $stagnantMessage = "Stagnant Capital: 0 units sold in 45 days. ₱{$formattedMoney} tied up in inventory.";
            $stagnantValue = $tiedUpMoney;
            $stagnantId = $stagnantProduct->id; // Pass exact ID
        }

        // Send everything to the view
        return view('inventory', compact('products', 'categories', 'stagnantTitle', 'stagnantMessage', 'stagnantValue', 'stagnantId'));
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
            'in_stock' => 'required|integer|min:0|max:' . self::MAX_STOCK,
            'reorder_point' => 'required|integer|min:0',
        ], [
            'in_stock.max' => 'Stock cannot be more than 1,000,000 units per product.',
        ]);

        // 2. DSS Logic: Automatically determine the stock status
        $status = 'Available';
        if ($request->in_stock <= 0) {
            $status = 'Out of Stock';
        } elseif ($request->in_stock <= $request->reorder_point) {
            $status = 'Limited Stock';
        }

        // 3. Save the new product to the database
        $product = Product::create([
            'sku' => $request->sku,
            'product_name' => $request->product_name,
            'category' => $request->category,
            'unit_price' => $request->unit_price,
            'in_stock' => $request->in_stock,
            'reorder_point' => $request->reorder_point,
            'status' => $status,
        ]);

        SystemAudit::record(
            'Inventory',
            'product_created',
            "Added product {$product->product_name} ({$product->sku}) with {$product->in_stock} units at PHP " . number_format((float) $product->unit_price, 2) . ' each.',
            $product,
            [
                'sku' => $product->sku,
                'category' => $product->category,
                'unit_price' => (float) $product->unit_price,
                'stock' => (int) $product->in_stock,
                'reorder_point' => (int) $product->reorder_point,
            ]
        );

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
            'in_stock' => 'required|integer|min:0|max:' . self::MAX_STOCK,
            'reorder_point' => 'required|integer|min:0',
        ], [
            'in_stock.max' => 'Stock cannot be more than 1,000,000 units per product.',
        ]);

        // 2. Find the specific product in the database
        $product = Product::findOrFail($request->id);
        $before = $product->only(['sku', 'product_name', 'category', 'unit_price', 'in_stock', 'reorder_point', 'status']);

        // 3. DSS Logic: Recalculate status in case they changed the stock level
        $status = 'Available';
        if ($request->in_stock <= 0) {
            $status = 'Out of Stock';
        } elseif ($request->in_stock <= $request->reorder_point) {
            $status = 'Limited Stock';
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

        $changes = $product->getChanges();
        unset($changes['updated_at']);

        $changeSummary = collect($changes)
            ->map(fn ($value, $field) => str_replace('_', ' ', $field) . ': ' . ($before[$field] ?? 'none') . " -> {$value}")
            ->values()
            ->implode('; ');

        SystemAudit::record(
            'Inventory',
            'product_updated',
            "Updated product {$product->product_name} ({$product->sku})" . ($changeSummary ? ": {$changeSummary}." : ' with no field changes.'),
            $product,
            ['before' => $before, 'after' => $product->only(array_keys($before))]
        );

        return redirect()->route('inventory.index')->with('success', 'Product updated successfully!');
    }

    /**
     * Delete a product from the database
     */
    public function destroy(Request $request)
    {
        // Make sure the ID exists before trying to delete it
        $request->validate(['id' => 'required|exists:products,id']);
        
        $product = Product::findOrFail($request->id);

        SystemAudit::record(
            'Inventory',
            'product_deleted',
            "Deleted product {$product->product_name} ({$product->sku}); its stock level was {$product->in_stock} units.",
            $product,
            $product->only(['sku', 'product_name', 'category', 'unit_price', 'in_stock', 'reorder_point', 'status'])
        );

        $product->delete();
        
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
            $import = new ProductsImport;
            Excel::import($import, $request->file('excel_file'));

            $fileName = $request->file('excel_file')->getClientOriginalName();
            SystemAudit::record(
                'Inventory',
                'products_imported',
                "Imported inventory file {$fileName}: {$import->processed} rows processed, {$import->created} products created, and {$import->updated} products updated.",
                null,
                [
                    'file_name' => $fileName,
                    'rows_processed' => $import->processed,
                    'products_created' => $import->created,
                    'products_updated' => $import->updated,
                    'sample_skus' => $import->skus,
                ]
            );

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
            'quantity_added' => 'required|integer|min:1|max:' . self::MAX_STOCK,
            'supplier' => 'nullable|string|max:255',
            'reference_no' => 'nullable|string|max:100',
        ], [
            'quantity_added.required' => 'Please enter how many items were delivered.',
            'quantity_added.integer' => 'Please enter a whole number, like 1, 5, or 15. Do not use decimals.',
            'quantity_added.min' => 'Please enter at least 1 item.',
            'quantity_added.max' => 'That quantity is too large. Please enter a smaller number of items.',
        ]);

        $product = Product::findOrFail($request->product_id);
        $maximumAddition = max(0, self::MAX_STOCK - (int) $product->in_stock);

        $request->validate([
            'quantity_added' => 'integer|max:' . $maximumAddition,
        ], [
            'quantity_added.max' => 'That delivery would make the total stock too large. You can add up to ' . number_format($maximumAddition) . ' items.',
        ]);

        $oldStockLevel = (int) $product->in_stock;
        $supplier = $request->supplier ?: 'Direct Supplier';
        $referenceNo = $request->reference_no ?: 'DR-' . time();

        // 1. Log the incoming shipment in the ledger
        $restock = Restock::create([
            'product_id' => $product->id,
            'quantity_added' => $request->quantity_added,
            'supplier' => $supplier,
            'reference_no' => $referenceNo,
        ]);

        // 2. INCREASE THE STOCK
        $newStockLevel = $product->in_stock + $request->quantity_added;

        // 3. DSS AUTOMATION: Recalculate status
        $status = 'Available';
        if ($newStockLevel <= 0) {
            $status = 'Out of Stock';
        } elseif ($newStockLevel <= $product->reorder_point) {
            $status = 'Limited Stock';
        }

        // 4. Update the physical inventory
        $product->update([
            'in_stock' => $newStockLevel,
            'status' => $status,
        ]);

        SystemAudit::record(
            'Inventory',
            'product_restocked',
            "Restocked {$product->product_name} ({$product->sku}) by {$request->quantity_added} units: stock {$oldStockLevel} -> {$newStockLevel}; supplier {$supplier}; reference {$referenceNo}.",
            $restock,
            [
                'product_id' => $product->id,
                'sku' => $product->sku,
                'quantity_added' => (int) $request->quantity_added,
                'stock_before' => $oldStockLevel,
                'stock_after' => $newStockLevel,
                'supplier' => $supplier,
                'reference_no' => $referenceNo,
            ]
        );

        return back()->with('success', 'Shipment logged! Added ' . $request->quantity_added . ' units to ' . $product->product_name . '. Status recalculated.');
    }
}
