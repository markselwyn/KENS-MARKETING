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
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 animate-fade-in">
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:-translate-y-1 hover:shadow-lg transition-all duration-300">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-3">
                    <div class="bg-blue-50 p-2 rounded-lg text-blue-600"><i class="fa-solid fa-calendar-day"></i></div>
                    <h3 class="font-medium text-gray-500 text-sm">Today's Sales</h3>
                </div>
                <span class="text-xs font-medium text-green-600 bg-green-50 px-2 py-1 rounded-full"><i class="fa-solid fa-arrow-up mr-1"></i> 5.2%</span>
            </div>
            <h2 class="text-3xl font-bold text-gray-800 tracking-tight">₱28,450</h2>
            <p class="text-xs text-gray-400 mt-2">4 high-ticket transactions today</p>
        </div>

        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:-translate-y-1 hover:shadow-lg transition-all duration-300">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-3">
                    <div class="bg-indigo-50 p-2 rounded-lg text-indigo-600"><i class="fa-solid fa-calendar-week"></i></div>
                    <h3 class="font-medium text-gray-500 text-sm">This Week</h3>
                </div>
                <span class="text-xs font-medium text-green-600 bg-green-50 px-2 py-1 rounded-full"><i class="fa-solid fa-arrow-up mr-1"></i> 12.8%</span>
            </div>
            <h2 class="text-3xl font-bold text-gray-800 tracking-tight">₱146,726</h2>
            <p class="text-xs text-gray-400 mt-2">Appliance sales trending up</p>
        </div>

        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:-translate-y-1 hover:shadow-lg transition-all duration-300">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-3">
                    <div class="bg-purple-50 p-2 rounded-lg text-purple-600"><i class="fa-solid fa-calendar-days"></i></div>
                    <h3 class="font-medium text-gray-500 text-sm">This Month</h3>
                </div>
                <span class="text-xs font-medium text-red-600 bg-red-50 px-2 py-1 rounded-full"><i class="fa-solid fa-arrow-down mr-1"></i> 2.1%</span>
            </div>
            <h2 class="text-3xl font-bold text-gray-800 tracking-tight">₱584,200</h2>
            <p class="text-xs text-gray-400 mt-2">Slight dip in furniture segment</p>
        </div>

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
            <h2 class="text-3xl font-bold tracking-tight mb-2">₱120K+</h2>
            <p class="text-xs text-gray-300 leading-relaxed">High volume predicted for Smart TVs. Recommend ensuring 2 extra staff members are scheduled for Saturday.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 animate-fade-in delay-100">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden lg:col-span-1 flex flex-col">
            <div class="bg-navy-900 text-white p-4 border-b border-navy-700">
                <h2 class="text-md font-semibold flex items-center gap-2">
                    <i class="fa-solid fa-cart-plus"></i> Record Sale
                </h2>
            </div>
            <form action="#" method="POST" class="p-4 space-y-4 flex-1 flex flex-col justify-between" onsubmit="event.preventDefault(); alert('Sale Recorded! (Prototype)');">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Customer / Reference</label>
                        <input type="text" placeholder="e.g. Walk-in or Name" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-navy-700 bg-gray-50 focus:bg-white transition-colors" required>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Category</label>
                        <select class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-navy-700 bg-gray-50 focus:bg-white cursor-pointer transition-colors" required>
                            <option value="" disabled selected>Choose...</option>
                            <option value="furniture">Furniture</option>
                            <option value="appliances">Appliances</option>
                            <option value="foams">Foams</option>
                            <option value="speakers">Speakers</option>
                            <option value="tv">TV</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Item</label>
                        <select class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-navy-700 bg-gray-50 focus:bg-white cursor-pointer transition-colors" required>
                            <option value="" disabled selected>Choose item...</option>
                            <option value="item1">L-Shape Sofa Set</option>
                            <option value="item2">Queen Uratex Foam</option>
                            <option value="item3">43" Smart LED TV</option>
                        </select>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Qty</label>
                            <input type="number" min="1" value="1" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-navy-700 bg-gray-50 focus:bg-white transition-colors" required>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Total (₱)</label>
                            <input type="number" placeholder="0.00" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-navy-700 bg-gray-50 focus:bg-white transition-colors" required>
                        </div>
                    </div>
                </div>
                <div class="pt-2 mt-4">
                    <button type="submit" class="w-full bg-navy-900 text-white text-sm font-medium py-2.5 rounded-lg hover:bg-navy-700 hover:shadow-lg transition-all duration-200 flex justify-center items-center gap-2">
                        <i class="fa-solid fa-check"></i> Process
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

        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 lg:col-span-1 flex flex-col">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-md font-semibold text-gray-800">Top Sellers</h2>
                <span class="text-xs text-green-600 bg-green-50 px-2 py-1 rounded font-medium">This Week</span>
            </div>
            <div class="space-y-4 flex-1">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded bg-navy-50 text-navy-900 font-bold flex items-center justify-center text-xs">1</div>
                    <div class="flex-1">
                        <h4 class="text-sm font-semibold text-gray-800 line-clamp-1">Queen Uratex Foam</h4>
                        <p class="text-xs text-gray-500">12 units sold</p>
                    </div>
                    <div class="text-sm font-bold text-gray-900">₱85k</div>
                </div>
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded bg-gray-50 text-gray-600 font-bold flex items-center justify-center text-xs">2</div>
                    <div class="flex-1">
                        <h4 class="text-sm font-semibold text-gray-800 line-clamp-1">43" Smart LED TV</h4>
                        <p class="text-xs text-gray-500">5 units sold</p>
                    </div>
                    <div class="text-sm font-bold text-gray-900">₱72k</div>
                </div>
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded bg-gray-50 text-gray-600 font-bold flex items-center justify-center text-xs">3</div>
                    <div class="flex-1">
                        <h4 class="text-sm font-semibold text-gray-800 line-clamp-1">L-Shape Sofa Set</h4>
                        <p class="text-xs text-gray-500">3 sets sold</p>
                    </div>
                    <div class="text-sm font-bold text-gray-900">₱64k</div>
                </div>
            </div>
            <a href="#" class="mt-4 text-center text-xs text-navy-700 font-medium hover:underline block">View Full Inventory Report &rarr;</a>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden animate-fade-in delay-200">
        <div class="p-6 border-b border-gray-100 flex justify-between items-center">
            <h2 class="text-lg font-semibold text-gray-800">Sales Ledger</h2>
            <div class="relative">
                <i class="fa-solid fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 text-xs"></i>
                <input type="text" placeholder="Search receipt..." class="pl-8 pr-4 py-1.5 bg-gray-50 border border-gray-200 text-gray-600 text-sm rounded-lg focus:outline-none focus:ring-2 focus:ring-navy-700 focus:bg-white w-48 transition-all duration-200">
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-4 font-semibold">Receipt No.</th>
                        <th scope="col" class="px-6 py-4 font-semibold">Date & Time</th>
                        <th scope="col" class="px-6 py-4 font-semibold">Customer</th>
                        <th scope="col" class="px-6 py-4 font-semibold">Purchased Items</th>
                        <th scope="col" class="px-6 py-4 font-semibold">Total Amount</th>
                        <th scope="col" class="px-6 py-4 font-semibold text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <tr class="bg-white hover:bg-gray-50 transition-colors duration-200">
                        <td class="px-6 py-4 font-medium text-navy-700">#RC-10442</td>
                        <td class="px-6 py-4 text-xs">Mar 10, 2026</td>
                        <td class="px-6 py-4 text-gray-900 font-medium">Villanueva Residence</td>
                        <td class="px-6 py-4 text-xs">1x L-Shape Sofa Set</td>
                        <td class="px-6 py-4 font-medium text-gray-900">₱24,500.00</td>
                        <td class="px-6 py-4 text-right">
                            <button class="text-gray-400 hover:text-navy-900"><i class="fa-solid fa-eye px-2"></i></button>
                        </td>
                    </tr>
                    <tr class="bg-white hover:bg-gray-50 transition-colors duration-200">
                        <td class="px-6 py-4 font-medium text-navy-700">#RC-10441</td>
                        <td class="px-6 py-4 text-xs">Mar 10, 2026</td>
                        <td class="px-6 py-4 text-gray-900 font-medium">Walk-in</td>
                        <td class="px-6 py-4 text-xs">1x Queen Size Uratex Foam</td>
                        <td class="px-6 py-4 font-medium text-gray-900">₱4,100.00</td>
                        <td class="px-6 py-4 text-right">
                            <button class="text-gray-400 hover:text-navy-900"><i class="fa-solid fa-eye px-2"></i></button>
                        </td>
                    </tr>
                    <tr class="bg-white hover:bg-gray-50 transition-colors duration-200">
                        <td class="px-6 py-4 font-medium text-navy-700">#RC-10440</td>
                        <td class="px-6 py-4 text-xs">Mar 09, 2026</td>
                        <td class="px-6 py-4 text-gray-900 font-medium">Oas Restobar</td>
                        <td class="px-6 py-4 text-xs">2x Karaoke Speaker, 1x Smart TV</td>
                        <td class="px-6 py-4 font-medium text-gray-900">₱45,200.00</td>
                        <td class="px-6 py-4 text-right">
                            <button class="text-gray-400 hover:text-navy-900"><i class="fa-solid fa-eye px-2"></i></button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="hidden print:block text-black bg-white w-full">
    
    <div class="text-center border-b-2 border-black pb-6 mb-8">
        <h1 class="text-3xl font-bold uppercase tracking-wider text-black">Ken's Marketing</h1>
        <p class="text-sm mt-1">Ligao City, Bicol, Philippines</p>
        <h2 class="text-xl font-semibold mt-4">DSS Official Sales & Performance Report</h2>
        <p class="text-sm mt-1">Report Generated: {{ date('F j, Y, g:i a') }}</p>
    </div>

    <div class="mb-8">
        <h3 class="text-lg font-bold border-b border-gray-300 mb-4 pb-1 uppercase">1. Executive Summary</h3>
        <div class="grid grid-cols-3 gap-4 text-sm">
            <div class="p-4 border border-gray-300 rounded">
                <p class="text-gray-600 font-medium">Total Weekly Sales</p>
                <p class="text-2xl font-bold mt-1">₱146,726.00</p>
            </div>
            <div class="p-4 border border-gray-300 rounded">
                <p class="text-gray-600 font-medium">Top Performing Category</p>
                <p class="text-2xl font-bold mt-1">Appliances</p>
            </div>
            <div class="p-4 border border-gray-300 rounded">
                <p class="text-gray-600 font-medium">Average Transaction Value</p>
                <p class="text-2xl font-bold mt-1">₱12,227.00</p>
            </div>
        </div>
    </div>

    <div class="mb-8">
        <h3 class="text-lg font-bold border-b border-gray-300 mb-4 pb-1 uppercase">2. Category Breakdown</h3>
        <table class="w-full text-sm text-left border-collapse border border-gray-300">
            <thead>
                <tr class="bg-gray-100">
                    <th class="border border-gray-300 px-4 py-2 font-bold">Category</th>
                    <th class="border border-gray-300 px-4 py-2 font-bold">Total Sales (₱)</th>
                    <th class="border border-gray-300 px-4 py-2 font-bold">Contribution %</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="border border-gray-300 px-4 py-2">Furniture</td>
                    <td class="border border-gray-300 px-4 py-2">₱35,500.00</td>
                    <td class="border border-gray-300 px-4 py-2">24%</td>
                </tr>
                <tr>
                    <td class="border border-gray-300 px-4 py-2">Appliances</td>
                    <td class="border border-gray-300 px-4 py-2">₱68,300.00</td>
                    <td class="border border-gray-300 px-4 py-2">46%</td>
                </tr>
                <tr>
                    <td class="border border-gray-300 px-4 py-2">Foams</td>
                    <td class="border border-gray-300 px-4 py-2">₱18,450.00</td>
                    <td class="border border-gray-300 px-4 py-2">13%</td>
                </tr>
                <tr>
                    <td class="border border-gray-300 px-4 py-2">Speakers & TV</td>
                    <td class="border border-gray-300 px-4 py-2">₱24,476.00</td>
                    <td class="border border-gray-300 px-4 py-2">17%</td>
                </tr>
            </tbody>
            <tfoot>
                <tr class="bg-gray-50 font-bold">
                    <td class="border border-gray-300 px-4 py-2 text-right">TOTAL</td>
                    <td class="border border-gray-300 px-4 py-2" colspan="2">₱146,726.00</td>
                </tr>
            </tfoot>
        </table>
    </div>

    <div class="mb-8">
        <h3 class="text-lg font-bold border-b border-gray-300 mb-4 pb-1 uppercase">3. Raw Transaction Data (Last 3 Records)</h3>
        <table class="w-full text-sm text-left border-collapse border border-gray-300">
            <thead>
                <tr class="bg-gray-100">
                    <th class="border border-gray-300 px-3 py-2 font-bold">Date</th>
                    <th class="border border-gray-300 px-3 py-2 font-bold">Receipt</th>
                    <th class="border border-gray-300 px-3 py-2 font-bold">Customer Details</th>
                    <th class="border border-gray-300 px-3 py-2 font-bold">Items Purchased</th>
                    <th class="border border-gray-300 px-3 py-2 font-bold text-right">Amount (₱)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="border border-gray-300 px-3 py-2">03/10/2026</td>
                    <td class="border border-gray-300 px-3 py-2">RC-10442</td>
                    <td class="border border-gray-300 px-3 py-2">Villanueva Residence (Centro)</td>
                    <td class="border border-gray-300 px-3 py-2">1x L-Shape Sofa Set</td>
                    <td class="border border-gray-300 px-3 py-2 text-right">24,500.00</td>
                </tr>
                <tr>
                    <td class="border border-gray-300 px-3 py-2">03/10/2026</td>
                    <td class="border border-gray-300 px-3 py-2">RC-10441</td>
                    <td class="border border-gray-300 px-3 py-2">Walk-in</td>
                    <td class="border border-gray-300 px-3 py-2">1x Queen Size Uratex Foam</td>
                    <td class="border border-gray-300 px-3 py-2 text-right">4,100.00</td>
                </tr>
                <tr>
                    <td class="border border-gray-300 px-3 py-2">03/09/2026</td>
                    <td class="border border-gray-300 px-3 py-2">RC-10440</td>
                    <td class="border border-gray-300 px-3 py-2">Oas Restobar (Oas, Albay)</td>
                    <td class="border border-gray-300 px-3 py-2">2x Karaoke Speaker, 1x Smart TV</td>
                    <td class="border border-gray-300 px-3 py-2 text-right">45,200.00</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="mb-12">
        <h3 class="text-lg font-bold border-b border-gray-300 mb-4 pb-1 uppercase">4. DSS System Conclusion & Recommendations</h3>
        <div class="p-4 bg-gray-50 border border-gray-300 rounded text-sm italic text-gray-800 leading-relaxed">
            "Based on the analysis of the data above, the **Appliance** and **Furniture** categories are demonstrating high sales velocity. The system recommends reviewing inventory levels for Smart TVs and Uratex Foams immediately. Furthermore, intraday trends indicate peak traffic between 10:00 AM and 2:00 PM; it is advised to increase floor staffing during these hours to maximize conversion rates."
        </div>
    </div>

    <div class="grid grid-cols-2 gap-16 mt-16 text-sm">
        <div class="text-center">
            <div class="border-b border-black w-full mb-2"></div>
            <p class="font-bold">Prepared By</p>
            <p class="text-gray-500">System Administrator</p>
        </div>
        <div class="text-center">
            <div class="border-b border-black w-full mb-2"></div>
            <p class="font-bold">Approved By</p>
            <p class="text-gray-500">Store Manager</p>
        </div>
    </div>

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
    document.addEventListener("DOMContentLoaded", function() {
        const ctx = document.getElementById('salesVelocityChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['8 AM', '10 AM', '12 PM', '2 PM', '4 PM', '6 PM'],
                datasets: [{
                    label: 'Sales Vol (₱)',
                    data: [8500, 31000, 12400, 48000, 22600, 18100],
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