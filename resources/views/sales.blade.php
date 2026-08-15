@extends('layouts.app')

@section('title', 'Sales Module - Ken\'s Marketing')
@section('header_title', 'Sales Management')

@section('content')

<!-- ADD SELECT2 CSS FOR SEARCHABLE DROPDOWN -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    /* Custom styling to make Select2 match Tailwind's design */
    .select2-container .select2-selection--single {
        height: 38px !important;
        border-color: #e5e7eb !important;
        border-radius: 0.5rem !important;
        background-color: #f9fafb !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 38px !important;
        color: #4b5563 !important;
        font-size: 0.875rem !important;
        padding-left: 12px !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px !important;
    }
    .select2-dropdown {
        border-color: #e5e7eb !important;
        border-radius: 0.5rem !important;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06) !important;
    }
    .select2-search__field {
        border-radius: 0.25rem !important;
        outline: none !important;
    }
    .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background-color: #17507E !important; /* Navy 900 */
    }
    
    /* Custom Scrollbar for the locked table */
    .locked-table-scroll::-webkit-scrollbar { width: 6px; height: 6px; }
    .locked-table-scroll::-webkit-scrollbar-track { background: transparent; }
    .locked-table-scroll::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
    .locked-table-scroll::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
</style>

<div class="space-y-6">
    
    <div class="animate-fade-in mb-6">
        <p class="text-gray-500 text-sm">Manage and track your daily transactions.</p>
    </div>

    <!-- ========================================== -->
    <!-- MINIMALIST TOAST NOTIFICATIONS             -->
    <!-- ========================================== -->
    @if(session('success'))
        <div id="toast-success" class="fixed top-16 right-6 z-50 flex items-center w-full max-w-sm p-4 bg-white border border-gray-100 rounded-2xl shadow-xl animate-fade-in" role="alert">
            <div class="inline-flex items-center justify-center shrink-0 w-8 h-8 text-green-600 bg-green-50 rounded-lg">
                <i class="fa-solid fa-check"></i>
            </div>
            <div class="ml-3 text-sm font-medium text-gray-700 pr-4">{{ session('success') }}</div>
            <button type="button" onclick="closeToast('toast-success')" class="ml-auto -mx-1.5 -my-1.5 bg-white text-gray-400 hover:text-gray-900 rounded-lg p-1.5 hover:bg-gray-50 inline-flex items-center justify-center h-8 w-8 transition-colors">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    @endif

    @if($errors->any())
        <div id="toast-error" class="fixed top-24 right-6 z-50 flex items-center w-full max-w-sm p-4 bg-white border border-gray-100 rounded-2xl shadow-xl animate-fade-in" role="alert">
            <div class="inline-flex items-center justify-center shrink-0 w-8 h-8 text-red-600 bg-red-50 rounded-lg">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
            <div class="ml-3 text-sm font-medium text-gray-700 pr-4">{{ $errors->first() }}</div>
            <button type="button" onclick="closeToast('toast-error')" class="ml-auto -mx-1.5 -my-1.5 bg-white text-gray-400 hover:text-gray-900 rounded-lg p-1.5 hover:bg-gray-50 inline-flex items-center justify-center h-8 w-8 transition-colors">
                <i class="fa-solid fa-xmark"></i>
            </button>
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
                        <!-- ADDED 'searchable-select' CLASS FOR JAVASCRIPT TARGETING -->
                        <select id="productSelect" name="product_id" class="searchable-select w-full" required onchange="updateMaxQuantity(); calculateTotal();">
                            <option value="" disabled selected>Choose item...</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}" data-price="{{ $product->unit_price }}" data-stock="{{ $product->in_stock }}">
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
    <div id="recent-sales-container" class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden animate-fade-in delay-200">
        <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-white">
            <h2 class="text-lg font-semibold text-gray-800">Recent Sales Ledger</h2>
            <span class="bg-green-100 text-green-700 text-xs font-bold px-3 py-1 rounded-full">Live Updates</span>
        </div>
        
        <!-- ULTIMATE CSS FIX: Exact locked height guarantees 0 layout shift. Inner scroll handles overflow -->
        <div class="overflow-x-auto overflow-y-auto locked-table-scroll" style="height: 700px;">
            <table class="w-full text-sm text-left text-gray-500 relative">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 sticky top-0 z-10 shadow-sm border-b border-gray-200">
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
        
        <!-- Pagination sits securely below the locked table block -->
        <div class="border-t border-gray-100 bg-gray-50 rounded-b-2xl">
            @if(method_exists($recentSales, 'links'))
                <div class="p-4 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div class="text-sm text-gray-500 font-medium">
                        Page <span class="text-navy-900 font-bold">{{ $recentSales->currentPage() }}</span> of <span class="text-navy-900 font-bold">{{ $recentSales->lastPage() }}</span>
                    </div>
                    <div class="w-full sm:w-auto overflow-x-auto">
                        {{ $recentSales->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- ADD SELECT2 JS SCRIPT -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    // --- INITIALIZE SEARCHABLE DROPDOWN ---
    $(document).ready(function() {
        $('.searchable-select').select2({
            placeholder: "Search or select an item...",
            allowClear: true,
            width: '100%' 
        });
        
        $('.searchable-select').on('change', function() {
            updateMaxQuantity();
            calculateTotal();
        });
    });

    // --- TOAST NOTIFICATION LOGIC ---
    function closeToast(id) {
        const toast = document.getElementById(id);
        if (toast) {
            toast.style.opacity = '0';
            toast.style.transform = 'translateY(-10px)';
            toast.style.transition = 'all 0.3s ease-out';
            setTimeout(() => toast.remove(), 300); 
        }
    }

    document.addEventListener("DOMContentLoaded", function() {
        if (document.getElementById('toast-success')) {
            setTimeout(() => closeToast('toast-success'), 10000);
        }
        
        if (document.getElementById('toast-error')) {
            setTimeout(() => closeToast('toast-error'), 10000);
        }
    });

    // --- LIVE MATH CALCULATION FOR POS FORM ---
    function calculateTotal() {
        const select = document.getElementById('productSelect');
        const qtyInput = document.getElementById('qtyInput');
        const totalDueDisplay = document.getElementById('totalDueDisplay');

        if (qtyInput.value !== "" && qtyInput.value < 1) {
            qtyInput.value = 1;
        }

        if (select.selectedIndex > 0 && qtyInput.value > 0) {
            const price = parseFloat(select.options[select.selectedIndex].getAttribute('data-price'));
            const stock = parseInt(select.options[select.selectedIndex].getAttribute('data-stock'));
            let currentQty = parseInt(qtyInput.value);

            if (currentQty > stock) {
                alert("Warning: You cannot sell more than the current available stock (" + stock + " units).");
                qtyInput.value = stock;
                currentQty = stock; 
            }

            const formattedTotal = (price * currentQty).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            totalDueDisplay.value = formattedTotal;
        } else {
            totalDueDisplay.value = '0.00';
        }
    }

    function updateMaxQuantity() {
        const select = document.getElementById('productSelect');
        const qtyInput = document.getElementById('qtyInput');
        
        if (select.selectedIndex > 0) {
            const stock = select.options[select.selectedIndex].getAttribute('data-stock');
            qtyInput.max = stock; 
            qtyInput.value = 1; 
        }
    }

    // --- LIVE CHART LOGIC ---
    document.addEventListener("DOMContentLoaded", function() {
        const ctx = document.getElementById('salesVelocityChart').getContext('2d');
        const liveChartData = @json($chartData);

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['8 AM', '10 AM', '12 PM', '2 PM', '4 PM', '6 PM'],
                datasets: [{
                    label: 'Sales Vol (₱)',
                    data: liveChartData, 
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

    // --- BULLETPROOF AJAX PAGINATION (NO JUMPS) ---
    
    let currentFetchId = 0; 
    let currentAbortController = null;

    function performAjaxFetch(url) {
        const tableContainer = document.getElementById('recent-sales-container');
        if(!tableContainer) return;
        
        const fetchId = ++currentFetchId;
        
        if (currentAbortController) {
            currentAbortController.abort();
        }
        currentAbortController = new AbortController();
        
        // Disable pointer events to cleanly prevent race-condition double clicks
        tableContainer.style.pointerEvents = 'none'; 
        tableContainer.style.opacity = '0.5';
        tableContainer.style.transition = 'opacity 0.2s ease-in-out';
        
        fetch(url, { signal: currentAbortController.signal })
            .then(response => response.text())
            .then(html => {
                if (fetchId !== currentFetchId) return;

                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                
                // Swap purely the inner HTML
                tableContainer.innerHTML = doc.getElementById('recent-sales-container').innerHTML;
                
                // Re-enable clicks and restore opacity
                tableContainer.style.pointerEvents = 'auto'; 
                tableContainer.style.opacity = '1';
                
                window.history.pushState({}, '', url);
            })
            .catch(error => {
                tableContainer.style.pointerEvents = 'auto'; 
                if (error.name !== 'AbortError') {
                    window.location.href = url; 
                }
            });
    }

    document.addEventListener("DOMContentLoaded", function() {
        const tableContainer = document.getElementById('recent-sales-container');

        if (tableContainer) {
            tableContainer.addEventListener('click', function(e) {
                const link = e.target.closest('a');
                
                if (link && link.href && link.href.includes('page=')) {
                    e.preventDefault(); 
                    performAjaxFetch(link.href);
                }
            });
        }
    });
</script>
@endsection