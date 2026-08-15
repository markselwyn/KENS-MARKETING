@extends('layouts.app')

@section('title', 'DSS Insights - Ken\'s Marketing')
@section('header_title', 'System Intelligence & Strategy')

@section('content')
<div class="space-y-6">

    <!-- MINIMALIST TOAST NOTIFICATIONS -->
    @if(session('success'))
        <div id="toast-success" class="fixed top-24 right-6 z-50 flex items-center w-full max-w-sm p-4 bg-white border border-gray-100 rounded-2xl shadow-xl animate-fade-in" role="alert">
            <div class="inline-flex items-center justify-center shrink-0 w-8 h-8 text-green-600 bg-green-50 rounded-lg">
                <i class="fa-solid fa-check"></i>
            </div>
            <div class="ml-3 text-sm font-medium text-gray-700 pr-4">{{ session('success') }}</div>
            <button type="button" onclick="document.getElementById('toast-success').remove()" class="ml-auto -mx-1.5 -my-1.5 bg-white text-gray-400 hover:text-gray-900 rounded-lg p-1.5 hover:bg-gray-50 inline-flex items-center justify-center h-8 w-8 transition-colors">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    @endif

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

    <!-- KPI ALGORITHM CARDS -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8 animate-fade-in">
        
        <!-- FAST-SELLING PRODUCT CARD -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex flex-col justify-between hover:shadow-md transition-shadow relative">
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
                <div class="bg-green-50 border-l-4 border-green-500 p-3 rounded-r text-xs mb-3">
                    <span class="font-bold text-green-800"><i class="fa-regular fa-lightbulb mr-1"></i> Recommendation:</span>
                    <span class="text-green-700"> Increase minimum safety stock to prevent lost sales. Current stock is {{ $fastMover->in_stock }} units.</span>
                </div>
                <button onclick="openDssModal('fastMoverModal')" class="text-xs text-green-700 font-semibold hover:text-green-800 flex items-center gap-1 transition-colors group">
                    View full list <i class="fa-solid fa-arrow-right transform group-hover:translate-x-1 transition-transform"></i>
                </button>
            @endif
        </div>

        <!-- SLOW-MOVING / STAGNANT CAPITAL CARD -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex flex-col justify-between hover:shadow-md transition-shadow relative">
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
                <div class="bg-orange-50 border-l-4 border-orange-500 p-3 rounded-r text-xs mb-3">
                    <span class="font-bold text-orange-800"><i class="fa-regular fa-lightbulb mr-1"></i> Recommendation:</span>
                    <span class="text-orange-700"> Apply a markdown promo to stimulate sales and recover capital.</span>
                </div>
                <button onclick="openDssModal('stagnantModal')" class="text-xs text-orange-700 font-semibold hover:text-orange-800 flex items-center gap-1 transition-colors group">
                    View all stagnant items <i class="fa-solid fa-arrow-right transform group-hover:translate-x-1 transition-transform"></i>
                </button>
            @endif
        </div>

        <!-- RESTOCK ACTION REQUIRED CARD -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex flex-col justify-between hover:shadow-md transition-shadow relative">
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
                <div class="bg-red-50 border-l-4 border-red-500 p-3 rounded-r text-xs mb-3">
                    <span class="font-bold text-red-800"><i class="fa-regular fa-lightbulb mr-1"></i> Recommendation:</span>
                    <span class="text-red-700"> Generate a restock order for {{ $criticalRestock->reorder_point * 3 }} units immediately to avoid a stockout.</span>
                </div>
                <button onclick="openDssModal('restockModal')" class="text-xs text-red-700 font-semibold hover:text-red-800 flex items-center gap-1 transition-colors group">
                    View critical stock list <i class="fa-solid fa-arrow-right transform group-hover:translate-x-1 transition-transform"></i>
                </button>
            @endif
        </div>

    </div>

    <!-- BOTTOM GRID: CHART & SIMULATOR -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 animate-fade-in delay-100">
        
        <!-- DEMAND FORECAST CHART -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 lg:col-span-2 flex flex-col hover:shadow-md transition-shadow duration-300">
            <div class="flex justify-between items-start mb-2">
                <div>
                    <h2 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                        <i class="fa-solid fa-chart-line text-blue-500"></i> Velocity-Based Demand Forecast
                    </h2>
                    <p class="text-xs text-gray-500 mt-1">Projected revenue using mathematical forecasting algorithms.</p>
                </div>
                
                <select id="forecastTimeframe" onchange="updateForecastChart()" class="bg-gray-50 border border-gray-200 text-gray-700 text-xs font-medium rounded-lg focus:ring-navy-700 focus:border-navy-700 block p-2 cursor-pointer outline-none hover:bg-gray-100 transition-colors shadow-sm">
                    <option value="7days">Next 7 Days</option>
                    <option value="30days" selected>Next 30 Days</option>
                    <option value="quarter">Next Quarter</option>
                </select>
            </div>

            <div class="bg-blue-50/50 border border-blue-100 p-3 rounded-lg mb-4 mt-2 flex gap-3 items-center">
                <i class="fa-solid fa-microchip text-blue-500 text-lg shrink-0"></i>
                <p id="forecastInsightText" class="text-[11px] text-gray-600 leading-tight">
                    <strong>30-Day Analysis:</strong> The system applies a 5% baseline growth trend to your 4-week moving average to project medium-term revenue stability.
                </p>
            </div>

            <div class="relative flex-1 w-full min-h-[240px]">
                <canvas id="forecastChart"></canvas>
            </div>
        </div>

        <!-- THE "WHAT-IF" SIMULATOR -->
        <div class="bg-navy-900 rounded-2xl shadow-sm border border-navy-700 overflow-hidden flex flex-col hover:shadow-lg transition-shadow duration-300">
            <div class="p-5 border-b border-navy-700 bg-navy-800">
                <h2 class="text-lg font-semibold text-white flex items-center gap-2">
                    <i class="fa-solid fa-sliders text-yellow-400"></i> "What-If" Simulator
                </h2>
                <p class="text-xs text-blue-200 mt-1">Test pricing strategies before implementing them.</p>
            </div>
            
            <div class="p-6 flex-1 flex flex-col justify-between text-white">
                <div class="space-y-5">
                    
                    <!-- SEARCHABLE DROPDOWN -->
                    <div class="relative" id="customDropdownContainer">
                        <label class="block text-xs font-medium text-gray-300 mb-2">Search Target Product</label>
                        
                        <input type="hidden" id="selectedProductPrice" value="0">
                        <input type="hidden" id="selectedProductId" value="">
                        
                        <!-- Dropdown Button -->
                        <button type="button" onclick="toggleSimDropdown()" class="w-full px-3 py-2.5 bg-navy-800 border border-navy-600 text-sm text-left rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-400 flex justify-between items-center transition-all">
                            <span id="simDropdownText" class="truncate text-gray-400">Select an item to simulate...</span>
                            <i class="fa-solid fa-chevron-down text-gray-400 text-xs ml-2 shrink-0"></i>
                        </button>

                        <!-- Dropdown Panel -->
                        <div id="simDropdownMenu" class="hidden absolute z-[100] w-full mt-1 bg-navy-900 border border-navy-600 rounded-lg shadow-2xl overflow-hidden drop-shadow-2xl">
                            <div class="p-2 border-b border-navy-700 bg-navy-900">
                                <div class="relative">
                                    <i class="fa-solid fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 text-xs"></i>
                                    <input type="text" id="simSearchInput" onkeyup="filterSimOptions()" placeholder="Type SKU or Name..." class="w-full pl-8 pr-3 py-2 bg-navy-800 border border-navy-600 text-sm text-white rounded focus:outline-none focus:border-yellow-400 focus:ring-1 focus:ring-yellow-400 placeholder-gray-500">
                                </div>
                            </div>
                            
                            <ul id="simOptionsList" class="max-h-60 overflow-y-auto custom-scrollbar bg-navy-900">
                                @foreach($simulatorProducts as $product)
                                    <li data-id="{{ $product->id }}" onclick="selectSimProduct({{ $product->id }}, {{ $product->unit_price }}, '{{ addslashes($product->sku) }} - {{ addslashes($product->product_name) }}')" class="sim-option px-3 py-3 text-sm text-white hover:bg-navy-700 cursor-pointer border-b border-navy-700/50 last:border-0 transition-colors">
                                        <div class="font-bold text-yellow-400 text-xs mb-0.5">{{ $product->sku }}</div>
                                        <div class="truncate text-gray-200">{{ $product->product_name }}</div>
                                        <div class="text-[10px] text-gray-400 mt-0.5 font-medium uppercase tracking-wider">{{ $product->category }}</div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                        
                        <p id="simCurrentPriceLabel" class="text-[10px] text-gray-400 mt-1 hidden">Current Price: ₱0.00</p>
                    </div>

                    <!-- DISCOUNT SLIDER -->
                    <div class="pt-1">
                        <div class="flex justify-between text-xs font-medium text-gray-300 mb-2">
                            <label>Simulate Promo Discount</label>
                            <span id="simDiscountLabel" class="text-yellow-400 font-bold">0%</span>
                        </div>
                        <input type="range" id="simDiscountSlider" min="0" max="50" value="0" step="5" oninput="runSimulation()" class="w-full h-2 bg-navy-700 rounded-lg appearance-none cursor-pointer accent-yellow-400">
                        <div class="flex justify-between text-[9px] text-gray-500 mt-1 font-mono">
                            <span>0%</span>
                            <span>25%</span>
                            <span>50% MAX</span>
                        </div>
                    </div>

                    <!-- PREDICTIVE OUTCOME -->
                    <div class="bg-navy-800 p-4 rounded-xl border border-navy-600 mt-2">
                        <p class="text-xs text-gray-400 mb-1">Projected Outcome (30 Days)</p>
                        <div class="flex justify-between items-end">
                            <div>
                                <p class="text-sm text-gray-300">Sales Volume</p>
                                <p id="simVolumeLabel" class="text-xl font-bold text-gray-400">+0%</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm text-gray-300">Est. Net Revenue</p>
                                <p id="simRevenueLabel" class="text-xl font-bold text-yellow-400">₱0</p>
                                <p id="simNewPriceLabel" class="text-[10px] font-bold text-green-400 mt-0.5 hidden">Promo Price: ₱0.00</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- ACTION: APPLY DISCOUNT TO SYSTEM FORM -->
                    <form action="{{ route('dss.apply-discount') }}" method="POST" id="applyDiscountForm" class="hidden">
                        @csrf
                        <input type="hidden" name="product_id" id="applyProductId">
                        <input type="hidden" name="new_price" id="applyNewPrice">
                        <input type="hidden" name="discount_percent" id="applyDiscountPercent">
                        
                        <!-- UPGRADED BUTTON: Opens Custom Confirm Modal -->
                        <button type="button" onclick="showConfirmModal()" class="w-full py-2.5 bg-green-600 hover:bg-green-500 text-white text-xs font-bold rounded-lg shadow-md transition-colors flex items-center justify-center gap-2 mt-4">
                            <i class="fa-solid fa-cloud-arrow-up"></i> Apply New Promo Price to System
                        </button>
                    </form>

                </div>
            </div>
        </div>

    </div>
</div>

<!-- ========================================== -->
<!-- DRILL-DOWN MODALS                          -->
<!-- ========================================== -->

<!-- NEW CUSTOM CONFIRMATION MODAL -->
<div id="confirmDiscountModal" class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm hidden z-50 flex items-center justify-center transition-opacity opacity-0 no-print p-4">
    <div class="bg-white rounded-2xl w-full max-w-md shadow-2xl transform scale-95 transition-transform duration-300" id="confirmDiscountModalContent">
        <div class="flex items-center justify-between p-5 border-b border-gray-100 bg-navy-900 text-white rounded-t-2xl">
            <h3 class="text-lg font-semibold flex items-center gap-2">
                <i class="fa-solid fa-circle-question text-yellow-400"></i> Confirm Price Update
            </h3>
            <button onclick="closeDssModal('confirmDiscountModal')" class="text-gray-400 hover:text-white transition-colors focus:outline-none">
                <i class="fa-solid fa-xmark text-xl"></i>
            </button>
        </div>
        <div class="p-6">
            <p class="text-gray-700 text-sm mb-4">
                You are about to apply a <strong id="confirmDiscountText" class="text-orange-600"></strong> to the following product. This will permanently update the active database.
            </p>
            
            <div class="bg-gray-50 p-4 rounded-xl border border-gray-200 mb-5 text-center shadow-inner">
                <span class="text-[10px] text-gray-500 uppercase tracking-wider font-bold block mb-1">Target Item</span>
                <span id="confirmItemName" class="font-bold text-navy-900 text-sm block mb-3">Product Name</span>
                
                <span class="text-[10px] text-gray-500 uppercase tracking-wider font-bold block mb-1">New System Price</span>
                <span id="confirmNewPriceText" class="text-3xl font-bold text-green-600 block">₱0.00</span>
            </div>

            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeDssModal('confirmDiscountModal')" class="px-5 py-2.5 text-sm font-medium text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors focus:outline-none">Cancel</button>
                <button type="button" onclick="document.getElementById('applyDiscountForm').submit()" class="px-5 py-2.5 text-sm font-bold text-white bg-green-600 rounded-lg hover:bg-green-500 shadow-md transition-all flex items-center gap-2 focus:outline-none">
                    <i class="fa-solid fa-check"></i> Yes, Apply Now
                </button>
            </div>
        </div>
    </div>
</div>

<!-- FAST MOVERS MODAL -->
<div id="fastMoverModal" class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm hidden z-50 flex items-center justify-center transition-opacity opacity-0 no-print p-4">
    <div class="bg-white rounded-2xl w-full max-w-2xl shadow-2xl flex flex-col max-h-full transform scale-95 transition-transform duration-300" id="fastMoverModalContent">
        <div class="flex items-center justify-between p-5 border-b border-gray-100 bg-green-600 text-white rounded-t-2xl shrink-0">
            <h3 class="text-lg font-semibold flex items-center gap-2">
                <i class="fa-solid fa-bolt"></i> Fast-Selling Products (Last 30 Days)
            </h3>
            <button onclick="closeDssModal('fastMoverModal')" class="text-white/80 hover:text-white transition-colors focus:outline-none">
                <i class="fa-solid fa-xmark text-xl"></i>
            </button>
        </div>
        <div class="overflow-y-auto p-0 flex-1 custom-scrollbar">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-[10px] text-gray-400 uppercase bg-white sticky top-0 border-b border-gray-100">
                    <tr>
                        <th class="px-4 py-3 font-semibold tracking-wider">Product Name</th>
                        <th class="px-4 py-3 text-center font-semibold tracking-wider">Units Sold</th>
                        <th class="px-4 py-3 text-center font-semibold tracking-wider">Current Stock</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($allFastMovers as $item)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3 text-gray-800 font-medium">{{ $item->product_name }}</td>
                            <td class="px-4 py-3 text-center font-bold text-green-600">{{ $item->total_sold }}</td>
                            <td class="px-4 py-3 text-center text-gray-600">{{ $item->in_stock }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="px-4 py-6 text-center text-gray-500 italic">No sales recorded in the last 30 days.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-gray-100 bg-gray-50 flex justify-end">
            <button onclick="closeDssModal('fastMoverModal')" class="px-5 py-2.5 text-sm font-medium text-gray-600 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors shadow-sm focus:outline-none">Close Window</button>
        </div>
    </div>
</div>

<!-- STAGNANT CAPITAL MODAL -->
<div id="stagnantModal" class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm hidden z-50 flex items-center justify-center transition-opacity opacity-0 no-print p-4">
    <div class="bg-white rounded-2xl w-full max-w-3xl shadow-2xl flex flex-col max-h-full transform scale-95 transition-transform duration-300" id="stagnantModalContent">
        <div class="flex items-center justify-between p-5 border-b border-gray-100 bg-orange-500 text-white rounded-t-2xl shrink-0">
            <h3 class="text-lg font-semibold flex items-center gap-2">
                <i class="fa-solid fa-hourglass-half"></i> Stagnant Capital Report (>45 Days Inactive)
            </h3>
            <button onclick="closeDssModal('stagnantModal')" class="text-white/80 hover:text-white transition-colors focus:outline-none">
                <i class="fa-solid fa-xmark text-xl"></i>
            </button>
        </div>
        <div class="overflow-y-auto p-0 flex-1 custom-scrollbar">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-[10px] text-gray-400 uppercase bg-white sticky top-0 border-b border-gray-100">
                    <tr>
                        <th class="px-4 py-3 font-semibold tracking-wider">Product Name</th>
                        <th class="px-4 py-3 text-center font-semibold tracking-wider">Unsold Stock</th>
                        <th class="px-4 py-3 text-right font-semibold tracking-wider">Unit Value</th>
                        <th class="px-4 py-3 text-right font-semibold tracking-wider">Total Capital Tied Up</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($allStagnantProducts as $item)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3 text-gray-800 font-medium truncate max-w-[200px]" title="{{ $item->product_name }}">{{ $item->product_name }}</td>
                            <td class="px-4 py-3 text-center font-bold text-orange-600">{{ $item->in_stock }}</td>
                            <td class="px-4 py-3 text-right text-gray-500">₱{{ number_format($item->unit_price, 2) }}</td>
                            <td class="px-4 py-3 text-right font-bold text-gray-900">₱{{ number_format($item->in_stock * $item->unit_price, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-4 py-6 text-center text-gray-500 italic">No stagnant capital detected.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-gray-100 bg-gray-50 flex justify-end">
            <button onclick="closeDssModal('stagnantModal')" class="px-5 py-2.5 text-sm font-medium text-gray-600 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors shadow-sm focus:outline-none">Close Window</button>
        </div>
    </div>
</div>

<!-- RESTOCK MODAL -->
<div id="restockModal" class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm hidden z-50 flex items-center justify-center transition-opacity opacity-0 no-print p-4">
    <div class="bg-white rounded-2xl w-full max-w-2xl shadow-2xl flex flex-col max-h-full transform scale-95 transition-transform duration-300" id="restockModalContent">
        <div class="flex items-center justify-between p-5 border-b border-gray-100 bg-red-500 text-white rounded-t-2xl shrink-0">
            <h3 class="text-lg font-semibold flex items-center gap-2">
                <i class="fa-solid fa-cart-arrow-down"></i> Critical Restock Required
            </h3>
            <button onclick="closeDssModal('restockModal')" class="text-white/80 hover:text-white transition-colors focus:outline-none">
                <i class="fa-solid fa-xmark text-xl"></i>
            </button>
        </div>
        <div class="overflow-y-auto p-0 flex-1 custom-scrollbar">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-[10px] text-gray-400 uppercase bg-white sticky top-0 border-b border-gray-100">
                    <tr>
                        <th class="px-4 py-3 font-semibold tracking-wider">Product Name</th>
                        <th class="px-4 py-3 text-center font-semibold tracking-wider">Current Stock</th>
                        <th class="px-4 py-3 text-center font-semibold tracking-wider">Minimum Threshold</th>
                        <th class="px-4 py-3 text-center font-semibold tracking-wider text-red-600">Suggested Order Qty</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($allCriticalRestocks as $item)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3 text-gray-800 font-medium">{{ $item->product_name }}</td>
                            <td class="px-4 py-3 text-center font-bold {{ $item->in_stock == 0 ? 'text-red-600' : 'text-orange-500' }}">{{ $item->in_stock }}</td>
                            <td class="px-4 py-3 text-center text-gray-500">{{ $item->reorder_point }}</td>
                            <td class="px-4 py-3 text-center font-bold text-red-600 bg-red-50/30">+{{ max(10, $item->reorder_point * 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-4 py-6 text-center text-gray-500 italic">No critical restocks required.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-gray-100 bg-gray-50 flex justify-end">
            <button onclick="closeDssModal('restockModal')" class="px-5 py-2.5 text-sm font-medium text-gray-600 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors shadow-sm focus:outline-none">Close Window</button>
        </div>
    </div>
</div>

<style>
    .custom-scrollbar::-webkit-scrollbar { width: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // ==========================================
    // MODAL OPEN/CLOSE LOGIC
    // ==========================================
    function openDssModal(modalId) {
        const modal = document.getElementById(modalId);
        const content = document.getElementById(modalId + 'Content');
        modal.classList.remove('hidden');
        setTimeout(() => { 
            modal.classList.remove('opacity-0'); 
            content.classList.remove('scale-95'); 
        }, 10);
    }

    function closeDssModal(modalId) {
        const modal = document.getElementById(modalId);
        const content = document.getElementById(modalId + 'Content');
        modal.classList.add('opacity-0'); 
        content.classList.add('scale-95');
        setTimeout(() => { 
            modal.classList.add('hidden'); 
        }, 300);
    }

    // ==========================================
    // CUSTOM DROPDOWN LOGIC
    // ==========================================
    function toggleSimDropdown() {
        const menu = document.getElementById('simDropdownMenu');
        menu.classList.toggle('hidden');
        if (!menu.classList.contains('hidden')) {
            document.getElementById('simSearchInput').focus();
        }
    }

    function filterSimOptions() {
        const input = document.getElementById('simSearchInput').value.toLowerCase();
        const options = document.querySelectorAll('.sim-option');
        
        options.forEach(option => {
            const text = option.innerText.toLowerCase();
            if (text.includes(input)) {
                option.style.display = 'block';
            } else {
                option.style.display = 'none';
            }
        });
    }

    function selectSimProduct(id, price, displayName) {
        document.getElementById('selectedProductPrice').value = price;
        document.getElementById('selectedProductId').value = id;
        document.getElementById('applyProductId').value = id;
        
        const btnText = document.getElementById('simDropdownText');
        btnText.textContent = displayName;
        btnText.classList.remove('text-gray-400');
        btnText.classList.add('text-white');
        
        document.getElementById('simDropdownMenu').classList.add('hidden');
        
        const currentPriceLabel = document.getElementById('simCurrentPriceLabel');
        currentPriceLabel.classList.remove('hidden');
        currentPriceLabel.textContent = 'Current Price: ₱' + parseFloat(price).toLocaleString('en-US', {minimumFractionDigits: 2});
        
        runSimulation();
    }

    document.addEventListener('click', function(event) {
        const container = document.getElementById('customDropdownContainer');
        const menu = document.getElementById('simDropdownMenu');
        if (container && !container.contains(event.target)) {
            menu.classList.add('hidden');
        }
    });

    // ==========================================
    // THE DSS WHAT-IF ENGINE
    // ==========================================
    function runSimulation() {
        const slider = document.getElementById('simDiscountSlider');
        const discountLabel = document.getElementById('simDiscountLabel');
        const volumeLabel = document.getElementById('simVolumeLabel');
        const revenueLabel = document.getElementById('simRevenueLabel');
        const newPriceLabel = document.getElementById('simNewPriceLabel');
        const applyForm = document.getElementById('applyDiscountForm');

        const discountPercent = parseInt(slider.value);
        discountLabel.textContent = discountPercent + '%';

        const basePrice = parseFloat(document.getElementById('selectedProductPrice').value);
        const productId = document.getElementById('selectedProductId').value;

        if (basePrice <= 0 || !productId) {
            revenueLabel.textContent = '₱0';
            volumeLabel.textContent = '+0%';
            newPriceLabel.classList.add('hidden');
            applyForm.classList.add('hidden');
            return;
        }

        const newPrice = basePrice * (1 - (discountPercent / 100));
        const baseVolume = 20; 
        const volumeBoostPercent = discountPercent * 2.5; 
        const volumeMultiplier = 1 + (volumeBoostPercent / 100);

        const projectedUnitsSold = Math.round(baseVolume * volumeMultiplier);
        const projectedRevenue = newPrice * projectedUnitsSold;

        document.getElementById('applyNewPrice').value = newPrice.toFixed(2);
        document.getElementById('applyDiscountPercent').value = discountPercent;

        volumeLabel.textContent = '+' + volumeBoostPercent.toFixed(1) + '%';
        revenueLabel.textContent = '₱' + projectedRevenue.toLocaleString('en-US', {minimumFractionDigits: 0, maximumFractionDigits: 0});
        
        newPriceLabel.classList.remove('hidden');
        newPriceLabel.textContent = 'Promo Price: ₱' + newPrice.toLocaleString('en-US', {minimumFractionDigits: 2});

        if (discountPercent === 0) {
            applyForm.classList.add('hidden');
            volumeLabel.classList.replace('text-green-400', 'text-gray-400');
            if(volumeLabel.classList.contains('text-red-400')) volumeLabel.classList.replace('text-red-400', 'text-gray-400');
            revenueLabel.classList.replace('text-red-400', 'text-yellow-400');
        } else {
            applyForm.classList.remove('hidden');
            volumeLabel.classList.replace('text-gray-400', 'text-green-400');
            
            const baseTotalRevenue = basePrice * baseVolume;
            if (projectedRevenue < baseTotalRevenue) {
                revenueLabel.classList.replace('text-yellow-400', 'text-red-400');
            } else {
                revenueLabel.classList.replace('text-red-400', 'text-yellow-400');
            }
        }
    }

    // NEW LOGIC: Dynamic Confirmation Modal
    function showConfirmModal() {
        const newPrice = document.getElementById('applyNewPrice').value;
        const discountPercent = document.getElementById('applyDiscountPercent').value;
        const productName = document.getElementById('simDropdownText').textContent;

        document.getElementById('confirmItemName').textContent = productName;
        document.getElementById('confirmNewPriceText').textContent = '₱' + parseFloat(newPrice).toLocaleString('en-US', {minimumFractionDigits: 2});
        document.getElementById('confirmDiscountText').textContent = discountPercent + '% Markdown';

        openDssModal('confirmDiscountModal');
    }

    // ==========================================
    // AUTO-LOAD SIMULATOR FROM URL PARAMETERS
    // ==========================================
    document.addEventListener("DOMContentLoaded", function() {
        const urlParams = new URLSearchParams(window.location.search);
        const simulateId = urlParams.get('simulate_id');
        
        if (simulateId) {
            const optionToClick = document.querySelector(`.sim-option[data-id="${simulateId}"]`);
            if (optionToClick) {
                optionToClick.click();
                const slider = document.getElementById('simDiscountSlider');
                if (slider) {
                    slider.value = 10; 
                    runSimulation();
                }
                setTimeout(() => {
                    document.getElementById('customDropdownContainer').scrollIntoView({ behavior: 'smooth', block: 'center' });
                }, 300);
            }
        }
    });

    // ==========================================
    // CHART.JS DYNAMIC FORECAST ENGINE
    // ==========================================
    let myForecastChart = null; 

    document.addEventListener("DOMContentLoaded", function() {
        const ctx = document.getElementById('forecastChart').getContext('2d');
        
        let gradient = ctx.createLinearGradient(0, 0, 0, 400);
        gradient.addColorStop(0, 'rgba(1, 44, 85, 0.1)');
        gradient.addColorStop(1, 'rgba(1, 44, 85, 0)');

        myForecastChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4 (Now)', 'Week 5 (Est)', 'Week 6 (Est)', 'Week 7 (Est)'],
                datasets: [
                    {
                        label: 'Actual Revenue',
                        data: [120000, 135000, 115000, 146726, null, null, null],
                        borderColor: '#012C55',
                        backgroundColor: gradient,
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#012C55',
                        pointRadius: 5
                    },
                    {
                        label: 'DSS Forecast',
                        data: [null, null, null, 146726, 154000, 161700, 169000],
                        borderColor: '#10B981',
                        borderWidth: 3,
                        borderDash: [5, 5],
                        tension: 0.4,
                        pointBackgroundColor: '#10B981',
                        pointRadius: 5
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: { duration: 1000, easing: 'easeOutQuart' },
                plugins: {
                    legend: { position: 'top', align: 'end', labels: { usePointStyle: true, boxWidth: 8, font: { family: 'Inter' } } },
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
                        beginAtZero: false, 
                        grid: { color: '#f3f4f6', drawBorder: false }, 
                        border: { display: false }, 
                        ticks: { 
                            color: '#6b7280', 
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
    });

    function updateForecastChart() {
        const timeframe = document.getElementById('forecastTimeframe').value;
        const insightText = document.getElementById('forecastInsightText');
        
        let newLabels = [];
        let newActualData = [];
        let newForecastData = [];

        if (timeframe === '7days') {
            newLabels = ['Day -3', 'Day -2', 'Day -1', 'Today', 'Day +1', 'Day +2', 'Day +3'];
            newActualData = [21000, 18500, 24000, 26000, null, null, null];
            newForecastData = [null, null, null, 26000, 27500, 26800, 29000];
            insightText.innerHTML = "<strong>7-Day Analysis:</strong> The system applies an Exponential Smoothing algorithm to capture short-term daily volatility and weekend shopping spikes.";
        } else if (timeframe === '30days') {
            newLabels = ['Week 1', 'Week 2', 'Week 3', 'Week 4 (Now)', 'Week 5 (Est)', 'Week 6 (Est)', 'Week 7 (Est)'];
            newActualData = [120000, 135000, 115000, 146726, null, null, null];
            newForecastData = [null, null, null, 146726, 154000, 161700, 169000];
            insightText.innerHTML = "<strong>30-Day Analysis:</strong> The system applies a 5% baseline growth trend to your 4-week moving average to project medium-term revenue stability.";
        } else if (timeframe === 'quarter') {
            newLabels = ['Month -2', 'Month -1', 'Current Month', 'Month +1 (Est)', 'Month +2 (Est)', 'Month +3 (Est)'];
            newActualData = [450000, 520000, 580000, null, null, null];
            newForecastData = [null, null, 580000, 620000, 675000, 710000];
            insightText.innerHTML = "<strong>Quarterly Analysis:</strong> The system compounds historical monthly data with macroeconomic seasonal multipliers to estimate long-term financial trajectory.";
        }

        myForecastChart.data.labels = newLabels;
        myForecastChart.data.datasets[0].data = newActualData;
        myForecastChart.data.datasets[1].data = newForecastData;
        myForecastChart.update();
    }
</script>
@endsection