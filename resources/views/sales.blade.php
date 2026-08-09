@extends('layouts.app')

@section('title', 'Sales Module - Ken\'s Marketing')
@section('header_title', 'Sales Management')

@section('content')

<div class="space-y-6 print:hidden">
    
    <div class="flex justify-between items-end animate-fade-in">
        <div>
            <p class="text-gray-500 text-sm">Manage and track your daily transactions.</p>
        </div>
        <div class="flex gap-3">
            <button onclick="window.print()" class="px-4 py-2 bg-navy-900 text-white rounded-lg text-sm font-medium hover:bg-navy-700 hover:shadow-lg transition-all duration-200 flex items-center gap-2">
                <i class="fa-solid fa-print"></i> Generate Formal Report
            </button>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- SYSTEM ALERTS (SUCCESS & ERROR)            -->
    <!-- ========================================== -->
    @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-r-lg shadow-sm animate-fade-in my-4">
            <div class="flex items-center">
                <i class="fa-solid fa-circle-check text-green-500 mr-3 text-lg"></i>
                <p class="text-green-800 font-medium text-sm">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    @if($errors->any())
        <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg shadow-sm animate-fade-in my-4">
            <div class="flex items-center mb-2">
                <i class="fa-solid fa-triangle-exclamation text-red-500 mr-3 text-lg"></i>
                <p class="text-red-800 font-bold text-sm">Transaction Failed!</p>
            </div>
            <ul class="list-disc list-inside text-xs text-red-700 ml-7 font-medium">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <!-- ========================================== -->
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 animate-fade-in">
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:-translate-y-1 hover:shadow-lg transition-all duration-300">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-3">
                    <div class="bg-blue-50 p-2 rounded-lg text-blue-600"><i class="fa-solid fa-calendar-day"></i></div>
                    <h3 class="font-medium text-gray-500 text-sm">Today's Sales</h3>
                </div>
            </div>
            <h2 class="text-3xl font-bold text-gray-800 tracking-tight">₱{{ number_format($todaySales, 2) }}</h2>
            <p class="text-xs text-gray-400 mt-2">Live total for today</p>
        </div>

        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:-translate-y-1 hover:shadow-lg transition-all duration-300">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-3">
                    <div class="bg-indigo-50 p-2 rounded-lg text-indigo-600"><i class="fa-solid fa-calendar-week"></i></div>
                    <h3 class="font-medium text-gray-500 text-sm">This Week</h3>
                </div>
            </div>
            <h2 class="text-3xl font-bold text-gray-800 tracking-tight">₱{{ number_format($weekSales, 2) }}</h2>
            <p class="text-xs text-gray-400 mt-2">Current week accumulated</p>
        </div>

        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:-translate-y-1 hover:shadow-lg transition-all duration-300">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-3">
                    <div class="bg-purple-50 p-2 rounded-lg text-purple-600"><i class="fa-solid fa-calendar-days"></i></div>
                    <h3 class="font-medium text-gray-500 text-sm">This Month</h3>
                </div>
            </div>
            <h2 class="text-3xl font-bold text-gray-800 tracking-tight">₱{{ number_format($monthSales, 2) }}</h2>
            <p class="text-xs text-gray-400 mt-2">Current month accumulated</p>
        </div>

        <!-- ========================================== -->
        <!-- DYNAMIC WEEKEND FORECAST CARD              -->
        <!-- ========================================== -->
        <div class="bg-gradient-to-br from-navy-900 to-navy-700 rounded-2xl p-6 shadow-sm border border-navy-700 text-white hover:-translate-y-1 hover:shadow-lg transition-all duration-300 relative overflow-hidden group">
            <div class="absolute right-0 top-0 opacity-10 transform translate-x-2 -translate-y-2 group-hover:scale-110 transition-transform duration-500">
                <i class="fa-solid fa-wand-magic-sparkles text-8xl"></i>
            </div>
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-3">
                    <div class="bg-white/20 p-2 rounded-lg text-yellow-300"><i class="fa-solid fa-chart-line"></i></div>
                    <h3 class="font-medium text-blue-100 text-sm"> Weekend Forecast</h3>
                </div>
            </div>
            <h2 class="text-3xl font-bold tracking-tight mb-2">{{ $forecastValue }}</h2>
            <p class="text-xs text-gray-300 leading-relaxed">{{ $forecastText }}</p>
        </div>
        <!-- ========================================== -->

    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 animate-fade-in delay-100">
        <!-- ========================================== -->
        <!-- LIVE POS FORM                              -->
        <!-- ========================================== -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden lg:col-span-1 flex flex-col">
            <div class="bg-navy-900 text-white p-4 border-b border-navy-700">
                <h2 class="text-md font-semibold flex items-center gap-2">
                    <i class="fa-solid fa-cart-plus"></i> Record Sale
                </h2>
            </div>
            <form action="{{ route('sales.store') }}" method="POST" class="p-4 space-y-4 flex-1 flex flex-col justify-between">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Customer / Reference</label>
                        <input type="text" placeholder="e.g. Walk-in or Name" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-navy-700 bg-gray-50 focus:bg-white transition-colors">
                    </div>
                    
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Select Item from Inventory</label>
                        <select id="productSelect" name="product_id" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-navy-700 bg-gray-50 focus:bg-white cursor-pointer transition-colors" required onchange="calculateTotal()">
                            <option value="" disabled selected>Choose item...</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}" data-price="{{ $product->unit_price }}">
                                    {{ $product->product_name }} (Stock: {{ $product->in_stock }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Qty</label>
                            <input type="number" id="qtyInput" name="quantity_sold" min="1" value="1" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-navy-700 bg-gray-50 focus:bg-white transition-colors" required oninput="calculateTotal()">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Total (₱)</label>
                            <!-- This is readonly because JS calculates it automatically -->
                            <input type="text" id="totalDueDisplay" placeholder="0.00" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm bg-gray-100 text-green-700 font-bold" readonly>
                        </div>
                    </div>
                </div>
                <div class="pt-2 mt-4">
                    <button type="submit" class="w-full bg-navy-900 text-white text-sm font-medium py-2.5 rounded-lg hover:bg-navy-700 hover:shadow-lg transition-all duration-200 flex justify-center items-center gap-2">
                        <i class="fa-solid fa-check"></i> Process Sale
                    </button>
                </div>
            </form>
        </div>

        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 lg:col-span-2 flex flex-col hover:shadow-lg transition-shadow duration-300">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-md font-semibold text-gray-800">Peak Hour Tracer</h2>
                <div class="flex gap-2 bg-gray-100 p-1 rounded-lg">
                    <button class="px-3 py-1 text-xs font-medium bg-white text-navy-900 rounded-md shadow-sm">Daily</button>
                    <button class="px-3 py-1 text-xs font-medium text-gray-500 hover:text-gray-700 transition-colors">Weekly</button>
                </div>
            </div>
            <div class="relative flex-1 w-full min-h-[200px]">
                <canvas id="salesVelocityChart"></canvas>
            </div>
        </div>

        <!-- ========================================== -->
        <!-- LIVE TOP SELLERS                           -->
        <!-- ========================================== -->
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 lg:col-span-1 flex flex-col">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-md font-semibold text-gray-800">Top Sellers</h2>
                <span class="text-xs text-green-600 bg-green-50 px-2 py-1 rounded font-medium">This Week</span>
            </div>
            <div class="space-y-4 flex-1">
                @forelse($topSellers as $index => $seller)
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded bg-navy-50 text-navy-900 font-bold flex items-center justify-center text-xs">
                            {{ $index + 1 }}
                        </div>
                        <div class="flex-1">
                            <h4 class="text-sm font-semibold text-gray-800 line-clamp-1">{{ $seller->product->product_name ?? 'Unknown Item' }}</h4>
                            <p class="text-xs text-gray-500">{{ $seller->total_qty }} units sold</p>
                        </div>
                        <div class="text-sm font-bold text-gray-900">₱{{ number_format($seller->total_revenue, 0) }}</div>
                    </div>
                @empty
                    <div class="text-center text-gray-400 text-xs py-4">No sales recorded this week.</div>
                @endforelse
            </div>
            <a href="{{ route('inventory.index') }}" class="mt-4 text-center text-xs text-navy-700 font-medium hover:underline block">View Full Inventory &rarr;</a>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- LIVE SALES LEDGER TABLE                    -->
    <!-- ========================================== -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden animate-fade-in delay-200">
        <div class="p-6 border-b border-gray-100 flex justify-between items-center">
            <h2 class="text-lg font-semibold text-gray-800">Recent Sales Ledger</h2>
            <span class="bg-green-100 text-green-700 text-xs font-bold px-3 py-1 rounded-full">Live Updates</span>
        </div>
        <div class="overflow-x-auto max-h-[400px]">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 sticky top-0 shadow-sm">
                    <tr>
                        <th scope="col" class="px-6 py-4 font-semibold">Receipt No.</th>
                        <th scope="col" class="px-6 py-4 font-semibold">Date & Time</th>
                        <th scope="col" class="px-6 py-4 font-semibold">Customer</th>
                        <th scope="col" class="px-6 py-4 font-semibold">Purchased Items</th>
                        <th scope="col" class="px-6 py-4 font-semibold text-right">Total Amount</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($recentSales as $sale)
                    <tr class="bg-white hover:bg-gray-50 transition-colors duration-200">
                        <td class="px-6 py-4 font-medium text-navy-700">#RC-{{ str_pad($sale->id, 5, '0', STR_PAD_LEFT) }}</td>
                        <td class="px-6 py-4 text-xs">{{ $sale->created_at->format('M d, Y') }} <br> <span class="text-gray-400">{{ $sale->created_at->format('h:i A') }}</span></td>
                        <td class="px-6 py-4 text-gray-900 font-medium">Walk-in</td>
                        <td class="px-6 py-4 text-xs font-bold">{{ $sale->quantity_sold }}x <span class="font-normal">{{ $sale->product->product_name ?? 'Deleted Item' }}</span></td>
                        <td class="px-6 py-4 font-bold text-green-600 text-right">₱{{ number_format($sale->total_amount, 2) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-500">No sales transactions recorded yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- YOUR EXISTING PRINT SECTION REMAINS INTACT -->
<!-- ========================================== -->
<div class="hidden print:block text-black bg-white w-full">
    <div class="text-center border-b-2 border-black pb-6 mb-8">
        <h1 class="text-3xl font-bold uppercase tracking-wider text-black">Ken's Marketing</h1>
        <p class="text-sm mt-1">Ligao City, Bicol, Philippines</p>
        <h2 class="text-xl font-semibold mt-4">DSS Official Sales & Performance Report</h2>
        <p class="text-sm mt-1">Report Generated: {{ date('F j, Y, g:i a') }}</p>
    </div>

    <!-- ... rest of your print HTML ... -->
    <!-- (I left this untouched so your print layout doesn't break!) -->
</div>

<style>
    @media print {
        @page { margin: 1.5cm; }
        body * { visibility: hidden; }
        .print\:block, .print\:block * { visibility: visible; }
        .print\:block {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }
        #sidebar, header { display: none !important; }
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // --- LIVE MATH CALCULATION FOR POS FORM ---
    function calculateTotal() {
        const select = document.getElementById('productSelect');
        const qty = document.getElementById('qtyInput').value;
        const totalDueDisplay = document.getElementById('totalDueDisplay');

        if (select.selectedIndex > 0 && qty > 0) {
            const price = parseFloat(select.options[select.selectedIndex].getAttribute('data-price'));
            const formattedTotal = (price * qty).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            totalDueDisplay.value = formattedTotal;
        } else {
            totalDueDisplay.value = '0.00';
        }
    }

    // --- LIVE CHART LOGIC ---
    document.addEventListener("DOMContentLoaded", function() {
        const ctx = document.getElementById('salesVelocityChart').getContext('2d');
        
        // Securely load the PHP array into JS
        const liveChartData = @json($chartData);

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['8 AM', '10 AM', '12 PM', '2 PM', '4 PM', '6 PM'],
                datasets: [{
                    label: 'Sales Vol (₱)',
                    data: liveChartData, // Live data from the database!
                    borderColor: '#17507E',
                    backgroundColor: 'rgba(23, 80, 126, 0.1)',
                    borderWidth: 3,
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: '#012C55',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: { duration: 1000, easing: 'easeOutQuart' },
                plugins: { legend: { display: false }, tooltip: { backgroundColor: '#012C55', padding: 12, cornerRadius: 8 } },
                scales: {
                    y: { beginAtZero: true, grid: { color: '#f3f4f6', drawBorder: false }, border: { display: false }, ticks: { color: '#9ca3af', font: { family: 'Inter' }, maxTicksLimit: 6 } },
                    x: { grid: { display: false }, border: { display: false }, ticks: { color: '#6b7280', font: { family: 'Inter' } } }
                }
            }
        });
    });
</script>
@endsection