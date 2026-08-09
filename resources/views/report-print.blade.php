<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $report->report_name ?? 'Business Report' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <style>
        @media print {
            @page { margin: 1cm; }
            body { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body class="bg-gray-100 p-8 font-sans">
    
    <div id="status-message" class="text-center mb-6 bg-blue-50 text-blue-800 p-4 rounded-lg font-bold border border-blue-200 shadow-sm transition-colors duration-300 max-w-4xl mx-auto">
        <i class="fa-solid fa-spinner animate-spin mr-2"></i> Generating your PDF document... Please wait.
    </div>

    <div id="report-document" class="bg-white p-10 max-w-4xl mx-auto shadow-md border border-gray-200">
        <div class="text-center border-b-2 border-black pb-6 mb-8">
            <h1 class="text-3xl font-bold uppercase tracking-wider">Ken's Marketing</h1>
            <p class="text-sm mt-1">Decision Support System & Master Ledger</p>
            <h2 class="text-xl font-semibold mt-4">{{ $report->report_name }}</h2>
            <p class="text-sm mt-1 text-gray-600">Generated: {{ \Carbon\Carbon::parse($report->created_at)->format('F j, Y, g:i a') }}</p>
        </div>

        <!-- 1. INVENTORY AUDIT TABLE -->
        @if($type == 'inventory_audit')
            <table class="w-full text-sm text-left border-collapse border border-gray-400">
                <thead>
                    <tr class="bg-gray-200">
                        <th class="border border-gray-400 px-3 py-2 font-bold">SKU</th>
                        <th class="border border-gray-400 px-3 py-2 font-bold">Product Name</th>
                        <th class="border border-gray-400 px-3 py-2 font-bold text-center">Category</th>
                        <th class="border border-gray-400 px-3 py-2 font-bold text-right">Unit Price</th>
                        <th class="border border-gray-400 px-3 py-2 font-bold text-center">In Stock</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data as $item)
                        <tr>
                            <td class="border border-gray-400 px-3 py-2 font-mono text-xs">{{ $item->sku }}</td>
                            <td class="border border-gray-400 px-3 py-2">{{ $item->product_name }}</td>
                            <td class="border border-gray-400 px-3 py-2 text-center">{{ $item->category }}</td>
                            <td class="border border-gray-400 px-3 py-2 text-right">₱{{ number_format($item->unit_price, 2) }}</td>
                            <td class="border border-gray-400 px-3 py-2 text-center font-bold {{ $item->in_stock == 0 ? 'text-red-600' : '' }}">{{ $item->in_stock }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="border border-gray-400 px-3 py-8 text-center italic text-gray-500">No inventory data available.</td></tr>
                    @endforelse
                </tbody>
            </table>

        <!-- 2. FAST/SLOW MOVING TABLE (WITH DSS STATUS) -->
        @elseif($type == 'fast_slow')
            <table class="w-full text-sm text-left border-collapse border border-gray-400">
                <thead>
                    <tr class="bg-gray-200">
                        <th class="border border-gray-400 px-3 py-2 font-bold text-center">Rank</th>
                        <th class="border border-gray-400 px-3 py-2 font-bold">Product Name</th>
                        <th class="border border-gray-400 px-3 py-2 font-bold text-center">Total Qty Sold</th>
                        <th class="border border-gray-400 px-3 py-2 font-bold text-right">Gross Revenue Generated</th>
                        <th class="border border-gray-400 px-3 py-2 font-bold text-center">DSS Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data as $index => $item)
                        <!-- Highlights the row slightly red if it's dead stock -->
                        <tr class="{{ $item->total_qty == 0 ? 'bg-red-50' : '' }}">
                            <td class="border border-gray-400 px-3 py-2 text-center font-bold">#{{ $index + 1 }}</td>
                            <td class="border border-gray-400 px-3 py-2">{{ $item->product_name }}</td>
                            <td class="border border-gray-400 px-3 py-2 text-center font-bold {{ $item->total_qty == 0 ? 'text-red-600' : '' }}">{{ $item->total_qty }}</td>
                            <td class="border border-gray-400 px-3 py-2 text-right">₱{{ number_format($item->total_revenue, 2) }}</td>
                            <td class="border border-gray-400 px-3 py-2 text-center font-semibold">
                                @if($index < 3 && $item->total_qty > 0)
                                    <span class="text-green-600">Fast Moving</span>
                                @elseif($item->total_qty == 0)
                                    <span class="text-red-600">Dead Stock / Slow</span>
                                @else
                                    <span class="text-yellow-600">Average</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="border border-gray-400 px-3 py-8 text-center italic text-gray-500">No products found in database.</td></tr>
                    @endforelse
                </tbody>
            </table>

        <!-- 3. PROFIT MARGIN TABLE -->
        @elseif($type == 'profit_margin')
            <table class="w-full text-sm text-left border-collapse border border-gray-400">
                <thead>
                    <tr class="bg-gray-200">
                        <th class="border border-gray-400 px-3 py-2 font-bold">Date</th>
                        <th class="border border-gray-400 px-3 py-2 font-bold">Item Sold</th>
                        <th class="border border-gray-400 px-3 py-2 font-bold text-right">Gross Revenue</th>
                        <th class="border border-gray-400 px-3 py-2 font-bold text-right text-red-700">Est. Cost (70%)</th>
                        <th class="border border-gray-400 px-3 py-2 font-bold text-right text-green-700">Net Profit (30%)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data as $sale)
                        @php 
                            $rev = $sale->total_amount;
                            $profit = $rev * 0.30;
                            $cost = $rev - $profit;
                        @endphp
                        <tr>
                            <td class="border border-gray-400 px-3 py-2">{{ $sale->created_at->format('M d, Y') }}</td>
                            <td class="border border-gray-400 px-3 py-2">{{ $sale->product->product_name ?? 'N/A' }}</td>
                            <td class="border border-gray-400 px-3 py-2 text-right font-medium">₱{{ number_format($rev, 2) }}</td>
                            <td class="border border-gray-400 px-3 py-2 text-right text-red-600">- ₱{{ number_format($cost, 2) }}</td>
                            <td class="border border-gray-400 px-3 py-2 text-right text-green-600 font-bold">+ ₱{{ number_format($profit, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="border border-gray-400 px-3 py-8 text-center italic text-gray-500">No sales data available.</td></tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="bg-gray-100 font-bold">
                        <td colspan="4" class="border border-gray-400 px-3 py-2 text-right">TOTAL NET PROFIT:</td>
                        <td class="border border-gray-400 px-3 py-2 text-right text-green-700">₱{{ number_format($data->sum('total_amount') * 0.30, 2) }}</td>
                    </tr>
                </tfoot>
            </table>

        <!-- 4. STANDARD SALES TABLE -->
        @else
            <table class="w-full text-sm text-left border-collapse border border-gray-400">
                <thead>
                    <tr class="bg-gray-200">
                        <th class="border border-gray-400 px-3 py-2 font-bold">Date</th>
                        <th class="border border-gray-400 px-3 py-2 font-bold">Item Sold</th>
                        <th class="border border-gray-400 px-3 py-2 font-bold text-center">Qty</th>
                        <th class="border border-gray-400 px-3 py-2 font-bold text-right">Amount (PHP)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data as $sale)
                        <tr>
                            <td class="border border-gray-400 px-3 py-2">{{ $sale->created_at->format('M d, Y') }}</td>
                            <td class="border border-gray-400 px-3 py-2">{{ $sale->product->product_name ?? 'N/A' }}</td>
                            <td class="border border-gray-400 px-3 py-2 text-center">{{ $sale->quantity_sold }}</td>
                            <td class="border border-gray-400 px-3 py-2 text-right">₱{{ number_format($sale->total_amount, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="border border-gray-400 px-3 py-8 text-center italic text-gray-500">No sales data available for this timeframe.</td></tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="bg-gray-100 font-bold">
                        <td colspan="3" class="border border-gray-400 px-3 py-2 text-right">TOTAL REVENUE:</td>
                        <td class="border border-gray-400 px-3 py-2 text-right">₱{{ number_format($data->sum('total_amount'), 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        @endif

        <div class="grid grid-cols-2 gap-8 mt-16 text-sm">
            <div class="text-center"><div class="border-b border-black w-48 mx-auto mb-2"></div><p class="font-bold">Prepared By</p><p class="text-gray-600">{{ $report->prepared_by }}</p></div>
            <div class="text-center"><div class="border-b border-black w-48 mx-auto mb-2"></div><p class="font-bold">Noted By</p><p class="text-gray-600">Store Manager</p></div>
        </div>
    </div>

    <!-- Script to Auto-Download the PDF -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const element = document.getElementById('report-document');
            const opt = {
                margin:       0.5,
                filename:     '{{ str_replace(" ", "_", $report->report_name) }}_{{ time() }}.pdf',
                image:        { type: 'jpeg', quality: 0.98 },
                html2canvas:  { scale: 2 },
                jsPDF:        { unit: 'in', format: 'letter', orientation: 'portrait' }
            };

            html2pdf().set(opt).from(element).save().then(() => {
                let statusBox = document.getElementById('status-message');
                statusBox.innerHTML = '<i class="fa-solid fa-check-circle mr-2"></i> PDF Downloaded Successfully! You can now close this tab.';
                statusBox.classList.replace('bg-blue-50', 'bg-green-50');
                statusBox.classList.replace('text-blue-800', 'text-green-800');
                statusBox.classList.replace('border-blue-200', 'border-green-200');
            });
        });
    </script>
</body>
</html>