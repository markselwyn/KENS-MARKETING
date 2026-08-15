@extends('layouts.app')

@section('title', 'Inventory Module - Ken\'s Marketing')
@section('header_title', 'Inventory Management')

@section('content')

<style>
    .locked-table-scroll::-webkit-scrollbar { width: 6px; height: 6px; }
    .locked-table-scroll::-webkit-scrollbar-track { background: transparent; }
    .locked-table-scroll::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
    .locked-table-scroll::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
</style>

<div class="space-y-6 print:hidden">

    <!-- ========================================== -->
    <!-- MINIMALIST TOAST NOTIFICATIONS             -->
    <!-- ========================================== -->
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

    @if($errors->any())
        <div id="toast-error" class="fixed top-24 right-6 z-50 flex items-center w-full max-w-sm p-4 bg-white border border-gray-100 rounded-2xl shadow-xl animate-fade-in" role="alert">
            <div class="inline-flex items-center justify-center shrink-0 w-8 h-8 text-red-600 bg-red-50 rounded-lg">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
            <div class="ml-3 text-sm font-medium text-gray-700 pr-4">{{ $errors->first() ?: 'Update Failed! Please check your inputs.' }}</div>
            <button type="button" onclick="closeToast('toast-error')" class="ml-auto -mx-1.5 -my-1.5 bg-white text-gray-400 hover:text-gray-900 rounded-lg p-1.5 hover:bg-gray-50 inline-flex items-center justify-center h-8 w-8 transition-colors">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    @endif
    <!-- ========================================== -->

    <div class="flex justify-between items-end animate-fade-in mb-2">
        <div>
            <p class="text-gray-500 text-sm">Monitor stock levels and system restock recommendations.</p>
        </div>
        <div class="flex gap-3">
            <button onclick="openImportModal()" class="px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 hover:shadow transition-all duration-200 flex items-center gap-2">
                <i class="fa-solid fa-file-excel text-green-600"></i> Import Excel
            </button>
            
            <button onclick="openAddProductModal()" class="px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 hover:shadow transition-all duration-200 flex items-center gap-2">
                <i class="fa-solid fa-plus"></i> Add New Product
            </button>
            
            <button onclick="window.print()" class="px-4 py-2 bg-navy-900 text-white rounded-lg text-sm font-medium hover:bg-navy-700 hover:shadow-lg transition-all duration-200 flex items-center gap-2">
                <i class="fa-solid fa-print"></i> Print Stock Report
            </button>
        </div>
    </div>

    <!-- 4 KPI CARDS (Strictly Global Database Stats) -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 animate-fade-in">
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:-translate-y-1 hover:shadow-lg transition-all duration-300">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-3">
                    <div class="bg-blue-50 p-2 rounded-lg text-blue-600"><i class="fa-solid fa-boxes-stacked"></i></div>
                    <h3 class="font-medium text-gray-500 text-sm">Total SKUs</h3>
                </div>
            </div>
            <h2 class="text-3xl font-bold text-gray-800 tracking-tight">{{ \App\Models\Product::count() }}</h2>
            <p class="text-xs text-gray-400 mt-2">Active database records</p>
        </div>

        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:-translate-y-1 hover:shadow-lg transition-all duration-300 overflow-hidden">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-3">
                    <div class="bg-green-50 p-2 rounded-lg text-green-600"><i class="fa-solid fa-peso-sign"></i></div>
                    <h3 class="font-medium text-gray-500 text-sm">Total Asset Value</h3>
                </div>
            </div>

            @php
                $totalAssetValue = \App\Models\Product::sum(DB::raw('unit_price * in_stock'));
                $formattedAssetValue = '₱' . number_format($totalAssetValue, 2);
                $length = strlen($formattedAssetValue);
                
                $textSize = 'text-3xl';
                if ($length > 18) {
                    $textSize = 'text-lg';
                } elseif ($length > 14) {
                    $textSize = 'text-xl';
                } elseif ($length > 11) {
                    $textSize = 'text-2xl';
                }
            @endphp

            <h2 class="{{ $textSize }} font-bold text-gray-800 tracking-tight truncate" title="{{ $formattedAssetValue }}">
                {{ $formattedAssetValue }}
            </h2>
            <p class="text-xs text-gray-400 mt-2">Current holding value</p>
        </div>

        <!-- CLICKABLE LIMITED STOCK CARD -->
        <div onclick="openDssModal('limitedStockModal')" class="bg-orange-50 rounded-2xl p-6 shadow-sm border border-orange-100 hover:-translate-y-1 hover:shadow-lg transition-all duration-300 cursor-pointer border-l-4 border-l-orange-500">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-3">
                    <div class="bg-orange-100 p-2 rounded-lg text-orange-600"><i class="fa-solid fa-triangle-exclamation"></i></div>
                    <h3 class="font-medium text-orange-800 text-sm">Limited Stock</h3>
                </div>
                <span class="text-xs font-semibold text-orange-700 underline">View list <i class="fa-solid fa-arrow-up-right-from-square ml-0.5"></i></span>
            </div>
            <h2 class="text-3xl font-bold text-orange-900 tracking-tight">{{ \App\Models\Product::where('in_stock', '>', 0)->whereColumn('in_stock', '<=', 'reorder_point')->count() }}</h2>
            <p class="text-xs text-orange-700 mt-2 font-medium">Approaching reorder point</p>
        </div>

        <!-- CLICKABLE OUT OF STOCK CARD -->
        <div onclick="openDssModal('outOfStockModal')" class="bg-red-50 rounded-2xl p-6 shadow-sm border border-red-100 hover:-translate-y-1 hover:shadow-lg transition-all duration-300 cursor-pointer border-l-4 border-l-red-500">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-3">
                    <div class="bg-red-100 p-2 rounded-lg text-red-600"><i class="fa-solid fa-circle-xmark"></i></div>
                    <h3 class="font-medium text-red-800 text-sm">Out of Stock</h3>
                </div>
                <span class="text-xs font-semibold text-red-700 underline">View list <i class="fa-solid fa-arrow-up-right-from-square ml-0.5"></i></span>
            </div>
            <h2 class="text-3xl font-bold text-red-900 tracking-tight">{{ \App\Models\Product::where('in_stock', '<=', 0)->count() }}</h2>
            <p class="text-xs text-red-700 mt-2 font-medium">Action required immediately</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 animate-fade-in delay-100">
        
        <div class="bg-gradient-to-br from-navy-900 to-navy-700 rounded-2xl p-6 shadow-sm border border-navy-700 text-white lg:col-span-2 hover:shadow-lg transition-shadow duration-300">
            <div class="flex items-center gap-2 mb-6">
                <i class="fa-solid fa-robot text-yellow-400"></i>
                <h2 class="text-lg font-semibold">Sales Pattern & Action Center</h2>
            </div>
            
            <div class="space-y-4">
                <div class="bg-white/10 rounded-xl p-4 border border-white/20 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="flex gap-4 items-center">
                        <div class="w-10 h-10 rounded bg-red-400/20 text-red-400 flex items-center justify-center text-lg shrink-0">
                            <i class="fa-solid fa-couch"></i>
                        </div>
                        <div>
                            @php
                                $criticalItem = \App\Models\Product::where('in_stock', '<=', 0)->first();
                            @endphp
                            @if($criticalItem)
                                <h4 class="font-semibold text-sm">Action Required</h4>
                                <p class="text-xs text-gray-300 mt-0.5"><strong class="text-white">{{ $criticalItem->product_name }}</strong> is depleted. Please restock immediately.</p>
                            @else
                                <h4 class="font-semibold text-sm">Action Required</h4>
                                <p class="text-xs text-gray-300 mt-0.5">Please review restock alerts on the main dashboard for depleted items.</p>
                            @endif
                        </div>
                    </div>
                    @if($criticalItem)
                        <button onclick="openRestockModal({{ $criticalItem->id }}, '{{ addslashes($criticalItem->product_name) }}', {{ $criticalItem->in_stock }})" class="text-center text-xs bg-red-500 text-white font-semibold px-4 py-2.5 rounded-lg hover:bg-red-600 transition-colors shadow-sm whitespace-nowrap shrink-0 border border-red-400">
                            <i class="fa-solid fa-plus mr-1"></i> Quick Restock
                        </button>
                    @else
                        <a href="/dashboard" class="text-center text-xs bg-white text-navy-900 font-semibold px-4 py-2.5 rounded-lg hover:bg-gray-100 transition-colors shadow-sm whitespace-nowrap shrink-0">
                            View Dashboard DSS
                        </a>
                    @endif
                </div>

                <div class="bg-white/10 rounded-xl p-4 border border-white/20 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="flex gap-4 items-center">
                        <div class="w-10 h-10 rounded bg-orange-400/20 text-orange-400 flex items-center justify-center text-lg shrink-0">
                            <i class="fa-solid fa-tags"></i>
                        </div>
                        <div>
                            <h4 class="font-semibold text-sm">{{ $stagnantTitle }}</h4>
                            <p class="text-xs text-gray-300 mt-0.5">{{ $stagnantMessage }}</p>
                        </div>
                    </div>
                    @if($stagnantValue > 0)
                        <button onclick="openDssModal('promoModal')" class="text-xs bg-white text-navy-900 font-semibold px-4 py-2.5 rounded-lg hover:bg-gray-100 transition-colors shadow-sm whitespace-nowrap shrink-0">
                            Suggest Markdown Promo
                        </button>
                    @endif
                </div>

            </div>
        </div>

        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex flex-col justify-between hover:shadow-lg transition-shadow duration-300">
            <h2 class="text-md font-semibold text-gray-800 mb-2">Overall Stock Availability</h2>
            <div class="relative flex-1 w-full min-h-[180px] flex items-center justify-center">
                <canvas id="stockHealthChart"></canvas>
                <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none mt-2">
                    @php
                        $totalCount = \App\Models\Product::count();
                        $availableCount = \App\Models\Product::whereColumn('in_stock', '>', 'reorder_point')->count();
                        $percentage = $totalCount > 0 ? round(($availableCount / $totalCount) * 100) : 0;
                    @endphp
                    <span class="text-2xl font-bold text-gray-800">
                        {{ $percentage }}%
                    </span>
                    <span class="text-xs text-gray-400 font-medium">Available</span>
                </div>
            </div>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- MAIN PRODUCT LIST                          -->
    <!-- ========================================== -->
    <div id="inventory-table-container" class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden animate-fade-in delay-200" style="overflow-anchor: none;">
        <div class="p-6 border-b border-gray-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
            
            <!-- NEW: Dynamic Records Counter for Table Filter -->
            <h2 class="text-lg font-semibold text-gray-800 flex items-center gap-3">
                Product Masterlist
                <span class="text-[11px] font-bold text-navy-600 bg-blue-50 border border-blue-100 px-2 py-1 rounded-md uppercase tracking-wider shadow-sm">
                    {{ method_exists($products, 'total') ? $products->total() : $products->count() }} Records Found
                </span>
            </h2>
            
            <form action="{{ route('inventory.index') }}" method="GET" class="flex flex-col sm:flex-row gap-3 w-full md:w-auto" id="filterForm" onsubmit="applyAjaxFilter(event)">
                
                <select name="status" id="statusFilter" onchange="applyAjaxFilter()" class="px-3 py-2 bg-gray-50 border border-gray-200 text-gray-600 text-sm rounded-lg focus:outline-none focus:ring-2 focus:ring-navy-700 focus:bg-white transition-all cursor-pointer">
                    <option value="all" {{ request('status') == 'all' || !request('status') ? 'selected' : '' }}>All Status</option>
                    <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>Available</option>
                    <option value="limited_stock" {{ request('status') == 'limited_stock' ? 'selected' : '' }}>Limited Stock</option>
                    <option value="out_of_stock" {{ request('status') == 'out_of_stock' ? 'selected' : '' }}>Out of Stock</option>
                </select>

                <select name="category" id="categoryFilter" onchange="applyAjaxFilter()" class="px-3 py-2 bg-gray-50 border border-gray-200 text-gray-600 text-sm rounded-lg focus:outline-none focus:ring-2 focus:ring-navy-700 focus:bg-white transition-all cursor-pointer">
                    <option value="all" {{ request('category') == 'all' || !request('category') ? 'selected' : '' }}>All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                    @endforeach
                </select>
                
                <div class="relative flex">
                    <i class="fa-solid fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 text-xs"></i>
                    <input type="text" name="search" id="inventorySearchInput" value="{{ request('search') }}" onkeyup="debounceAjaxFilter()" placeholder="Search SKU or Name..." class="pl-8 pr-4 py-2 bg-gray-50 border border-gray-200 text-gray-600 text-sm rounded-l-lg focus:outline-none focus:ring-2 focus:ring-navy-700 focus:bg-white w-full sm:w-64 transition-all" autocomplete="off">
                    <button type="submit" class="bg-navy-900 text-white px-3 py-2 rounded-r-lg text-sm font-medium hover:bg-navy-700 transition-colors">Search</button>
                </div>

                @if(request('search') || (request('category') && request('category') != 'all') || (request('status') && request('status') != 'all'))
                    <a href="{{ route('inventory.index') }}" class="clear-filter-btn flex items-center text-sm text-gray-500 hover:text-red-500 transition-colors ml-2">
                        <i class="fa-solid fa-xmark mr-1"></i> Clear
                    </a>
                @endif
            </form>

        </div>
        
        <div class="overflow-x-auto overflow-y-auto locked-table-scroll" style="height: 750px;">
            <table class="w-full text-sm text-left text-gray-500 relative">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 sticky top-0 z-10 shadow-sm border-b border-gray-200">
                    <tr>
                        <th scope="col" class="px-6 py-4 font-semibold">SKU</th>
                        <th scope="col" class="px-6 py-4 font-semibold">Product Name</th>
                        <th scope="col" class="px-6 py-4 font-semibold">Category</th>
                        <th scope="col" class="px-6 py-4 font-semibold">Unit Price</th>
                        <th scope="col" class="px-6 py-4 font-semibold text-center">In Stock</th>
                        <th scope="col" class="px-6 py-4 font-semibold text-center">Status</th>
                        <th scope="col" class="px-6 py-4 font-semibold text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($products as $product)
                    <tr class="bg-white hover:bg-gray-50 transition-colors duration-200">
                        <td class="px-6 py-4 font-medium text-navy-700">{{ $product->sku }}</td>
                        <td class="px-6 py-4 text-gray-900 font-medium">{{ $product->product_name }}</td>
                        <td class="px-6 py-4 text-xs">{{ $product->category }}</td>
                        <td class="px-6 py-4">₱{{ number_format($product->unit_price, 2) }}</td>
                        
                        <td class="px-6 py-4 text-center font-bold 
                            @if($product->in_stock <= 0) text-red-600
                            @elseif($product->in_stock <= $product->reorder_point) text-orange-500
                            @else text-gray-800
                            @endif">
                            {{ $product->in_stock }}
                        </td>
                        
                        <td class="px-6 py-4 text-center">
                            @if($product->in_stock <= 0)
                                <span class="bg-red-100 text-red-700 px-2.5 py-1 rounded-full text-xs font-medium border border-red-200">Out of Stock</span>
                            @elseif($product->in_stock <= $product->reorder_point)
                                <span class="bg-orange-100 text-orange-700 px-2.5 py-1 rounded-full text-xs font-medium border border-orange-200">Limited Stock</span>
                            @else
                                <span class="bg-green-100 text-green-700 px-2.5 py-1 rounded-full text-xs font-medium border border-green-200">Available</span>
                            @endif
                        </td>
                        
                        <td class="px-6 py-4 text-right">
                            <div class="flex justify-end gap-2">
                                <button onclick="openRestockModal({{ $product->id }}, '{{ addslashes($product->product_name) }}', {{ $product->in_stock }})" class="px-3 py-1.5 bg-green-600 text-white text-xs font-medium rounded hover:bg-green-700 transition-colors shadow-sm flex items-center gap-1">
                                    <i class="fa-solid fa-plus"></i> Restock
                                </button>
                                <button onclick="openEditProductModal(this, {{ $product->id }}, {{ $product->reorder_point }})" class="px-3 py-1.5 bg-navy-700 text-white text-xs font-medium rounded hover:bg-navy-900 transition-colors shadow-sm">Edit</button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                            @if(request('search') || (request('category') && request('category') != 'all') || (request('status') && request('status') != 'all'))
                                No products found matching your search criteria. <a href="{{ route('inventory.index') }}" class="text-navy-700 underline">Clear filter</a>
                            @else
                                No products found in the database. Add one to get started!
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="border-t border-gray-100 bg-gray-50 rounded-b-2xl">
            @if(method_exists($products, 'links') && $products->hasPages())
                <div class="p-4 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div class="text-sm text-gray-500 font-medium">
                        Page <span class="text-navy-900 font-bold" id="currentPageDisplay">{{ $products->currentPage() }}</span> of <span class="text-navy-900 font-bold" id="lastPageDisplay">{{ $products->lastPage() }}</span>
                    </div>
                    <div class="w-full sm:w-auto overflow-x-auto">
                        {{ $products->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- ALL MODALS REMAIN THE SAME BELOW -->

<div id="addProductModal" class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm hidden z-50 flex items-center justify-center transition-opacity opacity-0 no-print">
    <div class="bg-white rounded-2xl w-full max-w-lg shadow-2xl transform scale-95 transition-transform duration-300" id="modalContent">
        <div class="flex items-center justify-between p-5 border-b border-gray-100 bg-navy-900 text-white rounded-t-2xl">
            <h3 class="text-lg font-semibold flex items-center gap-2">
                <i class="fa-solid fa-box-open"></i> Add New Product
            </h3>
            <button onclick="closeAddProductModal()" class="text-gray-300 hover:text-white transition-colors focus:outline-none">
                <i class="fa-solid fa-xmark text-xl"></i>
            </button>
        </div>
        <form action="{{ route('inventory.store') }}" method="POST" class="p-6 space-y-5">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">SKU</label>
                    <input type="text" name="sku" placeholder="e.g. FUR-1005" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-navy-700 bg-gray-50 focus:bg-white transition-colors" required>
                </div>
                <div>
                    <div class="flex justify-between items-center mb-1">
                        <label class="block text-sm font-medium text-gray-700">Category</label>
                        <button type="button" onclick="addNewCategory('categorySelect')" class="text-xs font-semibold text-navy-700 hover:text-navy-900 transition-colors focus:outline-none">
                            <i class="fa-solid fa-plus mr-1"></i>Add New
                        </button>
                    </div>
                    <select id="categorySelect" name="category" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-navy-700 bg-gray-50 focus:bg-white cursor-pointer transition-colors" required>
                        <option value="" disabled selected>Select...</option>
                        <option value="Furniture">Furniture</option>
                        <option value="Appliances">Appliances</option>
                        <option value="Foams">Foams</option>
                        <option value="Speakers">Speakers</option>
                        <option value="TV">TV</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Product Name</label>
                <input type="text" name="product_name" placeholder="e.g. 3-Door Wooden Wardrobe Cabinet" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-navy-700 bg-gray-50 focus:bg-white transition-colors" required>
            </div>
            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Unit Price (₱)</label>
                    <input type="number" name="unit_price" step="0.01" placeholder="0.00" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-navy-700 bg-gray-50 focus:bg-white transition-colors" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Initial Stock</label>
                    <input type="number" name="in_stock" min="0" value="0" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-navy-700 bg-gray-50 focus:bg-white transition-colors" required>
                </div>
                <div>
                    <label class="block text-sm font-bold text-navy-700 mb-1">Reorder Point</label>
                    <input type="number" name="reorder_point" min="1" value="5" class="w-full px-3 py-2 border border-navy-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-navy-700 bg-blue-50 focus:bg-white transition-colors" required>
                </div>
            </div>
            <div class="pt-4 mt-2 flex justify-end gap-3 border-t border-gray-100">
                <button type="button" onclick="closeAddProductModal()" class="px-5 py-2.5 text-sm font-medium text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors focus:outline-none">Cancel</button>
                <button type="submit" class="px-5 py-2.5 text-sm font-medium text-white bg-navy-900 rounded-lg hover:bg-navy-700 hover:shadow-lg transition-all flex items-center gap-2 focus:outline-none">
                    <i class="fa-solid fa-floppy-disk"></i> Save Product
                </button>
            </div>
        </form>
    </div>
</div>

<form id="deleteProductForm" action="{{ route('inventory.delete') }}" method="POST" class="hidden">
    @csrf
    <input type="hidden" name="id" id="deleteProductId">
</form>

<div id="editProductModal" class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm hidden z-50 flex items-center justify-center transition-opacity opacity-0 no-print">
    <div class="bg-white rounded-2xl w-full max-w-lg shadow-2xl transform scale-95 transition-transform duration-300" id="editModalContent">
        <div class="flex items-center justify-between p-5 border-b border-gray-100 bg-navy-700 text-white rounded-t-2xl">
            <h3 class="text-lg font-semibold flex items-center gap-2">
                <i class="fa-solid fa-pen-to-square"></i> Edit Product
            </h3>
            <button onclick="closeEditProductModal()" class="text-gray-300 hover:text-white transition-colors focus:outline-none">
                <i class="fa-solid fa-xmark text-xl"></i>
            </button>
        </div>
        <form action="{{ route('inventory.update') }}" method="POST" class="p-6 space-y-5">
            @csrf
            <input type="hidden" name="id" id="editProductId">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">SKU</label>
                    <input type="text" name="sku" id="editSku" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-navy-700 bg-gray-50 focus:bg-white transition-colors" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                    <select name="category" id="editCategorySelect" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-navy-700 bg-gray-50 focus:bg-white cursor-pointer transition-colors" required>
                        <option value="" disabled>Select...</option>
                        <option value="Furniture">Furniture</option>
                        <option value="Appliances">Appliances</option>
                        <option value="Foams">Foams</option>
                        <option value="Speakers">Speakers</option>
                        <option value="TV">TV</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Product Name</label>
                <input type="text" name="product_name" id="editName" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-navy-700 bg-gray-50 focus:bg-white transition-colors" required>
            </div>

            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Unit Price (₱)</label>
                    <input type="number" name="unit_price" id="editPrice" step="0.01" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-navy-700 bg-gray-50 focus:bg-white transition-colors" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Current Stock</label>
                    <input type="number" name="in_stock" id="editStock" min="0" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-navy-700 bg-gray-50 focus:bg-white transition-colors" required>
                </div>
                <div>
                    <label class="block text-sm font-bold text-navy-700 mb-1">Reorder Point</label>
                    <input type="number" name="reorder_point" id="editRop" min="1" class="w-full px-3 py-2 border border-navy-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-navy-700 bg-blue-50 focus:bg-white transition-colors" required>
                </div>
            </div>

            <div class="pt-4 mt-2 flex justify-between items-center border-t border-gray-100">
                <button type="button" onclick="if(confirm('Are you sure you want to completely delete this product?')) { document.getElementById('deleteProductForm').submit(); }" class="text-sm font-medium text-red-500 hover:text-red-700 transition-colors focus:outline-none">
                    <i class="fa-solid fa-trash mr-1"></i> Delete
                </button>
                <div class="flex gap-3">
                    <button type="button" onclick="closeEditProductModal()" class="px-5 py-2.5 text-sm font-medium text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors focus:outline-none">Cancel</button>
                    <button type="submit" class="px-5 py-2.5 text-sm font-medium text-white bg-navy-700 rounded-lg hover:bg-navy-900 hover:shadow-lg transition-all flex items-center gap-2 focus:outline-none">
                        <i class="fa-solid fa-check"></i> Update Product
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<div id="restockShipmentModal" class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm hidden z-50 flex items-center justify-center transition-opacity opacity-0 no-print">
    <div class="bg-white rounded-2xl w-full max-w-md shadow-2xl transform scale-95 transition-transform duration-300" id="restockModalBoxContent">
        <div class="flex items-center justify-between p-5 border-b border-gray-100 bg-green-600 text-white rounded-t-2xl">
            <h3 class="text-lg font-semibold flex items-center gap-2">
                <i class="fa-solid fa-truck-ramp-box"></i> Receive Shipment
            </h3>
            <button onclick="closeRestockModal()" class="text-white/80 hover:text-white transition-colors focus:outline-none">
                <i class="fa-solid fa-xmark text-xl"></i>
            </button>
        </div>
        <form action="{{ route('inventory.restock') }}" method="POST" class="p-6 space-y-4">
            @csrf
            <input type="hidden" name="product_id" id="restockProductId">
            
            <div class="bg-gray-50 p-3 rounded-xl border border-gray-200">
                <span class="text-xs text-gray-400 uppercase tracking-wider font-bold block">Target Item</span>
                <span class="font-bold text-navy-900 text-base" id="restockProductName">Product Name</span>
                <div class="text-xs text-gray-500 mt-0.5">Current Inventory: <span class="font-bold text-gray-700" id="restockCurrentStock">0</span> units</div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Quantity Delivered</label>
                <input type="number" name="quantity_added" min="1" placeholder="e.g. 15" class="w-full px-3 py-2 border border-green-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-600 bg-green-50/30 font-bold text-gray-800" required>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Supplier Name</label>
                    <input type="text" name="supplier" placeholder="e.g. Uratex Direct" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-navy-700 bg-gray-50">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">DR / Ref Number</label>
                    <input type="text" name="reference_no" placeholder="e.g. DR-88392" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-xs focus:outline-none focus:ring-2 focus:ring-navy-700 bg-gray-50">
                </div>
            </div>

            <div class="pt-3 flex justify-end gap-3 border-t border-gray-100 mt-2">
                <button type="button" onclick="closeRestockModal()" class="px-4 py-2 text-sm font-medium text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200">Cancel</button>
                <button type="submit" class="px-5 py-2 text-sm font-bold text-white bg-green-600 rounded-lg hover:bg-green-700 shadow-md transition-all flex items-center gap-2">
                    <i class="fa-solid fa-check"></i> Add to Inventory
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================== -->
<!-- UPGRADED STAGNANT CAPITAL MODAL            -->
<!-- ========================================== -->
<div id="promoModal" class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm hidden z-50 flex items-center justify-center transition-opacity opacity-0 no-print">
    <div class="bg-white rounded-2xl w-full max-w-lg shadow-2xl transform scale-95 transition-transform duration-300" id="promoModalContent">
        <div class="flex items-center justify-between p-5 border-b border-gray-100 bg-white rounded-t-2xl">
            <h3 class="text-lg font-bold text-navy-900 flex items-center gap-2">
                <i class="fa-solid fa-tags text-orange-500"></i> DSS Capital Recovery Strategy
            </h3>
            <button onclick="closeDssModal('promoModal')" class="text-gray-400 hover:text-gray-600 focus:outline-none">
                <i class="fa-solid fa-xmark text-xl"></i>
            </button>
        </div>
        
        <div class="p-6">
            <div class="mb-5">
                <p class="text-sm text-gray-500 font-semibold mb-1 uppercase tracking-wider">Target Item</p>
                <p class="text-lg font-bold text-gray-900 uppercase">{{ $stagnantTitle }}</p>
            </div>
            
            <!-- Product Summary Card -->
            <div class="grid grid-cols-3 gap-3 bg-gray-50 p-4 rounded-xl border border-gray-200 mb-6 text-center">
                @php
                    $item = \App\Models\Product::find($stagnantId);
                    $unitPrice = $item ? $item->unit_price : 0;
                    $stock = $item ? $item->in_stock : 0;
                    $tiedUp = $unitPrice * $stock;
                    
                    $promoDiscount = 0.10; 
                    $promoPrice = $unitPrice * (1 - $promoDiscount);
                    $projectedRecovery = $promoPrice * $stock;
                @endphp
                
                <div>
                    <span class="text-xs text-gray-500 block mb-1">Unsold Stock</span>
                    <span class="font-bold text-gray-800 text-base">{{ $stock }} Units</span>
                </div>
                <div>
                    <span class="text-xs text-gray-500 block mb-1">Current Price</span>
                    <span class="font-bold text-gray-800 text-base">₱{{ number_format($unitPrice, 2) }}</span>
                </div>
                <div>
                    <span class="text-xs text-gray-500 block mb-1">Capital Tied Up</span>
                    <span class="font-bold text-red-600 text-base">₱{{ number_format($tiedUp, 2) }}</span>
                </div>
            </div>

            <!-- Strategy Recommendation Box -->
            <div class="bg-orange-50 border border-orange-200 rounded-xl p-5 mb-4">
                <div class="flex justify-between items-center mb-3">
                    <span class="text-xs font-bold uppercase tracking-wider text-orange-800">System Recommendation</span>
                    <span class="bg-orange-200 text-orange-900 text-[10px] font-bold px-2.5 py-1 rounded shadow-sm">10% Markdown</span>
                </div>
                
                <div class="flex items-baseline justify-between mb-3 pb-3 border-b border-orange-200/60">
                    <span class="text-sm font-medium text-gray-700">Suggested Promo Price:</span>
                    <span class="text-2xl font-bold text-green-700">₱{{ number_format($promoPrice, 2) }} <span class="text-sm line-through text-gray-400 font-normal ml-1">₱{{ number_format($unitPrice, 2) }}</span></span>
                </div>
                
                <p class="text-[11px] text-orange-800 leading-relaxed flex items-start gap-2">
                    <i class="fa-solid fa-chart-line mt-0.5 text-orange-500 shrink-0"></i>
                    <span>This price reduction is mathematically projected to stimulate movement and recover up to <strong>₱{{ number_format($projectedRecovery, 2) }}</strong> in liquidity within 14 days without dropping below standard supplier margins.</span>
                </p>
            </div>
        </div>
        
        <div class="p-4 border-t border-gray-100 bg-gray-50 rounded-b-2xl flex justify-center gap-3">
            <button onclick="closeDssModal('promoModal')" class="px-8 py-2.5 text-sm font-medium text-gray-600 bg-white border border-gray-200 rounded-lg hover:bg-gray-100 transition-colors shadow-sm focus:outline-none">Close</button>
            <a href="/dss-insights?simulate_id={{ $stagnantId }}" class="px-8 py-2.5 text-sm font-bold text-white bg-navy-900 rounded-lg hover:bg-navy-800 transition-all shadow-md flex items-center gap-2 focus:outline-none group">
                <i class="fa-solid fa-layer-group text-yellow-400"></i> Simulate in DSS Insights
            </a>
        </div>
    </div>
</div>

<div id="importExcelModal" class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm hidden z-50 flex items-center justify-center transition-opacity opacity-0 no-print p-4">
    <div class="bg-white rounded-2xl w-full max-w-md shadow-2xl transform scale-95 transition-transform duration-300" id="importModalContent">
        <div class="flex items-center justify-between p-5 border-b border-gray-100 bg-gray-50 text-gray-700 rounded-t-2xl">
            <h3 class="text-lg font-semibold flex items-center gap-2">
                <i class="fa-solid fa-file-excel text-green-600"></i>  Import Inventory
            </h3>
            <button onclick="closeImportModal()" class="text-gray-400 hover:text-gray-700 transition-colors focus:outline-none">
                <i class="fa-solid fa-xmark text-xl"></i>
            </button>
        </div>
        <form action="{{ route('inventory.import') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
            @csrf
            <div class="bg-blue-50 border border-blue-200 p-3 rounded-lg text-xs text-blue-800 mb-4">
                <strong>Smart Importer Active:</strong> The system is configured to automatically read the official <strong>Ken's Marketing</strong> inventory format. It will skip header titles and extract data starting from Row 4.
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Upload Excel File (.xlsx, .csv)</label>
                <input type="file" name="excel_file" accept=".xlsx, .xls, .csv" class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-navy-700 bg-gray-50 cursor-pointer hover:bg-white transition-colors" required>
            </div>
            <div class="pt-3 flex justify-end gap-3 border-t border-gray-100 mt-2">
                <button type="button" onclick="closeImportModal()" class="px-4 py-2 text-sm font-medium text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200">Cancel</button>
                <button type="submit" class="px-5 py-2 text-sm font-bold text-white bg-green-600 rounded-lg hover:bg-green-700 shadow-md transition-all flex items-center gap-2">
                    <i class="fa-solid fa-upload"></i> Upload & Process
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================== -->
<!-- NEW KPI DRILL-DOWN MODALS                  -->
<!-- ========================================== -->

<!-- LIMITED STOCK MODAL -->
<div id="limitedStockModal" class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm hidden z-50 flex items-center justify-center transition-opacity opacity-0 no-print p-4">
    <div class="bg-white rounded-2xl w-full max-w-2xl shadow-2xl flex flex-col max-h-[85vh] transform scale-95 transition-transform duration-300 overflow-hidden" id="limitedStockModalContent">
        <div class="flex items-center justify-between p-5 border-b border-gray-100 bg-orange-500 text-white rounded-t-2xl shrink-0">
            <h3 class="text-lg font-semibold flex items-center gap-2">
                <i class="fa-solid fa-triangle-exclamation"></i> Limited Stock Items (Approaching Reorder Point)
            </h3>
            <button onclick="closeDssModal('limitedStockModal')" class="text-white/80 hover:text-white transition-colors focus:outline-none">
                <i class="fa-solid fa-xmark text-xl"></i>
            </button>
        </div>
        <div class="overflow-y-auto p-0 flex-1 custom-scrollbar">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-[10px] text-gray-400 uppercase bg-white sticky top-0 border-b border-gray-100 shadow-sm z-10">
                    <tr>
                        <th class="px-6 py-4 font-semibold tracking-wider">SKU</th>
                        <th class="px-6 py-4 font-semibold tracking-wider">Product Name</th>
                        <th class="px-6 py-4 font-semibold text-center tracking-wider">In Stock</th>
                        <th class="px-6 py-4 font-semibold text-center tracking-wider">Reorder Point</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @php
                        $limitedItems = \App\Models\Product::where('in_stock', '>', 0)->whereColumn('in_stock', '<=', 'reorder_point')->get();
                    @endphp
                    @forelse($limitedItems as $item)
                        <tr class="bg-white hover:bg-orange-50 transition-colors duration-200">
                            <td class="px-6 py-4 font-mono text-navy-700 text-xs">{{ $item->sku }}</td>
                            <td class="px-6 py-4 font-medium text-gray-900">{{ $item->product_name }}</td>
                            <td class="px-6 py-4 text-center font-bold text-orange-600 text-lg">{{ $item->in_stock }}</td>
                            <td class="px-6 py-4 text-center text-gray-500">{{ $item->reorder_point }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-gray-500 italic">No limited stock items found. All levels are healthy!</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-gray-100 bg-gray-50 rounded-b-2xl text-right shrink-0">
            <button onclick="closeDssModal('limitedStockModal')" class="px-5 py-2.5 text-sm font-medium text-gray-600 bg-white border border-gray-200 rounded-lg hover:bg-gray-100 transition-colors shadow-sm focus:outline-none">Close Window</button>
        </div>
    </div>
</div>

<!-- OUT OF STOCK MODAL -->
<div id="outOfStockModal" class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm hidden z-50 flex items-center justify-center transition-opacity opacity-0 no-print p-4">
    <div class="bg-white rounded-2xl w-full max-w-2xl shadow-2xl flex flex-col max-h-[85vh] transform scale-95 transition-transform duration-300 overflow-hidden" id="outOfStockModalContent">
        <div class="flex items-center justify-between p-5 border-b border-gray-100 bg-red-600 text-white rounded-t-2xl shrink-0">
            <h3 class="text-lg font-semibold flex items-center gap-2">
                <i class="fa-solid fa-circle-xmark"></i> Out of Stock Items (Immediate Action Required)
            </h3>
            <button onclick="closeDssModal('outOfStockModal')" class="text-white/80 hover:text-white transition-colors focus:outline-none">
                <i class="fa-solid fa-xmark text-xl"></i>
            </button>
        </div>
        <div class="overflow-y-auto p-0 flex-1 custom-scrollbar">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-[10px] text-gray-400 uppercase bg-white sticky top-0 border-b border-gray-100 shadow-sm z-10">
                    <tr>
                        <th class="px-6 py-4 font-semibold tracking-wider">SKU</th>
                        <th class="px-6 py-4 font-semibold tracking-wider">Product Name</th>
                        <th class="px-6 py-4 font-semibold text-center tracking-wider">Category</th>
                        <th class="px-6 py-4 font-semibold text-right tracking-wider">Unit Price</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @php
                        $outItems = \App\Models\Product::where('in_stock', '<=', 0)->get();
                    @endphp
                    @forelse($outItems as $item)
                        <tr class="bg-white hover:bg-red-50 transition-colors duration-200">
                            <td class="px-6 py-4 font-mono text-navy-700 text-xs">{{ $item->sku }}</td>
                            <td class="px-6 py-4 font-medium text-gray-900">{{ $item->product_name }}</td>
                            <td class="px-6 py-4 text-center">{{ $item->category }}</td>
                            <td class="px-6 py-4 text-right font-bold text-gray-800">₱{{ number_format($item->unit_price, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-green-600 font-medium italic"><i class="fa-solid fa-circle-check mr-1"></i> Amazing! There are 0 items out of stock.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-gray-100 bg-gray-50 rounded-b-2xl text-right shrink-0">
            <button onclick="closeDssModal('outOfStockModal')" class="px-5 py-2.5 text-sm font-medium text-gray-600 bg-white border border-gray-200 rounded-lg hover:bg-gray-100 transition-colors shadow-sm focus:outline-none">Close Window</button>
        </div>
    </div>
</div>

<div class="hidden print:block text-black bg-white w-full">
    <!-- ... (Print view remains exactly the same) ... -->
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

    // --- BULLETPROOF AJAX ENGINE (UPGRADED) ---
    let currentFetchId = 0; 
    let currentAbortController = null;

    function performAjaxFetch(url) {
        const tableContainer = document.getElementById('inventory-table-container');
        if(!tableContainer) return;
        
        const fetchId = ++currentFetchId;
        
        if (currentAbortController) {
            currentAbortController.abort();
        }
        currentAbortController = new AbortController();
        
        tableContainer.style.pointerEvents = 'none';
        tableContainer.style.opacity = '0.5';
        tableContainer.style.transition = 'opacity 0.2s ease-in-out';
        
        fetch(url, { signal: currentAbortController.signal })
            .then(response => response.text())
            .then(html => {
                if (fetchId !== currentFetchId) return;

                // STRICT DOM CAPTURE: Save exactly what the user is typing 
                // the millisecond before we replace the HTML table!
                let currentSearchValue = null;
                const activeElementId = document.activeElement ? document.activeElement.id : null;
                
                if (activeElementId === 'inventorySearchInput') {
                    currentSearchValue = document.getElementById('inventorySearchInput').value;
                }

                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newContent = doc.getElementById('inventory-table-container').innerHTML;
                
                tableContainer.innerHTML = newContent;
                tableContainer.style.pointerEvents = 'auto';
                tableContainer.style.opacity = '1';
                
                window.history.pushState({}, '', url);

                const urlObj = new URL(url);
                const statusParam = urlObj.searchParams.get('status');
                const selectElement = document.getElementById('statusFilter');
                
                if (selectElement) {
                    if (statusParam) {
                        selectElement.value = statusParam;
                    } else {
                        selectElement.value = 'all';
                    }
                }

                // STRICT RESTORE: Put the user's typing right back where they left off!
                if (activeElementId) {
                    const elem = document.getElementById(activeElementId);
                    if (elem && elem.tagName === 'INPUT') {
                        if (activeElementId === 'inventorySearchInput' && currentSearchValue !== null) {
                            elem.value = currentSearchValue; // This line fixes the typing bug!
                        }
                        elem.focus();
                        const len = elem.value.length;
                        elem.setSelectionRange(len, len);
                    }
                }
            })
            .catch(error => {
                tableContainer.style.pointerEvents = 'auto';
                if (error.name !== 'AbortError') {
                    window.location.href = url;
                }
            });
    }

    let typingTimer;
    function debounceAjaxFilter() {
        clearTimeout(typingTimer);
        typingTimer = setTimeout(function() {
            applyAjaxFilter();
        }, 500); 
    }

    function applyAjaxFilter(event = null) {
        if (event) event.preventDefault();
        const form = document.getElementById('filterForm');
        const searchParams = new URLSearchParams(new FormData(form)).toString();
        performAjaxFetch(form.action + '?' + searchParams);
    }

    document.addEventListener("DOMContentLoaded", function() {
        const tableContainer = document.getElementById('inventory-table-container');

        if (tableContainer) {
            tableContainer.addEventListener('click', function(e) {
                const link = e.target.closest('a');
                
                if (link && link.href && (link.href.includes('page=') || link.classList.contains('clear-filter-btn'))) {
                    e.preventDefault(); 
                    if (document.activeElement) {
                        document.activeElement.blur();
                    }
                    performAjaxFetch(link.href);
                }
            });
        }
    });

    // --- MODAL CONTROLS ---
    function openAddProductModal() {
        const addModal = document.getElementById('addProductModal');
        const addModalContent = document.getElementById('modalContent');
        addModal.classList.remove('hidden');
        setTimeout(() => { addModal.classList.remove('opacity-0'); addModalContent.classList.remove('scale-95'); }, 10);
    }
    function closeAddProductModal() {
        const addModal = document.getElementById('addProductModal');
        const addModalContent = document.getElementById('modalContent');
        addModal.classList.add('opacity-0'); addModalContent.classList.add('scale-95');
        setTimeout(() => { addModal.classList.add('hidden'); }, 300);
    }

    function openImportModal() {
        const importModal = document.getElementById('importExcelModal');
        const importModalContent = document.getElementById('importModalContent');
        importModal.classList.remove('hidden');
        setTimeout(() => { importModal.classList.remove('opacity-0'); importModalContent.classList.remove('scale-95'); }, 10);
    }
    function closeImportModal() {
        const importModal = document.getElementById('importExcelModal');
        const importModalContent = document.getElementById('importModalContent');
        importModal.classList.add('opacity-0'); importModalContent.classList.add('scale-95');
        setTimeout(() => { importModal.classList.add('hidden'); }, 300);
    }

    function openEditProductModal(buttonElement, productId, reorderPoint) {
        const editModal = document.getElementById('editProductModal');
        const editModalContent = document.getElementById('editModalContent');
        const row = buttonElement.closest('tr');
        const sku = row.cells[0].innerText.trim();
        const name = row.cells[1].innerText.trim();
        const categoryText = row.cells[2].innerText.trim().toLowerCase();
        let price = row.cells[3].innerText.trim().replace('₱', '').replace(/,/g, '');
        const stock = row.cells[4].innerText.trim();

        document.getElementById('editProductId').value = productId;
        document.getElementById('deleteProductId').value = productId;
        document.getElementById('editSku').value = sku;
        document.getElementById('editName').value = name;
        document.getElementById('editPrice').value = price;
        document.getElementById('editStock').value = stock;
        document.getElementById('editRop').value = reorderPoint;

        const catSelect = document.getElementById('editCategorySelect');
        for (let i = 0; i < catSelect.options.length; i++) {
            if (catSelect.options[i].text.toLowerCase() === categoryText) { catSelect.selectedIndex = i; break; }
        }

        editModal.classList.remove('hidden');
        setTimeout(() => { editModal.classList.remove('opacity-0'); editModalContent.classList.remove('scale-95'); }, 10);
    }
    function closeEditProductModal() {
        const editModal = document.getElementById('editProductModal');
        const editModalContent = document.getElementById('editModalContent');
        editModal.classList.add('opacity-0'); editModalContent.classList.add('scale-95');
        setTimeout(() => { editModal.classList.add('hidden'); }, 300);
    }

    function addNewCategory(selectElementId) {
        const newCategory = prompt("Enter the name of the new category:");
        if (newCategory && newCategory.trim() !== "") {
            const select = document.getElementById(selectElementId);
            const cleanName = newCategory.trim();
            const valueName = cleanName.toLowerCase().replace(/[^a-z0-9]/g, '_');
            const option = document.createElement('option');
            option.value = valueName; option.text = cleanName;
            select.add(option); select.value = valueName;
            alert("Success! '" + cleanName + "' has been added to your categories.");
        }
    }

    function openRestockModal(id, name, currentStock) {
        const restockModal = document.getElementById('restockShipmentModal');
        const restockBox = document.getElementById('restockModalBoxContent');
        document.getElementById('restockProductId').value = id;
        document.getElementById('restockProductName').textContent = name;
        document.getElementById('restockCurrentStock').textContent = currentStock;

        restockModal.classList.remove('hidden');
        setTimeout(() => { 
            restockModal.classList.remove('opacity-0'); 
            restockBox.classList.remove('scale-95'); 
        }, 10);
    }
    function closeRestockModal() {
        const restockModal = document.getElementById('restockShipmentModal');
        const restockBox = document.getElementById('restockModalBoxContent');
        restockModal.classList.add('opacity-0'); 
        restockBox.classList.add('scale-95');
        setTimeout(() => { restockModal.classList.add('hidden'); }, 300);
    }

    // THE FUNCTION THAT CONTROLS ALL DSS MODALS (Including Promo)
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

    // --- FIXED CHART LOGIC ---
    document.addEventListener("DOMContentLoaded", function() {
        const ctx = document.getElementById('stockHealthChart').getContext('2d');
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Available', 'Limited Stock', 'Out of Stock'],
                datasets: [{
                    data: [
                        {{ \App\Models\Product::whereColumn('in_stock', '>', 'reorder_point')->count() }}, 
                        {{ \App\Models\Product::where('in_stock', '>', 0)->whereColumn('in_stock', '<=', 'reorder_point')->count() }}, 
                        {{ \App\Models\Product::where('in_stock', '<=', 0)->count() }}
                    ],
                    backgroundColor: ['#10B981', '#F59E0B', '#EF4444'],
                    borderWidth: 0, hoverOffset: 4
                }]
            },
            options: {
                responsive: true, maintainAspectRatio: false, cutout: '80%',
                animation: { duration: 1500, easing: 'easeOutBounce' },
                plugins: { legend: { display: false }, tooltip: { backgroundColor: '#012C55', padding: 10, cornerRadius: 8, callbacks: { label: function(context) { return ' ' + context.label + ': ' + context.raw + ' items'; } } } }
            }
        });
    });
</script>
@endsection