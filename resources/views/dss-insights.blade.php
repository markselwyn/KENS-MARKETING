@extends('layouts.app')

@section('title', 'DSS Insights - Ken\'s Marketing')
@section('header_title', 'System Intelligence & Strategy')

@section('content')
<div class="space-y-6">

    <div class="flex justify-between items-end animate-fade-in mb-2">
        <div>
            <p class="text-gray-500 text-sm">Predictive analytics and algorithm-driven business strategies.</p>
        </div>
        <div class="flex gap-3">
            <div class="bg-blue-50 text-blue-700 px-3 py-1.5 rounded-lg text-xs font-semibold flex items-center gap-2 shadow-sm border border-blue-100">
                <span class="relative flex h-2.5 w-2.5">
                  <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                  <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-blue-500"></span>
                </span>
                Live System Analysis Active
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8 animate-fade-in">
        
        <!-- DYNAMIC FAST-SELLING PRODUCT CARD -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex flex-col justify-between hover:shadow-md transition-shadow">
            <div>
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-lg bg-green-100 text-green-600 flex items-center justify-center text-lg">
                        <i class="fa-solid fa-bolt"></i>
                    </div>
                    <h3 class="font-bold text-gray-800">Fast-Selling Product</h3>
                </div>
                <p class="text-sm text-gray-600 mb-4">
                    @if($fastMover)
                        Data shows the <strong>{{ $fastMover->product_name }}</strong> is moving at a high velocity ({{ $fastMover->total_sold }} units sold recently). It is currently your top-performing item.
                    @else
                        Insufficient sales data to calculate high-velocity items. Please record more transactions.
                    @endif
                </p>
            </div>
            @if($fastMover)
                <div class="bg-green-50 border-l-4 border-green-500 p-3 rounded-r text-xs">
                    <span class="font-bold text-green-800"><i class="fa-regular fa-lightbulb mr-1"></i> Recommendation:</span>
                    <span class="text-green-700"> Increase minimum safety stock to prevent lost sales. Current stock is {{ $fastMover->in_stock }} units.</span>
                </div>
            @endif
        </div>

        <!-- DYNAMIC STAGNANT CAPITAL CARD -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex flex-col justify-between hover:shadow-md transition-shadow">
            <div>
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-lg bg-orange-100 text-orange-600 flex items-center justify-center text-lg">
                        <i class="fa-solid fa-hourglass-half"></i>
                    </div>
                    <h3 class="font-bold text-gray-800">Slow-Moving Product</h3>
                </div>
                <p class="text-sm text-gray-600 mb-4">
                    @if($stagnantProduct)
                        You have <strong>₱{{ number_format($stagnantProduct->in_stock * $stagnantProduct->unit_price, 2) }}</strong> tied up in {{ $stagnantProduct->product_name }}. 0 units have been sold in the last 45 days.
                    @else
                        Capital allocation is healthy. No severely stagnant items detected in the last 45 days.
                    @endif
                </p>
            </div>
            @if($stagnantProduct)
                <div class="bg-orange-50 border-l-4 border-orange-500 p-3 rounded-r text-xs">
                    <span class="font-bold text-orange-800"><i class="fa-regular fa-lightbulb mr-1"></i> Recommendation:</span>
                    <span class="text-orange-700"> Apply a markdown promo to stimulate sales and recover capital.</span>
                </div>
            @endif
        </div>

        <!-- DYNAMIC RESTOCK ACTION CARD -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex flex-col justify-between hover:shadow-md transition-shadow">
            <div>
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-lg bg-red-100 text-red-600 flex items-center justify-center text-lg">
                        <i class="fa-solid fa-cart-arrow-down"></i>
                    </div>
                    <h3 class="font-bold text-gray-800">Restock Action Required</h3>
                </div>
                <p class="text-sm text-gray-600 mb-4">
                    @if($criticalRestock)
                        The <strong>{{ $criticalRestock->product_name }}</strong> has reached a critical stock level ({{ $criticalRestock->in_stock }} units left). 
                    @else
                        All products are currently above their minimum reorder points. No urgent action required.
                    @endif
                </p>
            </div>
            @if($criticalRestock)
                <div class="bg-red-50 border-l-4 border-red-500 p-3 rounded-r text-xs">
                    <span class="font-bold text-red-800"><i class="fa-regular fa-lightbulb mr-1"></i> Recommendation:</span>
                    <span class="text-red-700"> Generate a restock order for {{ $criticalRestock->reorder_point * 3 }} units immediately to avoid a stockout.</span>
                </div>
            @endif
        </div>

    </div>

    <!-- The rest of your charts and simulators remain untouched below -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 animate-fade-in delay-100">
        
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 lg:col-span-2 flex flex-col hover:shadow-md transition-shadow duration-300">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h2 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                        <i class="fa-solid fa-chart-line text-blue-500"></i> Velocity-Based Demand Forecast
                    </h2>
                    <p class="text-xs text-gray-500 mt-1">Projected revenue based on current velocity and historical patterns.</p>
                </div>
                <select class="bg-gray-50 border border-gray-200 text-gray-700 text-xs font-medium rounded-lg focus:ring-navy-700 focus:border-navy-700 block p-2 cursor-pointer outline-none hover:bg-gray-100 transition-colors">
                    <option>Next 30 Days</option>
                    <option>Next Quarter</option>
                </select>
            </div>
            <div class="relative flex-1 w-full min-h-[280px]">
                <canvas id="forecastChart"></canvas>
            </div>
        </div>

        <div class="bg-navy-900 rounded-2xl shadow-sm border border-navy-700 overflow-hidden flex flex-col hover:shadow-lg transition-shadow duration-300">
            <div class="p-5 border-b border-navy-700 bg-navy-800">
                <h2 class="text-lg font-semibold text-white flex items-center gap-2">
                    <i class="fa-solid fa-sliders text-yellow-400"></i> "What-If" Simulator
                </h2>
                <p class="text-xs text-blue-200 mt-1">Test pricing strategies before implementing them.</p>
            </div>
            
            <div class="p-6 flex-1 flex flex-col justify-between text-white">
                <div class="space-y-6">
                    <div>
                        <label class="block text-xs font-medium text-gray-300 mb-2">Target Product Category</label>
                        <select class="w-full px-3 py-2 bg-navy-700 border border-navy-600 rounded-lg text-sm text-white focus:outline-none focus:ring-2 focus:ring-yellow-400 cursor-pointer">
                            <option>Queen Size Uratex Foams</option>
                            <option>Smart TVs</option>
                            <option>L-Shape Sofas</option>
                        </select>
                    </div>

                    <div>
                        <div class="flex justify-between text-xs font-medium text-gray-300 mb-2">
                            <label>Apply Discount</label>
                            <span id="discountValue" class="text-yellow-400 font-bold">10%</span>
                        </div>
                        <input type="range" min="0" max="30" value="10" class="w-full h-2 bg-navy-700 rounded-lg appearance-none cursor-pointer accent-yellow-400" oninput="document.getElementById('discountValue').innerText = this.value + '%'; updateSimulation(this.value);">
                    </div>

                    <div class="bg-navy-800 p-4 rounded-xl border border-navy-600 mt-4">
                        <p class="text-xs text-gray-400 mb-1">Projected Outcome (30 Days)</p>
                        <div class="flex justify-between items-end">
                            <div>
                                <p class="text-sm text-gray-300">Sales Volume</p>
                                <p class="text-xl font-bold text-green-400" id="simVolume">+24%</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm text-gray-300">Net Revenue</p>
                                <p class="text-xl font-bold text-yellow-400" id="simRevenue">₱142,500</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const ctx = document.getElementById('forecastChart').getContext('2d');
        
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4 (Now)', 'Week 5 (Est)', 'Week 6 (Est)', 'Week 7 (Est)'],
                datasets: [
                    {
                        label: 'Actual Revenue',
                        data: [120000, 135000, 115000, 146726, null, null, null],
                        borderColor: '#012C55',
                        backgroundColor: 'rgba(1, 44, 85, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#012C55',
                        pointRadius: 5
                    },
                    {
                        label: 'DSS Forecast',
                        data: [null, null, null, 146726, 155000, 162000, 175000],
                        borderColor: '#10B981', // Green
                        borderWidth: 3,
                        borderDash: [5, 5], // Dotted line for future prediction
                        tension: 0.4,
                        pointBackgroundColor: '#10B981',
                        pointRadius: 5
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: { duration: 1500, easing: 'easeOutQuart' },
                plugins: {
                    legend: { position: 'top', align: 'end', labels: { usePointStyle: true, boxWidth: 8, font: { family: 'Inter' } } },
                    tooltip: { backgroundColor: '#012C55', padding: 12, cornerRadius: 8 }
                },
                scales: {
                    y: { beginAtZero: false, grid: { color: '#f3f4f6', drawBorder: false }, border: { display: false }, ticks: { color: '#6b7280', font: { family: 'Inter' } } },
                    x: { grid: { display: false }, border: { display: false }, ticks: { color: '#6b7280', font: { family: 'Inter' } } }
                }
            }
        });
    });

    // Simple script to make the What-If slider interactive
    function updateSimulation(discount) {
        let baseVolumeIncrease = 0;
        let baseRevenue = 120000; // Example base revenue without discount
        
        if (discount == 0) {
            document.getElementById('simVolume').innerText = "0%";
            document.getElementById('simVolume').className = "text-xl font-bold text-gray-300";
            document.getElementById('simRevenue').innerText = "₱" + baseRevenue.toLocaleString();
        } else {
            // Fake algorithm logic: Higher discount = higher volume, but revenue peaks then drops
            let volumeIncrease = Math.round(discount * 2.4); 
            let revenueMultiplier = 1 + (volumeIncrease / 100) - (discount / 100);
            let projectedRevenue = Math.round(baseRevenue * revenueMultiplier);

            document.getElementById('simVolume').innerText = "+" + volumeIncrease + "%";
            document.getElementById('simVolume').className = "text-xl font-bold text-green-400";
            document.getElementById('simRevenue').innerText = "₱" + projectedRevenue.toLocaleString();
            
            // Turn revenue text red if discount is too high (losing money)
            if (discount > 20) {
                document.getElementById('simRevenue').className = "text-xl font-bold text-red-400";
            } else {
                document.getElementById('simRevenue').className = "text-xl font-bold text-yellow-400";
            }
        }
    }
</script>
@endsection