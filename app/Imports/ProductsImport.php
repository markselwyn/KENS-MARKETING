<?php

namespace App\Imports;

use App\Models\Product;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\SkipsUnknownSheets;

class ProductsImport implements WithMultipleSheets, SkipsUnknownSheets 
{
    public function sheets(): array
    {
        // Dynamic Allocation: Prepare up to 20 sheet slots. 
        // SkipsUnknownSheets will safely ignore the ones that don't actually exist in the file.
        $sheets = [];
        for ($i = 0; $i < 20; $i++) {
            $sheets[$i] = new SheetImport();
        }
        
        return $sheets;
    }

    public function onUnknownSheet($sheetName)
    {
        // Do nothing. Just continue to the next sheet.
    }
}

class SheetImport implements ToCollection, WithHeadingRow
{
    /**
     * Skip the first 3 rows. Start grabbing data on Row 4 where the real headers are.
     */
    public function headingRow(): int
    {
        return 4; 
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            // 1. If the row doesn't have a description, it's a blank row (or the instruction tab). Skip it!
            if (!isset($row['description']) || empty(trim($row['description']))) {
                continue;
            }

            // 2. Map Ken's Marketing Excel columns to our database columns
            $productName = trim($row['description']);
            $category = $row['brand'] ?? 'Uncategorized';
            $sku = $row['product_inventory_code'] ?? 'SKU-' . strtoupper(substr(uniqid(), 0, 6));
            
            // Clean the unit price (removes commas if they typed 1,500 in Excel)
            $priceRaw = str_replace(',', '', $row['unit_price'] ?? 0);
            $unitPrice = floatval($priceRaw);
            
            $inStock = intval($row['quantity_in_stock'] ?? 0); 
            $reorderPoint = 5; // Default safe buffer

            // 3. DSS Automation: Calculate Status based on stock level
            $status = 'Available';
            if ($inStock <= 0) {
                $status = 'Out of Stock';
            } elseif ($inStock <= $reorderPoint) {
                $status = 'Limited Stock';
            }

            // 4. Save to Database
            // FIX: We now search by SKU instead of Product Name! 
            // If the SKU exists, it updates the stock/name. If it doesn't, it creates a new row.
            Product::updateOrCreate(
                ['sku' => $sku], // <-- SEARCH CRITERIA
                [
                    'product_name' => $productName, // <-- DATA TO UPDATE
                    'category' => $category,
                    'unit_price' => $unitPrice,
                    'in_stock' => $inStock,
                    'reorder_point' => $reorderPoint,
                    'status' => $status,
                ]
            );
        }
    }
}