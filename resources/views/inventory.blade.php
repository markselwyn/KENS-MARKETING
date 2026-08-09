@extends('layouts.app')

@section('title', 'Inventory Module - Ken\'s Marketing')
@section('header_title', 'Inventory Management')

@section('content')

<div class="space-y-6 print:hidden">

    <!-- ========================================== -->
    <!-- NEW: SYSTEM ALERTS (SUCCESS & ERROR)       -->
    <!-- ========================================== -->
    @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-r-lg shadow-sm animate-fade-in mb-4">
            <div class="flex items-center">
                <i class="fa-solid fa-circle-check text-green-500 mr-3 text-lg"></i>
                <p class="text-green-800 font-medium text-sm">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    @if($errors->any())
        <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg shadow-sm animate-fade-in mb-4">
            <div class="flex items-center mb-2">
                <i class="fa-solid fa-triangle-exclamation text-red-500 mr-3 text-lg"></i>
                <p class="text-red-800 font-bold text-sm">Update Failed! Please check the following:</p>
            </div>
            <ul class="list-disc list-inside text-xs text-red-700 ml-7 font-medium">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <!-- ========================================== -->

    <div class="flex justify-between items-end animate-fade-in mb-2">
        <div>
            <p class="text-gray-500 text-sm">Monitor stock levels and system restock recommendations.</p>
        </div>
        <div class="flex gap-3">
            <button onclick="openAddProductModal()" class="px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 hover:shadow transition-all duration-200 flex items-center gap-2">
                <i class="fa-solid fa-plus"></i> Add New Product
            </button>
            <button onclick="window.print()" class="px-4 py-2 bg-navy-900 text-white rounded-lg text-sm font-medium hover:bg-navy-700 hover:shadow-lg transition-all duration-200 flex items-center gap-2">
                <i class="fa-solid fa-print"></i> Print Stock Report
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 animate-fade-in">
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:-translate-y-1 hover:shadow-lg transition-all duration-300">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-3">
                    <div class="bg-blue-50 p-2 rounded-lg text-blue-600"><i class="fa-solid fa-boxes-stacked"></i></div>
                    <h3 class="font-medium text-gray-500 text-sm">Total SKUs</h3>
                </div>
            </div>
            <h2 class="text-3xl font-bold text-gray-800 tracking-tight">{{ $products->count() }}</h2>
            <p class="text-xs text-gray-400 mt-2">Active database records</p>
        </div>

        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:-translate-y-1 hover:shadow-lg transition-all duration-300">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-3">
                    <div class="bg-green-50 p-2 rounded-lg text-green-600"><i class="fa-solid fa-peso-sign"></i></div>
                    <h3 class="font-medium text-gray-500 text-sm">Total Asset Value</h3>
                </div>
            </div>
            <h2 class="text-3xl font-bold text-gray-800 tracking-tight">₱{{ number_format($products->sum(function($product) { return $product->unit_price * $product->in_stock; }), 2) }}</h2>
            <p class="text-xs text-gray-400 mt-2">Current holding value</p>
        </div>

        <div class="bg-orange-50 rounded-2xl p-6 shadow-sm border border-orange-100 hover:-translate-y-1 hover:shadow-lg transition-all duration-300 cursor-pointer border-l-4 border-l-orange-500">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-3">
                    <div class="bg-orange-100 p-2 rounded-lg text-orange-600"><i class="fa-solid fa-triangle-exclamation"></i></div>
                    <h3 class="font-medium text-orange-800 text-sm">Low Stock Items</h3>
                </div>
            </div>
            <h2 class="text-3xl font-bold text-orange-900 tracking-tight">{{ $products->where('status', 'Low Stock')->count() }}</h2>
            <p class="text-xs text-orange-700 mt-2 font-medium">Approaching reorder point</p>
        </div>

        <div class="bg-red-50 rounded-2xl p-6 shadow-sm border border-red-100 hover:-translate-y-1 hover:shadow-lg transition-all duration-300 cursor-pointer border-l-4 border-l-red-500">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-3">
                    <div class="bg-red-100 p-2 rounded-lg text-red-600"><i class="fa-solid fa-circle-xmark"></i></div>
                    <h3 class="font-medium text-red-800 text-sm">Out of Stock</h3>
                </div>
            </div>
            <h2 class="text-3xl font-bold text-red-900 tracking-tight">{{ $products->where('status', 'Out of Stock')->count() }}</h2>
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
                            <h4 class="font-semibold text-sm">Action Required</h4>
                            <p class="text-xs text-gray-300 mt-0.5">Please review restock alerts on the main dashboard for critical items.</p>
                        </div>
                    </div>
                    <a href="/dashboard" class="text-center text-xs bg-white text-navy-900 font-semibold px-4 py-2.5 rounded-lg hover:bg-gray-100 transition-colors shadow-sm whitespace-nowrap shrink-0">
                        View Dashboard DSS
                    </a>
                </div>

                <!-- ========================================== -->
                <!-- REAL DYNAMIC STAGNANT CAPITAL DSS          -->
                <!-- ========================================== -->
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
                <!-- ========================================== -->

            </div>
        </div>

        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex flex-col justify-between hover:shadow-lg transition-shadow duration-300">
            <h2 class="text-md font-semibold text-gray-800 mb-2">Overall Stock Health</h2>
            <div class="relative flex-1 w-full min-h-[180px] flex items-center justify-center">
                <canvas id="stockHealthChart"></canvas>
                <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none mt-2">
                    <!-- DYNAMIC HEALTH PERCENTAGE -->
                    <span class="text-2xl font-bold text-gray-800">
                        {{ $products->count() > 0 ? round(($products->where('status', 'Healthy')->count() / $products->count()) * 100) : 0 }}%
                    </span>
                    <span class="text-xs text-gray-400 font-medium">Healthy</span>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden animate-fade-in delay-200">
        <div class="p-6 border-b border-gray-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <h2 class="text-lg font-semibold text-gray-800">Product Masterlist</h2>
            <div class="flex flex-col sm:flex-row gap-3">
                <select class="px-3 py-2 bg-gray-50 border border-gray-200 text-gray-600 text-sm rounded-lg focus:outline-none focus:ring-2 focus:ring-navy-700 focus:bg-white transition-all cursor-pointer">
                    <option value="all">All Categories</option>
                    <option value="furniture">Furniture</option>
                    <option value="appliances">Appliances</option>
                    <option value="foams">Foams</option>
                    <option value="speakers">Speakers</option>
                    <option value="tv">TV</option>
                </select>
                <div class="relative">
                    <i class="fa-solid fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 text-xs"></i>
                    <input type="text" placeholder="Search SKU or Name..." class="pl-8 pr-4 py-2 bg-gray-50 border border-gray-200 text-gray-600 text-sm rounded-lg focus:outline-none focus:ring-2 focus:ring-navy-700 focus:bg-white w-full sm:w-64 transition-all">
                </div>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500" id="inventoryTable">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
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
                    <!-- DYNAMIC DATABASE LOOP STARTS HERE -->
                    @forelse($products as $product)
                    <tr class="bg-white hover:bg-gray-50 transition-colors duration-200">
                        <td class="px-6 py-4 font-medium text-navy-700">{{ $product->sku }}</td>
                        <td class="px-6 py-4 text-gray-900 font-medium">{{ $product->product_name }}</td>
                        <td class="px-6 py-4 text-xs">{{ $product->category }}</td>
                        <td class="px-6 py-4">₱{{ number_format($product->unit_price, 2) }}</td>
                        
                        <td class="px-6 py-4 text-center font-bold 
                            @if($product->in_stock == 0) text-red-600
                            @elseif($product->in_stock <= $product->reorder_point) text-orange-500
                            @else text-gray-800
                            @endif">
                            {{ $product->in_stock }}
                        </td>
                        
                        <td class="px-6 py-4 text-center">
                            @if($product->status == 'Healthy')
                                <span class="bg-green-100 text-green-700 px-2.5 py-1 rounded-full text-xs font-medium border border-green-200">Healthy</span>
                            @elseif($product->status == 'Low Stock')
                                <span class="bg-orange-100 text-orange-700 px-2.5 py-1 rounded-full text-xs font-medium border border-orange-200">Low Stock</span>
                            @else
                                <span class="bg-red-100 text-red-700 px-2.5 py-1 rounded-full text-xs font-medium border border-red-200">Critical</span>
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
                        <td colspan="7" class="px-6 py-8 text-center text-gray-500">No products found in the database. Add one to get started!</td>
                    </tr>
                    @endforelse
                    <!-- DYNAMIC DATABASE LOOP ENDS HERE -->
                </tbody>
            </table>
        </div>
    </div>
</div>

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

<!-- RESTOCK SHIPMENT MODAL -->
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

<div id="promoModal" class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm hidden z-50 flex items-center justify-center transition-opacity opacity-0 no-print">
    <div class="bg-white rounded-2xl w-full max-w-lg shadow-2xl p-6 transform scale-95 transition-transform duration-300" id="promoModalContent">
        <div class="flex items-center justify-between mb-4 border-b pb-3">
            <h2 class="text-xl font-bold text-navy-900 flex items-center gap-2">
                <i class="fa-solid fa-tags text-orange-500"></i> DSS Capital Recovery Strategy
            </h2>
            <button onclick="closeDssModal('promoModal')" class="text-gray-400 hover:text-gray-600 focus:outline-none">
                <i class="fa-solid fa-xmark text-xl"></i>
            </button>
        </div>
        <div class="mb-5 text-sm text-gray-700">
            <!-- DYNAMIC MODAL TEXT -->
            <p class="mb-2"><strong class="font-semibold w-36 inline-block">Item:</strong> {{ $stagnantTitle }}</p>
            <p class="mb-2"><strong class="font-semibold w-36 inline-block">Capital Tied Up:</strong> <span class="font-mono text-gray-900">₱{{ number_format($stagnantValue, 2) }}</span></p>
        </div>
        <div class="bg-orange-50 border border-orange-200 p-4 mb-6 rounded-lg">
            <p class="text-orange-800 font-bold text-sm mb-1 uppercase tracking-wider">Action Suggested:</p>
            <p class="text-orange-900 text-base mb-2">Apply a <strong>10% Markdown Promo</strong>.</p>
            <p class="text-xs text-orange-700 italic border-t border-orange-200 pt-2 mt-2">Projection: This price reduction is mathematically projected to stimulate movement and recover stagnant capital within 14 days without dropping below your supplier cost.</p>
        </div>
        <div class="flex justify-end gap-3">
            <button onclick="closeDssModal('promoModal')" class="px-4 py-2 text-sm font-medium text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 focus:outline-none">Close</button>
            <button onclick="closeDssModal('promoModal')" class="px-4 py-2 text-sm font-medium text-white bg-navy-900 rounded-lg hover:bg-navy-700 flex items-center gap-2 focus:outline-none">
                <i class="fa-solid fa-check"></i> Acknowledge Insight
            </button>
        </div>
    </div>
</div>

<!-- PRINT VIEW REMAINS UNTOUCHED BELOW -->
<div class="hidden print:block text-black bg-white w-full">
    <div class="text-center border-b-2 border-black pb-6 mb-8">
        <h1 class="text-3xl font-bold uppercase tracking-wider text-black">Ken's Marketing</h1>
        <p class="text-sm mt-1">Ligao City, Bicol</p>
        <h2 class="text-xl font-semibold mt-4">DSS Comprehensive Inventory Audit & Action Plan</h2>
        <p class="text-sm mt-1 text-gray-600">Report Generated: {{ date('F j, Y, g:i a') }}</p>
    </div>

    <div class="mb-8">
        <h3 class="text-lg font-bold border-b border-gray-400 mb-4 pb-1 uppercase text-black">1. Stock Health Summary</h3>
        <div class="grid grid-cols-4 gap-4 text-sm">
            <div class="flex flex-col justify-center px-2 py-4 border border-gray-400 rounded bg-gray-50 text-center">
                <p class="text-gray-700 font-bold uppercase tracking-wide text-[11px]">Total SKUs Tracked</p>
                <p class="text-xl font-bold mt-2 whitespace-nowrap">{{ $products->count() }}</p>
            </div>
            <div class="flex flex-col justify-center px-2 py-4 border border-gray-400 rounded bg-gray-50 text-center">
                <p class="text-gray-700 font-bold uppercase tracking-wide text-[11px]">Total Asset Value</p>
                <p class="text-xl font-bold mt-2 whitespace-nowrap">₱{{ number_format($products->sum(function($product) { return $product->unit_price * $product->in_stock; }), 2) }}</p>
            </div>
            <div class="flex flex-col justify-center px-2 py-4 border border-gray-400 rounded bg-gray-50 text-center">
                <p class="text-orange-700 font-bold uppercase tracking-wide text-[11px]">Low Stock Items</p>
                <p class="text-xl font-bold mt-2 text-orange-700 whitespace-nowrap">{{ $products->where('status', 'Low Stock')->count() }}</p>
            </div>
            <div class="flex flex-col justify-center px-2 py-4 border border-gray-400 rounded bg-gray-50 text-center">
                <p class="text-red-700 font-bold uppercase tracking-wide text-[11px]">Critical / Out of Stock</p>
                <p class="text-xl font-bold mt-2 text-red-700 whitespace-nowrap">{{ $products->where('status', 'Out of Stock')->count() }}</p>
            </div>
        </div>
    </div>

    <div class="mb-12 mt-8">
        <h3 class="text-lg font-bold border-b border-gray-400 mb-4 pb-1 uppercase text-black">3. Complete Product Ledger (Active Items)</h3>
        <table class="w-full text-sm text-left border-collapse border border-gray-400">
            <thead>
                <tr class="bg-gray-100">
                    <th class="border border-gray-400 px-3 py-2 font-bold text-black whitespace-nowrap">SKU</th>
                    <th class="border border-gray-400 px-3 py-2 font-bold text-black">Product Name</th>
                    <th class="border border-gray-400 px-3 py-2 font-bold text-black">Category</th>
                    <th class="border border-gray-400 px-3 py-2 font-bold text-black text-right whitespace-nowrap">Unit Value (₱)</th>
                    <th class="border border-gray-400 px-3 py-2 font-bold text-black text-center whitespace-nowrap">Count</th>
                    <th class="border border-gray-400 px-3 py-2 font-bold text-black text-center">System Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($products as $product)
                <tr>
                    <td class="border border-gray-400 px-3 py-2 font-mono text-xs whitespace-nowrap">{{ $product->sku }}</td>
                    <td class="border border-gray-400 px-3 py-2 font-medium">{{ $product->product_name }}</td>
                    <td class="border border-gray-400 px-3 py-2">{{ $product->category }}</td>
                    <td class="border border-gray-400 px-3 py-2 text-right whitespace-nowrap">{{ number_format($product->unit_price, 2) }}</td>
                    <td class="border border-gray-400 px-3 py-2 text-center 
                        @if($product->in_stock == 0) text-red-700 font-bold
                        @elseif($product->in_stock <= $product->reorder_point) text-orange-700 font-bold
                        @endif">
                        {{ $product->in_stock }}
                    </td>
                    <td class="border border-gray-400 px-3 py-2 text-center font-bold uppercase
                        @if($product->status == 'Healthy') text-green-700
                        @elseif($product->status == 'Low Stock') text-orange-700
                        @else text-red-700
                        @endif">
                        {{ $product->status }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="grid grid-cols-3 gap-8 mt-16 text-sm print-break-inside-avoid">
        <div class="text-center"><div class="border-b border-black w-full mb-2"></div><p class="font-bold text-black">Prepared By</p><p class="text-gray-600">System Administrator</p></div>
        <div class="text-center"><div class="border-b border-black w-full mb-2"></div><p class="font-bold text-black">Audited By</p><p class="text-gray-600">Warehouse Custodian</p></div>
        <div class="text-center"><div class="border-b border-black w-full mb-2"></div><p class="font-bold text-black">Approved By</p><p class="text-gray-600">Store Manager</p></div>
    </div>
</div>

<style>
    @media print {
        @page { margin: 1cm; size: A4 portrait; }
        body * { visibility: hidden; }
        .print\:block, .print\:block * { visibility: visible; }
        .print\:block { position: absolute; left: 0; top: 0; width: 100%; }
        #sidebar, header { display: none !important; }
        .print-break-inside-avoid { page-break-inside: avoid; }
        * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // --- ADD PRODUCT MODAL ---
    const addModal = document.getElementById('addProductModal');
    const addModalContent = document.getElementById('modalContent');
    function openAddProductModal() {
        addModal.classList.remove('hidden');
        setTimeout(() => { addModal.classList.remove('opacity-0'); addModalContent.classList.remove('scale-95'); }, 10);
    }
    function closeAddProductModal() {
        addModal.classList.add('opacity-0'); addModalContent.classList.add('scale-95');
        setTimeout(() => { addModal.classList.add('hidden'); }, 300);
    }

    // --- EDIT PRODUCT MODAL ---
    const editModal = document.getElementById('editProductModal');
    const editModalContent = document.getElementById('editModalContent');
    
    function openEditProductModal(buttonElement, productId, reorderPoint) {
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

    // --- NEW: RESTOCK SHIPMENT MODAL ---
    const restockModal = document.getElementById('restockShipmentModal');
    const restockBox = document.getElementById('restockModalBoxContent');

    function openRestockModal(id, name, currentStock) {
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
        restockModal.classList.add('opacity-0'); 
        restockBox.classList.add('scale-95');
        setTimeout(() => { restockModal.classList.add('hidden'); }, 300);
    }

    // --- DSS SYSTEM MODALS ---
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

    // --- DYNAMIC CHART.JS ---
    document.addEventListener("DOMContentLoaded", function() {
        const ctx = document.getElementById('stockHealthChart').getContext('2d');
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Healthy', 'Low Stock', 'Out of Stock'],
                datasets: [{
                    // DYNAMIC DATA INJECTED HERE
                    data: [
                        {{ $products->where('status', 'Healthy')->count() }}, 
                        {{ $products->where('status', 'Low Stock')->count() }}, 
                        {{ $products->where('status', 'Out of Stock')->count() }}
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