@extends('layouts.app')

@section('title', 'Reports Module - Ken\'s Marketing')
@section('header_title', 'Report Center & Analytics')

@section('content')

<style>
    /* Custom Scrollbar for the locked table */
    .locked-table-scroll::-webkit-scrollbar { width: 6px; height: 6px; }
    .locked-table-scroll::-webkit-scrollbar-track { background: transparent; }
    .locked-table-scroll::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
    .locked-table-scroll::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
</style>

<div class="space-y-6">

    <!-- SUCCESS ALERT WITH "X" DISMISS BUTTON -->
    @if(session('success'))
        <div id="toast-success" class="fixed top-24 right-6 z-50 flex items-center w-full max-w-sm p-4 bg-white border border-gray-100 rounded-2xl shadow-xl animate-fade-in" role="alert">
            <div class="inline-flex items-center justify-center shrink-0 w-8 h-8 text-green-600 bg-green-50 rounded-lg">
                <i class="fa-solid fa-check"></i>
            </div>
            <div class="ml-3 text-sm font-medium text-gray-700 pr-4">{{ session('success') }}</div>
            <button type="button" onclick="closeToast('toast-success')" class="ml-auto -mx-1.5 -my-1.5 bg-white text-gray-400 hover:text-gray-900 rounded-lg p-1.5 hover:bg-gray-50 inline-flex items-center justify-center h-8 w-8 transition-colors">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    @endif

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
            
            <form action="{{ route('reports.generate') }}" method="POST" class="space-y-5">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Select Report Type</label>
                        <select name="report_type" class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-navy-700 bg-gray-50 focus:bg-white cursor-pointer transition-colors" required>
                            <option value="" disabled selected>Choose report type...</option>
                            <option value="sales_summary">Sales & Revenue Summary</option>
                            <option value="inventory_audit">Inventory & Stock Audit</option>
                            <option value="fast_slow">Fast/Slow Moving Products (DSS)</option>
                            <option value="profit_margin">Profit Margin Analysis</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Timeframe</label>
                        <select name="timeframe" class="w-full px-3 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-navy-700 bg-gray-50 focus:bg-white cursor-pointer transition-colors" required>
                            <option value="today">Today</option>
                            <option value="this_week">This Week</option>
                            <option value="this_month" selected>This Month</option>
                            <option value="last_month">Last Month</option>
                            @php
                                $quarterLabels = [
                                    1 => 'Jan - Mar',
                                    2 => 'Apr - Jun',
                                    3 => 'Jul - Sep',
                                    4 => 'Oct - Dec',
                                ];
                            @endphp
                            @for($quarter = 1; $quarter <= now()->quarter; $quarter++)
                                <option value="q{{ $quarter }}">Quarter {{ $quarter }} ({{ $quarterLabels[$quarter] }})</option>
                            @endfor
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

        <!-- ========================================== -->
        <!-- DYNAMIC MACRO ANALYSIS                     -->
        <!-- ========================================== -->
        <div class="bg-gradient-to-br from-navy-900 to-navy-700 rounded-2xl p-6 shadow-sm border border-navy-700 text-white flex flex-col hover:-translate-y-1 hover:shadow-lg transition-all duration-300">
            <div class="flex items-center gap-2 mb-4">
                <i class="fa-solid fa-lightbulb text-yellow-400"></i>
                <h2 class="text-lg font-semibold"> Macro Analysis</h2>
            </div>
            <p class="text-xs text-blue-100 mb-6">System summary for the last 30 days.</p>
            
            <div class="space-y-4 flex-1">
                <!-- Insight 1: Revenue Target -->
                <div class="flex items-start gap-3">
                    @if($revenueGrowth >= 0)
                        <div class="mt-1 text-green-400"><i class="fa-solid fa-circle-check"></i></div>
                        <div>
                            <h4 class="text-sm font-bold">Revenue Target Met</h4>
                            <p class="text-xs text-gray-300 mt-1">Sales exceeded last month by {{ number_format($revenueGrowth, 1) }}%. Keep current marketing strategies active.</p>
                        </div>
                    @else
                        <div class="mt-1 text-red-400"><i class="fa-solid fa-circle-exclamation"></i></div>
                        <div>
                            <h4 class="text-sm font-bold">Revenue Target Missed</h4>
                            <p class="text-xs text-gray-300 mt-1">Sales are down {{ number_format(abs($revenueGrowth), 1) }}% compared to last month. Review pricing strategies.</p>
                        </div>
                    @endif
                </div>
                
                <!-- Insight 2: Stagnant Capital -->
                <div class="flex items-start gap-3">
                    <div class="mt-1 text-orange-400"><i class="fa-solid fa-circle-exclamation"></i></div>
                    <div>
                        <h4 class="text-sm font-bold">Capital Tied Up</h4>
                        @if($stagnantProduct)
                            <p class="text-xs text-gray-300 mt-1">{{ $stagnantProduct->in_stock }} units of '{{ $stagnantProduct->product_name }}' have not moved in 45 days. Suggest applying a discount promo.</p>
                        @else
                            <p class="text-xs text-gray-300 mt-1">Capital allocation is healthy. No severely stagnant items detected in the last 45 days.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <!-- ========================================== -->

    </div>

    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 animate-fade-in delay-100">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-lg font-semibold text-gray-800">6-Month Revenue vs. Profit Margin</h2>
            <div class="bg-gray-50 px-3 py-1 rounded border border-gray-200 text-xs font-medium text-gray-600">
                Year: {{ date('Y') }}
            </div>
        </div>
        <div class="relative w-full h-[300px]">
            <canvas id="macroAnalyticsChart"></canvas>
        </div>
    </div>

    <!-- REPORT ARCHIVE SECTION -->
    <div id="report-archive-container" class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden animate-fade-in delay-200">
        <div class="p-6 border-b border-gray-100 flex justify-between items-center">
            <h2 class="text-lg font-semibold text-gray-800">Report Archive</h2>
            <div class="relative">
                <i class="fa-solid fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 text-xs"></i>
                <input type="text" placeholder="Search archive..." class="pl-8 pr-4 py-1.5 bg-gray-50 border border-gray-200 text-gray-600 text-sm rounded-lg focus:outline-none focus:ring-2 focus:ring-navy-700 focus:bg-white w-48 sm:w-64 transition-all">
            </div>
        </div>
        
        <!-- ULTIMATE CSS FIX: Exact locked height guarantees 0 layout shift. Inner scroll handles overflow -->
        <div class="overflow-x-auto overflow-y-auto locked-table-scroll" style="height: 550px; overflow-anchor: none;">
            <table class="w-full text-sm text-left text-gray-500 relative">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 sticky top-0 shadow-sm z-10 border-b border-gray-200">
                    <tr>
                        <th scope="col" class="px-6 py-4 font-semibold">Report Name</th>
                        <th scope="col" class="px-6 py-4 font-semibold">Type</th>
                        <th scope="col" class="px-6 py-4 font-semibold">Generated On</th>
                        <th scope="col" class="px-6 py-4 font-semibold">Prepared By</th>
                        <th scope="col" class="px-6 py-4 font-semibold text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($archives as $archive)
                        <tr class="bg-white hover:bg-gray-50 transition-colors duration-200">
                            <td class="px-6 py-4 font-medium text-gray-900 flex items-center gap-2">
                                @if($archive->format == 'pdf')
                                    <i class="fa-regular fa-file-pdf text-red-500 text-lg"></i>
                                @else
                                    <i class="fa-regular fa-file-excel text-green-600 text-lg"></i>
                                @endif
                                {{ $archive->report_name }}
                            </td>
                            <td class="px-6 py-4">
                                @if($archive->report_type == 'sales_summary')
                                    <span class="bg-blue-50 text-blue-700 px-2 py-1 rounded text-xs font-medium">Sales Summary</span>
                                @elseif($archive->report_type == 'inventory_audit')
                                    <span class="bg-orange-50 text-orange-700 px-2 py-1 rounded text-xs font-medium">Inventory Audit</span>
                                @elseif($archive->report_type == 'fast_slow')
                                    <span class="bg-yellow-50 text-yellow-700 px-2 py-1 rounded text-xs font-medium">Fast/Slow Moving</span>
                                @else
                                    <span class="bg-purple-50 text-purple-700 px-2 py-1 rounded text-xs font-medium">Profit Margin</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-xs">{{ $archive->created_at->format('M d, Y - h:i A') }}</td>
                            <td class="px-6 py-4 text-xs">{{ $archive->prepared_by }}</td>
                            
                            <td class="px-6 py-4 text-right flex justify-end items-center gap-2">
                                <!-- SMART BUTTONS -->
                                @if($archive->format == 'pdf')
                                    <a href="{{ route('reports.view', $archive->id) }}" target="_blank" class="inline-flex items-center px-3 py-1.5 bg-red-50 text-red-600 hover:bg-red-100 rounded-md transition-colors text-xs font-semibold border border-red-100" title="View / Print PDF">
                                        <i class="fa-solid fa-file-pdf mr-1.5"></i> Open PDF
                                    </a>
                                @else
                                    <a href="{{ route('reports.download', $archive->id) }}" class="inline-flex items-center px-3 py-1.5 bg-green-50 text-green-700 hover:bg-green-100 rounded-md transition-colors text-xs font-semibold border border-green-100" title="Download Excel (.csv)">
                                        <i class="fa-solid fa-file-excel mr-1.5"></i> Download
                                    </a>
                                @endif

                                <!-- STANDARD FORM DELETE BUTTON -->
                                <form action="{{ route('reports.delete', $archive->id) }}" method="POST" class="inline-block m-0 p-0" onsubmit="return confirm('Are you sure you want to permanently delete this report from the archive?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center px-2 py-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-md transition-colors" title="Delete Report">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500">No reports have been generated yet. Use the builder above to create one!</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination sits securely below the locked table block -->
        <div class="border-t border-gray-100 bg-gray-50 rounded-b-2xl">
            @if(method_exists($archives, 'links'))
                <div class="p-4 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div class="text-sm text-gray-500 font-medium">
                        Page <span class="text-navy-900 font-bold">{{ $archives->currentPage() }}</span> of <span class="text-navy-900 font-bold">{{ $archives->lastPage() }}</span>
                    </div>
                    <div class="w-full sm:w-auto overflow-x-auto">
                        {{ $archives->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
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

    document.addEventListener("DOMContentLoaded", function() {
        const ctx = document.getElementById('macroAnalyticsChart').getContext('2d');
        
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: @json($labels), 
                datasets: [
                    {
                        type: 'line',
                        label: 'Net Profit (₱)',
                        data: @json($profitData),
                        borderColor: '#10B981', 
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
                        data: @json($revenueData),
                        backgroundColor: 'rgba(1, 44, 85, 0.8)', 
                        borderRadius: 4,
                        barThickness: 40,
                        yAxisID: 'y',
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: { duration: 1200, easing: 'easeOutQuart' },
                plugins: {
                    legend: { position: 'top', align: 'end', labels: { usePointStyle: true, boxWidth: 8, font: { family: 'Inter' } } },
                    tooltip: { backgroundColor: '#012C55', padding: 12, cornerRadius: 8 }
                },
                scales: {
                    y: { type: 'linear', display: true, position: 'left', title: { display: true, text: 'Gross Revenue (₱)' }, grid: { color: '#f3f4f6', drawBorder: false }, border: { display: false }, ticks: { color: '#6b7280', font: { family: 'Inter' } } },
                    y1: { type: 'linear', display: true, position: 'right', title: { display: true, text: 'Net Profit (₱)' }, grid: { drawOnChartArea: false }, border: { display: false }, ticks: { color: '#10B981', font: { family: 'Inter' } } },
                    x: { grid: { display: false }, border: { display: false }, ticks: { color: '#6b7280', font: { family: 'Inter' } } }
                }
            }
        });
    });

    // --- BULLETPROOF AJAX PAGINATION (NO JUMPS) ---
    
    let currentFetchId = 0; 
    let currentAbortController = null;

    function performAjaxFetch(url) {
        const tableContainer = document.getElementById('report-archive-container');
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
                tableContainer.innerHTML = doc.getElementById('report-archive-container').innerHTML;
                
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
        const tableContainer = document.getElementById('report-archive-container');

        if (tableContainer) {
            tableContainer.addEventListener('click', function(e) {
                const link = e.target.closest('a');
                
                if (link && link.href && link.href.includes('page=')) {
                    e.preventDefault(); 
                    
                    // ULTIMATE FIX: Blur the active button so the browser doesn't panic when it gets deleted!
                    if (document.activeElement) {
                        document.activeElement.blur();
                    }

                    performAjaxFetch(link.href);
                }
            });
        }
    });
</script>
@endsection
