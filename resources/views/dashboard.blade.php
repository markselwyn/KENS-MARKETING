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
        /* Ensure charts fit on paper */
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
        <div class="bg-navy-900 rounded-2xl p-6 text-white shadow-sm border border-navy-700 relative overflow-hidden group hover:-translate-y-1 hover:shadow-lg transition-all duration-300 cursor-pointer print:bg-white print:text-black print:border-gray-300">
            <div class="absolute right-0 top-0 opacity-10 transform translate-x-4 -translate-y-4 group-hover:scale-110 transition-transform duration-500 ease-out no-print">
                <i class="fa-solid fa-chart-line text-8xl"></i>
            </div>
            <div class="flex items-center gap-3 mb-4">
                <div class="bg-white/20 p-2 rounded-lg print:bg-gray-100 print:text-gray-600"><i class="fa-solid fa-wallet text-white print:text-gray-600 text-sm"></i></div>
                <h3 class="font-medium text-blue-100 text-sm print:text-gray-600">Gross Revenue</h3>
            </div>
            <h2 class="text-3xl font-bold tracking-tight mb-1">₱46,726</h2>
            <p class="text-xs text-blue-200 font-medium print:text-gray-500">Updated just now</p>
        </div>

        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:-translate-y-1 hover:shadow-lg transition-all duration-300 cursor-pointer print:border-gray-300">
            <div class="flex items-center gap-3 mb-4">
                <div class="bg-green-100 p-2 rounded-lg"><i class="fa-solid fa-money-bill-trend-up text-green-600 text-sm"></i></div>
                <h3 class="font-medium text-gray-500 text-sm">Net Profit</h3>
            </div>
            <h2 class="text-3xl font-bold text-gray-800 tracking-tight mb-1">₱32,450</h2>
            <p class="text-xs text-green-500 font-medium"><i class="fa-solid fa-arrow-up mr-1"></i> 12.5% vs last week</p>
        </div>

        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:-translate-y-1 hover:shadow-lg transition-all duration-300 cursor-pointer print:border-gray-300">
            <div class="flex items-center gap-3 mb-4">
                <div class="bg-blue-100 p-2 rounded-lg"><i class="fa-solid fa-basket-shopping text-blue-600 text-sm"></i></div>
                <h3 class="font-medium text-gray-500 text-sm">Avg. Order Value</h3>
            </div>
            <h2 class="text-3xl font-bold text-gray-800 tracking-tight mb-1">₱9,345</h2>
            <p class="text-xs text-gray-400 font-medium">Based on 142 orders</p>
        </div>

        <div class="bg-white rounded-2xl p-6 shadow-sm border border-red-100 hover:-translate-y-1 hover:shadow-lg transition-all duration-300 relative overflow-hidden group print:border-gray-300 flex flex-col justify-between cursor-pointer">
            <div class="absolute left-0 top-0 w-1 h-full bg-red-500 group-hover:w-2 transition-all duration-300 no-print"></div>
            <div>
                <div class="flex items-center gap-3 mb-3">
                    <div class="bg-red-50 p-2 rounded-lg"><i class="fa-solid fa-triangle-exclamation text-red-500 text-sm"></i></div>
                    <h3 class="font-medium text-gray-500 text-sm">Restock Required</h3>
                </div>
                <h2 class="text-3xl font-bold text-gray-800 tracking-tight mb-1">5 <span class="text-base font-normal text-gray-500 tracking-normal">items</span></h2>
                <p class="text-xs text-gray-500 line-clamp-1">Priority: <span class="font-medium text-red-600">L-Shape Sofa Set (1 left)</span></p>
            </div>
            <a href="/inventory" class="mt-4 text-xs text-center block w-full text-red-600 hover:text-red-800 font-medium transition-colors bg-red-50 py-2 rounded-lg group-hover:bg-red-100 no-print">
                Review & Restock <i class="fa-solid fa-arrow-right ml-1 transform group-hover:translate-x-1 transition-transform inline-block"></i>
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 animate-fade-in delay-200">
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 lg:col-span-2 print:border-gray-300">
            <div class="flex justify-between items-start mb-6">
                <div>
                    <h2 class="text-lg font-semibold text-gray-800">Revenue Trend</h2>
                    <p class="text-sm text-gray-500 mt-1 transition-all duration-300" id="revenue-total">₱46,726 <span class="text-green-500 font-medium bg-green-50 px-2 py-0.5 rounded text-xs ml-2"><i class="fa-solid fa-arrow-up"></i> 25.8%</span></p>
                </div>
                <select id="timeframe-selector" onchange="updateChartData()" class="bg-gray-50 border border-gray-200 text-gray-700 text-sm font-medium rounded-lg focus:ring-navy-700 focus:border-navy-700 block p-2.5 cursor-pointer outline-none hover:bg-gray-100 transition-colors duration-200 shadow-sm no-print">
                    <option value="7days">Last 7 Days</option>
                    <option value="30days">Last 30 Days</option>
                    <option value="year">This Year</option>
                </select>
            </div>
            <div class="relative h-72 w-full">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>

        <div class="bg-gradient-to-br from-navy-900 to-navy-700 rounded-2xl p-6 shadow-sm border border-navy-700 text-white flex flex-col hover:shadow-lg transition-shadow duration-300 print:bg-white print:text-black print:border-gray-300">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-wand-magic-sparkles text-yellow-400 print:text-black"></i>
                    <h2 class="text-lg font-semibold"> Smart Insights</h2>
                </div>
                <span class="bg-white/20 text-xs px-2 py-1 rounded-md font-medium print:bg-gray-200 print:text-black">Live</span>
            </div>
            
            <div class="flex-1 space-y-4">
                <div class="bg-white/10 rounded-xl p-4 border border-white/20 hover:bg-white/20 transition-colors duration-200 cursor-pointer group print:bg-gray-50 print:border-gray-200">
                    <div class="flex items-start gap-3">
                        <div class="mt-0.5 text-red-400 group-hover:scale-110 transition-transform print:text-red-600"><i class="fa-solid fa-arrow-trend-down"></i></div>
                        <div class="w-full">
                            <h4 class="text-sm font-semibold text-white print:text-black">Accelerated Depletion</h4>
                            <p class="text-xs text-gray-300 mt-1 print:text-gray-600">Queen Size Uratex Foam is selling 40% faster this week. Projected to run out in 3 days due to demand surge.</p>
                            <button onclick="window.location.href='/inventory'" class="mt-3 text-xs w-full bg-white text-navy-900 font-bold px-3 py-2 rounded-lg hover:bg-gray-100 transition-colors shadow-sm no-print">
                                Review Restock Priority
                            </button>
                        </div>
                    </div>
                </div>

                <div class="bg-white/10 rounded-xl p-4 border border-white/20 hover:bg-white/20 transition-colors duration-200 cursor-pointer group print:bg-gray-50 print:border-gray-200">
                    <div class="flex items-start gap-3">
                        <div class="mt-0.5 text-green-400 group-hover:scale-110 transition-transform print:text-green-600"><i class="fa-solid fa-lightbulb"></i></div>
                        <div class="w-full">
                            <h4 class="text-sm font-semibold text-white print:text-black flex items-center gap-2">
                                Sales Pattern Detected
                                <span class="bg-green-500 text-white text-[9px] px-1.5 py-0.5 rounded uppercase tracking-wider font-bold">New</span>
                            </h4>
                            <p class="text-xs text-gray-300 mt-1 print:text-gray-600"><strong class="text-white print:text-black">68% correlation:</strong> Customers buying the 43" Smart TV frequently purchase the Wall Mount. Instruct staff to bundle.</p>
                            <button class="mt-3 text-xs w-full bg-white text-navy-900 font-bold px-3 py-2 rounded-lg hover:bg-gray-100 transition-colors shadow-sm no-print flex items-center justify-center gap-2">
                                <i class="fa-solid fa-chart-pie"></i> View Sales Analytics
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 print-break">
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 animate-fade-in delay-300 print:border-gray-300">
            <h2 class="text-lg font-semibold text-gray-800 mb-6">Category Sales Velocity</h2>
            <div class="space-y-6">
                <div class="group cursor-pointer">
                    <div class="flex justify-between text-sm font-medium mb-2">
                        <span class="text-gray-700 group-hover:text-navy-900 transition-colors">Furniture</span>
                        <span class="text-gray-900">₱35,500</span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-2.5 overflow-hidden">
                        <div class="bg-navy-900 h-2.5 rounded-full transition-all duration-1000 ease-out print:bg-gray-800" style="width: 75%"></div>
                    </div>
                </div>
                <div class="group cursor-pointer">
                    <div class="flex justify-between text-sm font-medium mb-2">
                        <span class="text-gray-700 group-hover:text-navy-700 transition-colors">Appliances</span>
                        <span class="text-gray-900">₱28,300</span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-2.5 overflow-hidden">
                        <div class="bg-navy-700 h-2.5 rounded-full transition-all duration-1000 ease-out print:bg-gray-600" style="width: 60%"></div>
                    </div>
                </div>
                <div class="group cursor-pointer">
                    <div class="flex justify-between text-sm font-medium mb-2">
                        <span class="text-gray-700 group-hover:text-blue-500 transition-colors">Foams</span>
                        <span class="text-gray-900">₱18,450</span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-2.5 overflow-hidden">
                        <div class="bg-blue-500 h-2.5 rounded-full transition-all duration-1000 ease-out print:bg-gray-400" style="width: 45%"></div>
                    </div>
                </div>
                <div class="group cursor-pointer">
                    <div class="flex justify-between text-sm font-medium mb-2">
                        <span class="text-gray-700 group-hover:text-indigo-400 transition-colors">Speakers</span>
                        <span class="text-gray-900">₱12,800</span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-2.5 overflow-hidden">
                        <div class="bg-indigo-400 h-2.5 rounded-full transition-all duration-1000 ease-out print:bg-gray-300" style="width: 30%"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden animate-fade-in delay-300 print:border-gray-300">
            <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                <h2 class="text-lg font-semibold text-gray-800">Recent Transactions</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-4 font-semibold">Transaction ID</th>
                            <th scope="col" class="px-6 py-4 font-semibold">Customer & Location</th>
                            <th scope="col" class="px-6 py-4 font-semibold">Date</th>
                            <th scope="col" class="px-6 py-4 font-semibold">Amount</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <tr class="bg-white hover:bg-gray-50 transition-colors duration-200">
                            <td class="px-6 py-4 font-medium text-gray-900">#TRX-0092</td>
                            <td class="px-6 py-4">
                                <div class="text-gray-900 font-medium">Villanueva Residence</div>
                                <div class="text-xs text-gray-500 mt-0.5"><i class="fa-solid fa-location-dot mr-1"></i> Address: Centro, Polangui</div>
                            </td>
                            <td class="px-6 py-4 text-xs">Today, 10:23 AM</td>
                            <td class="px-6 py-4 font-medium text-gray-900">₱12,450.00</td>
                        </tr>
                        <tr class="bg-white hover:bg-gray-50 transition-colors duration-200">
                            <td class="px-6 py-4 font-medium text-gray-900">#TRX-0091</td>
                            <td class="px-6 py-4">
                                <div class="text-gray-900 font-medium">Walk-in Customer</div>
                                <div class="text-xs text-gray-500 mt-0.5"><i class="fa-solid fa-store mr-1"></i> In-Store Purchase</div>
                            </td>
                            <td class="px-6 py-4 text-xs">Today, 09:15 AM</td>
                            <td class="px-6 py-4 font-medium text-gray-900">₱8,500.00</td>
                        </tr>
                        <tr class="bg-white hover:bg-gray-50 transition-colors duration-200">
                            <td class="px-6 py-4 font-medium text-gray-900">#TRX-0090</td>
                            <td class="px-6 py-4">
                                <div class="text-gray-900 font-medium">Oas Restobar</div>
                                <div class="text-xs text-gray-500 mt-0.5"><i class="fa-solid fa-truck mr-1"></i> Delivery: Oas, Albay</div>
                            </td>
                            <td class="px-6 py-4 text-xs">Yesterday, 3:45 PM</td>
                            <td class="px-6 py-4 font-medium text-gray-900">₱45,200.00</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    let myChart; 

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
                labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                datasets: [{
                    label: 'Revenue (₱)',
                    data: [12000, 19000, 15000, 25000, 22000, 30000, 46726],
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
                    tooltip: { backgroundColor: '#012C55', padding: 12, cornerRadius: 8 }
                },
                scales: {
                    y: { beginAtZero: true, grid: { color: '#f3f4f6', drawBorder: false }, border: { display: false }, ticks: { color: '#9ca3af', font: { family: 'Inter' } } },
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
                newLabels = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
                newData = [12000, 19000, 15000, 25000, 22000, 30000, 46726];
                totalLabel.innerHTML = '₱46,726 <span class="text-green-500 font-medium bg-green-50 px-2 py-0.5 rounded text-xs ml-2"><i class="fa-solid fa-arrow-up"></i> 25.8%</span>';
            } else if (selector === '30days') {
                newLabels = ['Week 1', 'Week 2', 'Week 3', 'Week 4'];
                newData = [85000, 92000, 105000, 142000];
                totalLabel.innerHTML = '₱424,000 <span class="text-green-500 font-medium bg-green-50 px-2 py-0.5 rounded text-xs ml-2"><i class="fa-solid fa-arrow-up"></i> 12.1%</span>';
            } else if (selector === 'year') {
                newLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'];
                newData = [350000, 420000, 380000, 510000, 490000, 620000];
                totalLabel.innerHTML = '₱2,770,000 <span class="text-green-500 font-medium bg-green-50 px-2 py-0.5 rounded text-xs ml-2"><i class="fa-solid fa-arrow-up"></i> 8.4%</span>';
            }

            myChart.data.labels = newLabels;
            myChart.data.datasets[0].data = newData;
            myChart.update();
            
            totalLabel.style.opacity = '1';
        }, 200);
    }
</script>
@endsection