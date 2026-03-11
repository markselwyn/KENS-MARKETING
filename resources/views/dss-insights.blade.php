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

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 animate-fade-in">
        
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:-translate-y-1 hover:shadow-lg transition-all duration-300 relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 opacity-5 transform group-hover:scale-110 transition-transform duration-500">
                <i class="fa-solid fa-cart-shopping text-8xl"></i>
            </div>
            <div class="flex items-center gap-3 mb-4">
                <div class="bg-purple-50 p-2 rounded-lg text-purple-600"><i class="fa-solid fa-link"></i></div>
                <h3 class="font-bold text-gray-800 text-sm">Market Basket Analysis</h3>
            </div>
            <p class="text-sm text-gray-600 leading-relaxed mb-4">Data shows <span class="font-bold text-gray-900">68%</span> of customers who buy a Smart TV also purchase a Karaoke Bluetooth Speaker within 30 days.</p>
            <button class="text-xs bg-purple-50 text-purple-700 font-semibold px-3 py-1.5 rounded hover:bg-purple-100 transition-colors w-full text-left">
                💡 Suggestion: Create a "Home Entertainment" Bundle Promo.
            </button>
        </div>

        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:-translate-y-1 hover:shadow-lg transition-all duration-300 relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 opacity-5 transform group-hover:scale-110 transition-transform duration-500">
                <i class="fa-solid fa-boxes-stacked text-8xl"></i>
            </div>
            <div class="flex items-center gap-3 mb-4">
                <div class="bg-orange-50 p-2 rounded-lg text-orange-600"><i class="fa-solid fa-money-bill-transfer"></i></div>
                <h3 class="font-bold text-gray-800 text-sm">Capital Optimization</h3>
            </div>
            <p class="text-sm text-gray-600 leading-relaxed mb-4">You have <span class="font-bold text-gray-900">₱85,000</span> tied up in 'Wooden Dining Sets' which have a low velocity score of 0.2/week.</p>
            <button class="text-xs bg-orange-50 text-orange-700 font-semibold px-3 py-1.5 rounded hover:bg-orange-100 transition-colors w-full text-left">
                💡 Suggestion: Apply a 15% markdown to free up cash flow.
            </button>
        </div>

        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:-translate-y-1 hover:shadow-lg transition-all duration-300 relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 opacity-5 transform group-hover:scale-110 transition-transform duration-500">
                <i class="fa-solid fa-cloud-sun text-8xl"></i>
            </div>
            <div class="flex items-center gap-3 mb-4">
                <div class="bg-green-50 p-2 rounded-lg text-green-600"><i class="fa-solid fa-arrow-trend-up"></i></div>
                <h3 class="font-bold text-gray-800 text-sm">Seasonal Demand Forecast</h3>
            </div>
            <p class="text-sm text-gray-600 leading-relaxed mb-4">Historical data indicates a <span class="font-bold text-gray-900">45% spike</span> in Air Conditioner & Inverter Ref sales approaching April (Summer).</p>
            <button class="text-xs bg-green-50 text-green-700 font-semibold px-3 py-1.5 rounded hover:bg-green-100 transition-colors w-full text-left">
                💡 Suggestion: Increase supplier PO volume by March 20th.
            </button>
        </div>

    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 animate-fade-in delay-100">
        
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 lg:col-span-2 flex flex-col hover:shadow-md transition-shadow duration-300">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h2 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                        <i class="fa-solid fa-chart-area text-navy-700"></i> AI Demand Forecast
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