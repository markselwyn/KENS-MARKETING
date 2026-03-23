@extends('layouts.app')

@section('title', 'Reports Module - Ken\'s Marketing')
@section('header_title', 'Report Center & Analytics')

@section('content')
<div class="space-y-6">

    <div class="flex justify-between items-end animate-fade-in mb-2">
        <div>
            <p class="text-gray-500 text-sm">Generate, download, and analyze historical business performance.</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 animate-fade-in">
        
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 lg:col-span-2 flex flex-col justify-between hover:shadow-md transition-shadow duration-300">
            <div class="flex items-center gap-3 mb-6 border-b border-gray-100 pb-4">
                <div class="bg-navy-50 p-2 rounded-lg text-navy-900"><i class="fa-solid fa-file-export text-lg"></i></div>
                <h2 class="text-lg font-semibold text-gray-800">Custom Report Builder</h2>
            </div>
            
            <form action="#" method="POST" onsubmit="event.preventDefault(); alert('Generating your custom report... (Prototype)');" class="space-y-5">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Select Report Type</label>
                        <select class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-navy-700 bg-gray-50 focus:bg-white cursor-pointer transition-colors" required>
                            <option value="" disabled selected>Choose report type...</option>
                            <option value="sales_summary">Sales & Revenue Summary</option>
                            <option value="inventory_audit">Inventory & Stock Audit</option>
                            <option value="fast_slow">Fast/Slow Moving Products (DSS)</option>
                            <option value="profit_margin">Profit Margin Analysis</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Timeframe</label>
                        <select class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-navy-700 bg-gray-50 focus:bg-white cursor-pointer transition-colors" required>
                            <option value="today">Today</option>
                            <option value="this_week">This Week</option>
                            <option value="this_month" selected>This Month</option>
                            <option value="last_month">Last Month</option>
                            <option value="q1">Quarter 1 (Jan - Apr)</option>
                            <option value="custom">Custom Date Range...</option>
                        </select>
                    </div>
                </div>


                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Output Format</label>
                        <div class="flex gap-4">
                            <label class="flex items-center gap-2 cursor-pointer group">
                                <input type="radio" name="format" value="pdf" class="w-4 h-4 text-navy-700 focus:ring-navy-700 border-gray-300" checked>
                                <span class="text-sm text-gray-600 group-hover:text-navy-900"><i class="fa-regular fa-file-pdf text-red-500 mr-1"></i> PDF Document</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer group">
                                <input type="radio" name="format" value="excel" class="w-4 h-4 text-navy-700 focus:ring-navy-700 border-gray-300">
                                <span class="text-sm text-gray-600 group-hover:text-navy-900"><i class="fa-regular fa-file-excel text-green-600 mr-1"></i> Excel Spreadsheet</span>
                            </label>
                        </div>
                    </div>
                    
                    <div class="flex items-end justify-end">
                        <button type="submit" class="w-full md:w-auto px-6 py-2.5 bg-navy-900 text-white font-medium rounded-lg hover:bg-navy-700 hover:shadow-lg transition-all duration-200 flex items-center justify-center gap-2">
                            <i class="fa-solid fa-wand-magic-sparkles"></i> Generate Report
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <div class="bg-gradient-to-br from-navy-900 to-navy-700 rounded-2xl p-6 shadow-sm border border-navy-700 text-white flex flex-col hover:-translate-y-1 hover:shadow-lg transition-all duration-300">
            <div class="flex items-center gap-2 mb-4">
                <i class="fa-solid fa-lightbulb text-yellow-400"></i>
                <h2 class="text-lg font-semibold"> Macro Analysis</h2>
            </div>
            <p class="text-xs text-blue-100 mb-6">System summary for the last 30 days.</p>
            
            <div class="space-y-4 flex-1">
                <div class="flex items-start gap-3">
                    <div class="mt-1 text-green-400"><i class="fa-solid fa-circle-check"></i></div>
                    <div>
                        <h4 class="text-sm font-bold">Revenue Target Met</h4>
                        <p class="text-xs text-gray-300 mt-1">Appliance sales exceeded monthly projections by 14%. Keep current marketing strategies active.</p>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <div class="mt-1 text-orange-400"><i class="fa-solid fa-circle-exclamation"></i></div>
                    <div>
                        <h4 class="text-sm font-bold">Capital Tied Up</h4>
                        <p class="text-xs text-gray-300 mt-1">12 units of 'Premium Dining Sets' have not moved in 45 days. Suggest applying a 10% discount promo.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 animate-fade-in delay-100">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-lg font-semibold text-gray-800">6-Month Revenue vs. Profit Margin</h2>
            <div class="bg-gray-50 px-3 py-1 rounded border border-gray-200 text-xs font-medium text-gray-600">
                Year: 2026
            </div>
        </div>
        <div class="relative w-full h-[300px]">
            <canvas id="macroAnalyticsChart"></canvas>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden animate-fade-in delay-200">
        <div class="p-6 border-b border-gray-100 flex justify-between items-center">
            <h2 class="text-lg font-semibold text-gray-800">Report Archive</h2>
            <div class="relative">
                <i class="fa-solid fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 text-xs"></i>
                <input type="text" placeholder="Search archive..." class="pl-8 pr-4 py-1.5 bg-gray-50 border border-gray-200 text-gray-600 text-sm rounded-lg focus:outline-none focus:ring-2 focus:ring-navy-700 focus:bg-white w-48 sm:w-64 transition-all">
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-4 font-semibold">Report Name</th>
                        <th scope="col" class="px-6 py-4 font-semibold">Type</th>
                        <th scope="col" class="px-6 py-4 font-semibold">Generated On</th>
                        <th scope="col" class="px-6 py-4 font-semibold">Prepared By</th>
                        <th scope="col" class="px-6 py-4 font-semibold text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <tr class="bg-white hover:bg-gray-50 transition-colors duration-200">
                        <td class="px-6 py-4 font-medium text-gray-900 flex items-center gap-2">
                            <i class="fa-regular fa-file-pdf text-red-500 text-lg"></i>
                            February 2026 Monthly Sales
                        </td>
                        <td class="px-6 py-4"><span class="bg-blue-50 text-blue-700 px-2 py-1 rounded text-xs font-medium">Sales Summary</span></td>
                        <td class="px-6 py-4 text-xs">Mar 01, 2026 - 08:30 AM</td>
                        <td class="px-6 py-4 text-xs">Admin (System)</td>
                        <td class="px-6 py-4 text-right">
                            <button class="text-gray-400 hover:text-navy-900 mx-2" title="View"><i class="fa-solid fa-eye"></i></button>
                            <button class="text-gray-400 hover:text-navy-900 mx-2" title="Download"><i class="fa-solid fa-download"></i></button>
                        </td>
                    </tr>
                    <tr class="bg-white hover:bg-gray-50 transition-colors duration-200">
                        <td class="px-6 py-4 font-medium text-gray-900 flex items-center gap-2">
                            <i class="fa-regular fa-file-excel text-green-600 text-lg"></i>
                            Q1 Inventory Restock Audit
                        </td>
                        <td class="px-6 py-4"><span class="bg-orange-50 text-orange-700 px-2 py-1 rounded text-xs font-medium">Inventory Audit</span></td>
                        <td class="px-6 py-4 text-xs">Feb 15, 2026 - 10:15 AM</td>
                        <td class="px-6 py-4 text-xs">Warehouse Manager</td>
                        <td class="px-6 py-4 text-right">
                            <button class="text-gray-400 hover:text-navy-900 mx-2" title="View"><i class="fa-solid fa-eye"></i></button>
                            <button class="text-gray-400 hover:text-navy-900 mx-2" title="Download"><i class="fa-solid fa-download"></i></button>
                        </td>
                    </tr>
                    <tr class="bg-white hover:bg-gray-50 transition-colors duration-200">
                        <td class="px-6 py-4 font-medium text-gray-900 flex items-center gap-2">
                            <i class="fa-regular fa-file-pdf text-red-500 text-lg"></i>
                            Appliance Segment Performance
                        </td>
                        <td class="px-6 py-4"><span class="bg-purple-50 text-purple-700 px-2 py-1 rounded text-xs font-medium">Profit Margin</span></td>
                        <td class="px-6 py-4 text-xs">Feb 01, 2026 - 09:00 AM</td>
                        <td class="px-6 py-4 text-xs">Admin (System)</td>
                        <td class="px-6 py-4 text-right">
                            <button class="text-gray-400 hover:text-navy-900 mx-2" title="View"><i class="fa-solid fa-eye"></i></button>
                            <button class="text-gray-400 hover:text-navy-900 mx-2" title="Download"><i class="fa-solid fa-download"></i></button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-gray-100 flex justify-center">
            <button class="text-sm font-medium text-navy-700 hover:text-navy-900 hover:underline transition-colors">View All Archives</button>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const ctx = document.getElementById('macroAnalyticsChart').getContext('2d');
        
        new Chart(ctx, {
            type: 'bar', // Base type
            data: {
                labels: ['October', 'November', 'December', 'January', 'February', 'March (Current)'],
                datasets: [
                    {
                        type: 'line',
                        label: 'Net Profit (₱)',
                        data: [65000, 72000, 120000, 85000, 92000, 64000],
                        borderColor: '#10B981', // Emerald green line
                        backgroundColor: '#10B981',
                        borderWidth: 3,
                        tension: 0.3,
                        pointBackgroundColor: '#fff',
                        pointBorderColor: '#10B981',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        yAxisID: 'y1',
                    },
                    {
                        type: 'bar',
                        label: 'Gross Revenue (₱)',
                        data: [210000, 240000, 380000, 260000, 290000, 185000],
                        backgroundColor: 'rgba(1, 44, 85, 0.8)', // Navy blue bars
                        borderRadius: 4,
                        barThickness: 40,
                        yAxisID: 'y',
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: {
                    duration: 1200,
                    easing: 'easeOutQuart'
                },
                plugins: {
                    legend: {
                        position: 'top',
                        align: 'end',
                        labels: { usePointStyle: true, boxWidth: 8, font: { family: 'Inter' } }
                    },
                    tooltip: {
                        backgroundColor: '#012C55',
                        padding: 12,
                        cornerRadius: 8
                    }
                },
                scales: {
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        title: { display: true, text: 'Gross Revenue (₱)' },
                        grid: { color: '#f3f4f6', drawBorder: false },
                        border: { display: false },
                        ticks: { color: '#6b7280', font: { family: 'Inter' } }
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        title: { display: true, text: 'Net Profit (₱)' },
                        grid: { drawOnChartArea: false }, // Prevent gridline overlap
                        border: { display: false },
                        ticks: { color: '#10B981', font: { family: 'Inter' } }
                    },
                    x: {
                        grid: { display: false },
                        border: { display: false },
                        ticks: { color: '#6b7280', font: { family: 'Inter' } }
                    }
                }
            }
        });
    });
</script>
@endsection