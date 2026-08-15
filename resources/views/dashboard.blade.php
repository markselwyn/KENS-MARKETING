@extends('layouts.app')

@section('title', 'Dashboard - Ken\'s Marketing')
@section('header_title', 'Dashboard Overview')

@section('content')

<style>
    @media print {
        #sidebar, header, .no-print { display: none !important; }
        body, main, .bg-\[\#F9FAFB\] { background-color: white !important; height: auto !important; overflow: visible !important; padding: 0 !important; margin: 0 !important; }
        .shadow-sm, .shadow-lg, .hover\:shadow-lg { box-shadow: none !important; border: 1px solid #e5e7eb !important; transform: none !important; }
        .print-header { display: block !important; text-align: center; margin-bottom: 2rem; border-bottom: 2px solid #012C55; padding-bottom: 1rem; }
        .lg\:col-span-2 { grid-column: span 3 / span 3 !important; }
        .print-break { page-break-before: always; }
        * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
    }
    .print-header { display: none; }
</style>

<div class="space-y-6">
    <div class="print-header text-black">
        <h1 class="text-3xl font-bold uppercase tracking-wider">Ken's Marketing</h1>
        <p class="text-sm mt-1">Executive Dashboard Snapshot</p>
        <p class="text-sm mt-1 text-gray-600">Generated on: {{ date('F j, Y, g:i a') }}</p>
    </div>

    <div class="animate-fade-in no-print mb-2">
        <p class="text-gray-500 text-sm">Here's what's happening with your store today.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 animate-fade-in delay-100">
        
        <!-- GROSS REVENUE -->
        <div class="bg-navy-900 rounded-2xl p-6 text-white shadow-sm border border-navy-700 relative overflow-hidden group hover:-translate-y-1 hover:shadow-lg transition-all duration-300 cursor-pointer print:bg-white print:text-black print:border-gray-300">
            <div class="absolute right-0 top-0 opacity-10 transform translate-x-4 -translate-y-4 group-hover:scale-110 transition-transform duration-500 ease-out no-print">
                <i class="fa-solid fa-chart-line text-8xl"></i>
            </div>
            <div class="flex items-center gap-3 mb-4">
                <div class="bg-white/20 p-2 rounded-lg print:bg-gray-100 print:text-gray-600"><i class="fa-solid fa-wallet text-white print:text-gray-600 text-sm"></i></div>
                <h3 class="font-medium text-blue-100 text-sm print:text-gray-600">Gross Revenue</h3>
            </div>
            <h2 class="text-3xl font-bold tracking-tight mb-1 truncate" title="₱{{ number_format($grossRevenue, 2) }}">₱{{ number_format($grossRevenue, 2) }}</h2>
            <p class="text-xs text-blue-200 font-medium print:text-gray-500">Updated just now</p>
        </div>

        <!-- NET PROFIT -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:-translate-y-1 hover:shadow-lg transition-all duration-300 cursor-pointer print:border-gray-300">
            <div class="flex items-center gap-3 mb-4">
                <div class="bg-green-100 p-2 rounded-lg"><i class="fa-solid fa-money-bill-trend-up text-green-600 text-sm"></i></div>
                <h3 class="font-medium text-gray-500 text-sm">Net Profit</h3>
            </div>
            <h2 class="text-3xl font-bold text-gray-800 tracking-tight mb-1 truncate" title="₱{{ number_format($netProfit, 2) }}">₱{{ number_format($netProfit, 2) }}</h2>
            <p class="text-xs text-green-500 font-medium"><i class="fa-solid fa-arrow-up mr-1"></i> Est. 30% Margin</p>
        </div>

        <!-- AVG ORDER VALUE -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:-translate-y-1 hover:shadow-lg transition-all duration-300 cursor-pointer print:border-gray-300">
            <div class="flex items-center gap-3 mb-4">
                <div class="bg-blue-100 p-2 rounded-lg"><i class="fa-solid fa-basket-shopping text-blue-600 text-sm"></i></div>
                <h3 class="font-medium text-gray-500 text-sm">Avg. Order Value</h3>
            </div>
            <h2 class="text-3xl font-bold text-gray-800 tracking-tight mb-1 truncate" title="₱{{ number_format($avgOrderValue, 2) }}">₱{{ number_format($avgOrderValue, 2) }}</h2>
            <p class="text-xs text-gray-400 font-medium">Based on {{ $totalOrders ?? 0 }} orders</p>
        </div>

        <!-- RESTOCK REQUIRED WITH AUTO-FILTER LINK -->
        <div class="bg-white rounded-2xl p-6 shadow-sm {{ $lowStockCount > 0 ? 'border-red-100' : 'border-gray-100' }} hover:-translate-y-1 hover:shadow-lg transition-all duration-300 relative overflow-hidden group print:border-gray-300 flex flex-col justify-between cursor-pointer">
            @if($lowStockCount > 0)
                <div class="absolute left-0 top-0 w-1 h-full bg-red-500 group-hover:w-2 transition-all duration-300 no-print"></div>
            @else
                <div class="absolute left-0 top-0 w-1 h-full bg-green-500 group-hover:w-2 transition-all duration-300 no-print"></div>
            @endif
            
            <div>
                <div class="flex items-center gap-3 mb-3">
                    <div class="{{ $lowStockCount > 0 ? 'bg-red-50 text-red-500' : 'bg-green-50 text-green-500' }} p-2 rounded-lg">
                        <i class="fa-solid {{ $lowStockCount > 0 ? 'fa-triangle-exclamation' : 'fa-check-circle' }} text-sm"></i>
                    </div>
                    <h3 class="font-medium text-gray-500 text-sm">Restock Required</h3>
                </div>
                <h2 class="text-3xl font-bold text-gray-800 tracking-tight mb-1">{{ $lowStockCount }} <span class="text-base font-normal text-gray-500 tracking-normal">items</span></h2>
                <p class="text-xs text-gray-500 line-clamp-1">Priority: <span class="font-medium {{ $lowStockCount > 0 ? 'text-red-600' : 'text-green-600' }}">{{ $priorityRestock }}</span></p>
            </div>
            <!-- LINK UPDATED HERE -->
            <a href="/inventory?status=limited_stock" class="mt-4 text-xs text-center block w-full {{ $lowStockCount > 0 ? 'text-red-600 hover:text-red-800 bg-red-50 group-hover:bg-red-100' : 'text-navy-600 hover:text-navy-800 bg-navy-50 group-hover:bg-navy-100' }} font-medium transition-colors py-2 rounded-lg no-print">
                Review & Restock <i class="fa-solid fa-arrow-right ml-1 transform group-hover:translate-x-1 transition-transform inline-block"></i>
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 animate-fade-in delay-200">
        
        <!-- REVENUE CHART -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 lg:col-span-2 print:border-gray-300">
            <div class="flex justify-between items-start mb-6">
                <div>
                    <h2 class="text-lg font-semibold text-gray-800">Revenue Trend</h2>
                    <p class="text-sm text-gray-500 mt-1 transition-all duration-300 flex items-center" id="revenue-total">
                        ₱{{ number_format($total7, 2) }} 
                        <span class="text-green-500 font-medium bg-green-50 px-2 py-0.5 rounded text-xs ml-2 flex items-center gap-1">
                            <span class="relative flex h-2 w-2">
                              <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                              <span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span>
                            </span>
                            Live
                        </span>
                    </p>
                </div>
                <select id="timeframe-selector" onchange="updateChartData()" class="bg-gray-50 border border-gray-200 text-gray-700 text-sm font-medium rounded-lg focus:ring-navy-700 focus:border-navy-700 block p-2.5 cursor-pointer outline-none hover:bg-gray-100 transition-colors duration-200 shadow-sm no-print">
                    <option value="7days" selected>Last 7 Days</option>
                    <option value="30days">Last 30 Days</option>
                    <option value="year">This Year</option>
                </select>
            </div>
            <div class="relative h-72 w-full">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>

        <!-- REAL DYNAMIC SMART INSIGHTS CARD -->
        <div class="bg-gradient-to-br from-navy-900 to-navy-700 rounded-2xl p-6 shadow-sm border border-navy-700 text-white flex flex-col hover:shadow-lg transition-shadow duration-300 print:bg-white print:text-black print:border-gray-300">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-wand-magic-sparkles text-yellow-400 print:text-black"></i>
                    <h2 class="text-lg font-semibold"> Smart Insights</h2>
                </div>
                <span class="bg-white/20 text-xs px-2 py-1 rounded-md font-medium print:bg-gray-200 print:text-black flex items-center gap-1">
                    Live
                </span>
            </div>
            
            <div class="flex-1 space-y-4">
                <!-- Insight 1: Inventory (UPDATED TERMINOLOGY) -->
                <div class="bg-white/10 rounded-xl p-4 border border-white/20 hover:bg-white/20 transition-colors duration-200 cursor-pointer group print:bg-gray-50 print:border-gray-200">
                    <div class="flex items-start gap-3">
                        <div class="mt-0.5 text-{{ $insight1Color }}-400 group-hover:scale-110 transition-transform print:text-{{ $insight1Color }}-600 text-lg">
                            <i class="fa-solid fa-{{ $insight1Badge === 'Out of Stock' || $insight1Badge === 'Limited Stock' ? 'triangle-exclamation' : 'circle-check' }}"></i>
                        </div>
                        <div class="w-full">
                            <h4 class="text-sm font-semibold text-white print:text-black flex items-center gap-2">
                                {{ $insight1Title }}
                                <span class="text-[9px] px-1.5 py-0.5 rounded uppercase tracking-wider font-bold {{ $insight1Badge === 'Out of Stock' ? 'bg-red-500 text-white' : ($insight1Badge === 'Limited Stock' ? 'bg-orange-500 text-white' : 'bg-green-500 text-white') }}">
                                    {{ $insight1Badge }}
                                </span>
                            </h4>
                            <p class="text-xs text-gray-300 mt-1 print:text-gray-600 leading-relaxed">{!! $insight1Text !!}</p>
                            <button onclick="window.location.href='{{ $insight1Link }}'" class="mt-3 text-xs w-full bg-white text-navy-900 font-bold px-3 py-2 rounded-lg hover:bg-gray-100 transition-colors shadow-sm no-print">
                                {{ $insight1Btn }}
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Insight 2: Sales Trends -->
                <div class="bg-white/10 rounded-xl p-4 border border-white/20 hover:bg-white/20 transition-colors duration-200 cursor-pointer group print:bg-gray-50 print:border-gray-200">
                    <div class="flex items-start gap-3">
                        <div class="mt-0.5 text-yellow-400 group-hover:scale-110 transition-transform print:text-yellow-600 text-lg">
                            <i class="fa-solid fa-lightbulb"></i>
                        </div>
                        <div class="w-full">
                            <h4 class="text-sm font-semibold text-white print:text-black flex items-center gap-2">
                                {{ $insight2Title }}
                                <span class="bg-yellow-500 text-navy-900 text-[9px] px-1.5 py-0.5 rounded uppercase tracking-wider font-bold">Calculated</span>
                            </h4>
                            <p class="text-xs text-gray-300 mt-1 print:text-gray-600 leading-relaxed">{!! $insight2Text !!}</p>
                            <button onclick="window.location.href='{{ $insight2Link }}'" class="mt-3 text-xs w-full bg-white text-navy-900 font-bold px-3 py-2 rounded-lg hover:bg-gray-100 transition-colors shadow-sm no-print flex items-center justify-center gap-2">
                                <i class="fa-solid fa-chart-pie"></i> {{ $insight2Btn }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 print-break">
        
        <!-- CATEGORY VELOCITY -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 animate-fade-in delay-300 print:border-gray-300">
            <h2 class="text-lg font-semibold text-gray-800 mb-6">Category Sales Velocity</h2>
            <div class="space-y-6">
                @php 
                    $colors = ['bg-navy-900', 'bg-blue-600', 'bg-indigo-400', 'bg-sky-300']; 
                @endphp
                
                @forelse($categoryVelocity as $index => $cat)
                    @php
                        $percentage = ($maxCategorySales > 0) ? ($cat->total_sales / $maxCategorySales) * 100 : 0;
                        $barColor = $colors[$index % count($colors)];
                    @endphp
                    <div class="group cursor-default">
                        <div class="flex justify-between text-sm font-medium mb-2">
                            <span class="text-gray-700">{{ $cat->category ?: 'Uncategorized' }}</span>
                            <span class="text-gray-900 font-bold">₱{{ number_format($cat->total_sales, 2) }}</span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-2.5 overflow-hidden">
                            <div class="{{ $barColor }} h-2.5 rounded-full transition-all duration-1000 ease-out print:bg-gray-800" style="width: {{ $percentage }}%"></div>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-500 italic text-center py-4">No categories have sales data yet.</p>
                @endforelse
            </div>
        </div>

        <!-- RECENT TRANSACTIONS -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden animate-fade-in delay-300 print:border-gray-300">
            <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                <h2 class="text-lg font-semibold text-gray-800">Recent Transactions</h2>
                <a href="/sales" class="text-xs text-navy-600 font-medium hover:underline no-print">View Ledger</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-4 font-semibold">Receipt No.</th>
                            <th scope="col" class="px-6 py-4 font-semibold">Purchased Items</th>
                            <th scope="col" class="px-6 py-4 font-semibold">Date</th>
                            <th scope="col" class="px-6 py-4 font-semibold text-right">Amount</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($recentTransactions as $transaction)
                            <tr class="bg-white hover:bg-gray-50 transition-colors duration-200">
                                <td class="px-6 py-4 font-medium text-navy-900">#RC-{{ str_pad($transaction->id, 5, '0', STR_PAD_LEFT) }}</td>
                                <td class="px-6 py-4">
                                    <div class="text-gray-900 font-medium">{{ $transaction->customer_name ?? 'Walk-in Customer' }}</div>
                                    <div class="text-xs text-gray-500 mt-0.5"><i class="fa-solid fa-basket-shopping mr-1"></i> {{ $transaction->quantity_sold ?? 1 }}x {{ $transaction->product->product_name ?? 'Unknown Item' }}</div>
                                </td>
                                <td class="px-6 py-4 text-xs text-gray-400">{{ $transaction->created_at->diffForHumans() }}</td>
                                <td class="px-6 py-4 font-bold text-green-600 text-right">₱{{ number_format($transaction->total_amount, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-gray-500 italic">No recent transactions found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    let myChart; 

    const labels7 = @json($labels7);
    const data7 = @json($data7);
    const text7 = '₱{{ number_format($total7, 2) }} <span class="text-green-500 font-medium bg-green-50 px-2 py-0.5 rounded text-xs ml-2 flex items-center gap-1"><span class="relative flex h-2 w-2"><span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span><span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span></span> Live</span>';

    const labels30 = @json($labels30);
    const data30 = @json($data30);
    const text30 = '₱{{ number_format($total30, 2) }} <span class="text-green-500 font-medium bg-green-50 px-2 py-0.5 rounded text-xs ml-2 flex items-center gap-1"><span class="relative flex h-2 w-2"><span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span><span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span></span> Live</span>';

    const labelsYear = @json($labelsYear);
    const dataYear = @json($dataYear);
    const textYear = '₱{{ number_format($totalYear, 2) }} <span class="text-green-500 font-medium bg-green-50 px-2 py-0.5 rounded text-xs ml-2 flex items-center gap-1"><span class="relative flex h-2 w-2"><span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span><span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span></span> Live</span>';

    document.addEventListener("DOMContentLoaded", function() {
        initChart();
    });

    function initChart() {
        const revCtx = document.getElementById('revenueChart').getContext('2d');
        
        let gradient = revCtx.createLinearGradient(0, 0, 0, 400);
        gradient.addColorStop(0, '#17507E');   
        gradient.addColorStop(1, '#012C55');

        myChart = new Chart(revCtx, {
            type: 'bar',
            data: {
                labels: labels7,
                datasets: [{
                    label: 'Revenue (₱)',
                    data: data7,
                    backgroundColor: gradient,
                    borderRadius: 6,
                    barThickness: 30
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: {
                    duration: 800,
                    easing: 'easeOutQuart'
                },
                plugins: {
                    legend: { display: false },
                    tooltip: { 
                        backgroundColor: '#012C55', 
                        padding: 12, 
                        cornerRadius: 8,
                        callbacks: {
                            label: function(context) {
                                return '₱' + context.raw.toLocaleString();
                            }
                        }
                    }
                },
                scales: {
                    y: { 
                        beginAtZero: true, 
                        grid: { color: '#f3f4f6', drawBorder: false }, 
                        border: { display: false }, 
                        ticks: { 
                            color: '#9ca3af', 
                            font: { family: 'Inter' },
                            callback: function(value) {
                                return value >= 1000 ? (value/1000) + 'k' : value;
                            }
                        } 
                    },
                    x: { grid: { display: false }, border: { display: false }, ticks: { color: '#6b7280', font: { family: 'Inter' } } }
                }
            }
        });
    }

    function updateChartData() {
        const selector = document.getElementById('timeframe-selector').value;
        const totalLabel = document.getElementById('revenue-total');
        
        totalLabel.style.opacity = '0';
        
        setTimeout(() => {
            let newData = [];
            let newLabels = [];
            
            if (selector === '7days') {
                newLabels = labels7;
                newData = data7;
                totalLabel.innerHTML = text7;
            } else if (selector === '30days') {
                newLabels = labels30;
                newData = data30;
                totalLabel.innerHTML = text30;
            } else if (selector === 'year') {
                newLabels = labelsYear;
                newData = dataYear;
                totalLabel.innerHTML = textYear;
            }

            myChart.data.labels = newLabels;
            myChart.data.datasets[0].data = newData;
            myChart.update();
            
            totalLabel.style.opacity = '1';
        }, 200);
    }
</script>
@endsection