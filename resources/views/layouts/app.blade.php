<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Ken\'s Marketing DSS')</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .sidebar-text {
            transition: max-width 0.3s ease-in-out, opacity 0.2s ease-in-out, margin 0.3s ease-in-out;
        }
        .text-expanded {
            max-width: 150px;
            opacity: 1;
            margin-left: 0.75rem; /* ml-3 */
        }
        .text-collapsed {
            max-width: 0px;
            opacity: 0;
            margin-left: 0px;
        }
        
        /* Dashboard Load Animations */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in {
            animation: fadeInUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            opacity: 0; /* Starts hidden */
        }
        /* Stagger delays */
        .delay-100 { animation-delay: 100ms; }
        .delay-200 { animation-delay: 200ms; }
        .delay-300 { animation-delay: 300ms; }

        /* Custom scrollbar for search results */
        #search-results::-webkit-scrollbar { width: 6px; }
        #search-results::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 8px; }
        #search-results::-webkit-scrollbar-thumb { background: #c1c1c1; border-radius: 8px; }
    </style>
</head>
<body class="bg-gray-50 text-gray-800 font-sans antialiased flex h-screen overflow-hidden">

    <aside id="sidebar" class="bg-navy-900 text-white w-64 flex flex-col transition-all duration-300 ease-in-out z-20 shrink-0 relative">
        <div class="h-16 flex items-center justify-center px-4 border-b border-navy-700/50 overflow-hidden">
            <img src="{{ asset('images/logo.png') }}" alt="Logo" class="h-8 w-auto object-contain sidebar-logo transition-transform duration-300 shrink-0">
            <span class="font-semibold text-lg tracking-wide sidebar-text text-expanded whitespace-nowrap overflow-hidden">Ken's Marketing</span>
        </div>

        <nav class="flex-1 py-6 px-3 space-y-2 overflow-y-auto overflow-x-hidden">
            <a href="/dashboard" class="flex items-center px-3 py-3 rounded-lg transition-colors duration-200 group {{ request()->is('dashboard') ? 'bg-navy-700 text-white' : 'text-gray-300 hover:bg-navy-700/50 hover:text-white' }}" title="Dashboard">
                <i class="fa-solid fa-chart-pie w-6 text-center text-lg shrink-0"></i>
                <span class="font-medium sidebar-text text-expanded whitespace-nowrap overflow-hidden">Dashboard</span>
            </a>
            
            <a href="/sales" class="flex items-center px-3 py-3 rounded-lg transition-colors duration-200 group {{ request()->is('sales') ? 'bg-navy-700 text-white' : 'text-gray-300 hover:bg-navy-700/50 hover:text-white' }}" title="Sales Module">
                <i class="fa-solid fa-cart-shopping w-6 text-center text-lg shrink-0"></i>
                <span class="font-medium sidebar-text text-expanded whitespace-nowrap overflow-hidden">Sales Module</span>
            </a>
            
            <a href="/inventory" class="flex items-center px-3 py-3 rounded-lg transition-colors duration-200 group {{ request()->is('inventory') ? 'bg-navy-700 text-white' : 'text-gray-300 hover:bg-navy-700/50 hover:text-white' }}" title="Inventory">
                <i class="fa-solid fa-boxes-stacked w-6 text-center text-lg shrink-0"></i>
                <span class="font-medium sidebar-text text-expanded whitespace-nowrap overflow-hidden">Inventory</span>
            </a>
            
            <a href="/reports" class="flex items-center px-3 py-3 rounded-lg transition-colors duration-200 group {{ request()->is('reports') ? 'bg-navy-700 text-white' : 'text-gray-300 hover:bg-navy-700/50 hover:text-white' }}" title="Reports">
                <i class="fa-solid fa-file-invoice w-6 text-center text-lg shrink-0"></i>
                <span class="font-medium sidebar-text text-expanded whitespace-nowrap overflow-hidden">Reports</span>
            </a>
            
            <a href="/dss-insights" class="flex items-center px-3 py-3 rounded-lg transition-colors duration-200 group {{ request()->is('dss-insights') ? 'bg-navy-700 text-white' : 'text-gray-300 hover:bg-navy-700/50 hover:text-white' }}" title="DSS Insights">
                <i class="fa-solid fa-lightbulb w-6 text-center text-lg shrink-0"></i>
                <span class="font-medium sidebar-text text-expanded whitespace-nowrap overflow-hidden">DSS Insights</span>
            </a>

            <!-- ADMIN SECURITY HUB LINK (BULLETPROOF CHECK APPLIED) -->
            @if(Auth::check() && strtolower(trim(Auth::user()->role)) === 'admin')
            <a href="{{ route('admin.security') }}" class="flex items-center px-3 py-3 rounded-lg transition-colors duration-200 group {{ request()->routeIs('admin.security') ? 'bg-navy-700 text-white' : 'text-gray-300 hover:bg-navy-700/50 hover:text-white' }}" title="Security Hub">
                <i class="fa-solid fa-shield-halved w-6 text-center text-lg shrink-0"></i>
                <span class="font-medium sidebar-text text-expanded whitespace-nowrap overflow-hidden">Security Hub</span>
            </a>
            @endif
        </nav>

        <div class="p-3 border-t border-navy-700/50">
            <a href="{{ route('logout') }}" 
               onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
               class="flex items-center px-3 py-2 text-gray-300 hover:text-white transition-colors duration-200 group cursor-pointer" title="Logout">
                <i class="fa-solid fa-right-from-bracket w-6 text-center text-lg shrink-0"></i>
                <span class="font-medium sidebar-text text-expanded whitespace-nowrap overflow-hidden">Logout</span>
            </a>

            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden" style="display: none;">
                @csrf
            </form>
        </div>
    </aside>

    <main class="flex-1 flex flex-col h-screen overflow-hidden">
        <header class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-6 z-50 shrink-0">
            <div class="flex items-center gap-4">
                <button onclick="toggleSidebar()" class="text-gray-500 hover:text-navy-900 transition-colors focus:outline-none p-2 rounded-lg hover:bg-gray-100">
                    <i class="fa-solid fa-bars text-xl"></i>
                </button>
                <h1 class="text-xl font-semibold text-gray-800">@yield('header_title', 'Dashboard')</h1>
            </div>
            
            <div class="flex items-center gap-6">
                <!-- LIVE GLOBAL SEARCH BAR (Option 1 - Hybrid) -->
                <div class="relative hidden md:block">
                    <div class="relative">
                        <i class="fa-solid fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        <input type="text" id="global-search-input" placeholder="Search system globally..." autocomplete="off" class="pl-10 pr-4 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-navy-700 focus:bg-white w-72 transition-all">
                        
                        <!-- Search Loading Spinner -->
                        <i id="search-spinner" class="fa-solid fa-circle-notch fa-spin absolute right-3 top-1/2 transform -translate-y-1/2 text-navy-700 hidden"></i>
                    </div>

                    <!-- Dropdown Results Container -->
                    <div id="search-results-container" class="absolute top-full left-0 mt-2 w-80 bg-white border border-gray-100 rounded-xl shadow-xl overflow-hidden hidden transform transition-all z-50 origin-top">
                        <div id="search-results" class="max-h-96 overflow-y-auto p-2 space-y-1">
                            <!-- Results injected here via JS -->
                        </div>
                    </div>
                </div>
                <!-- / LIVE GLOBAL SEARCH BAR -->

                <!-- DYNAMIC USER PROFILE (REPLACES HARDCODED 'AD Admin') -->
                @if(Auth::check())
                <div class="flex items-center gap-3 cursor-pointer border-l border-gray-200 pl-6 ml-2">
                    <div class="w-9 h-9 bg-navy-900 text-white rounded-full flex items-center justify-center text-sm font-bold shadow-sm hover:bg-navy-700 transition-colors uppercase">
                        {{ substr(Auth::user()->name, 0, 2) }}
                    </div>
                    <div class="hidden md:flex flex-col text-sm">
                        <span class="font-semibold text-gray-800 leading-tight">{{ Auth::user()->name }}</span>
                        <span class="text-[11px] text-gray-500 capitalize leading-tight font-medium">{{ strtolower(trim(Auth::user()->role)) }}</span>
                    </div>
                </div>
                @endif
            </div>
        </header>

        <div class="flex-1 overflow-y-auto p-6 md:p-8 bg-[#F9FAFB] scroll-smooth" id="main-content" onclick="closeSearch()">
            @yield('content')
        </div>
    </main>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const texts = document.querySelectorAll('.sidebar-text');
            
            if (sidebar.classList.contains('w-64')) {
                sidebar.classList.remove('w-64');
                sidebar.classList.add('w-20');
                
                texts.forEach(text => {
                    text.classList.remove('text-expanded');
                    text.classList.add('text-collapsed');
                });
            } else {
                sidebar.classList.remove('w-20');
                sidebar.classList.add('w-64');
                
                texts.forEach(text => {
                    text.classList.remove('text-collapsed');
                    text.classList.add('text-expanded');
                });
            }
        }

        // ==========================================
        // REAL-TIME HYBRID GLOBAL SEARCH ENGINE
        // ==========================================
        const searchInput = document.getElementById('global-search-input');
        const resultsContainer = document.getElementById('search-results-container');
        const resultsBox = document.getElementById('search-results');
        const spinner = document.getElementById('search-spinner');
        let searchTimeout = null;

        searchInput.addEventListener('input', function() {
            const query = this.value.trim();
            
            if (query.length < 2) {
                resultsContainer.classList.add('hidden');
                return;
            }

            spinner.classList.remove('hidden');

            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                fetch(`/global-search?query=${encodeURIComponent(query)}`)
                    .then(response => response.json())
                    .then(data => {
                        renderResults(data, query);
                        spinner.classList.add('hidden');
                        resultsContainer.classList.remove('hidden');
                    })
                    .catch(error => {
                        console.error('Search error:', error);
                        spinner.classList.add('hidden');
                    });
            }, 300);
        });

        function renderResults(data, query) {
            resultsBox.innerHTML = ''; 
            let hasResults = false;
            const q = query.toLowerCase();

            // 1. RENDER SYSTEM MODULES (The Smart Router part)
            const modules = [
                { name: 'Dashboard', url: '/dashboard', icon: 'fa-chart-pie', keys: ['dash', 'home', 'trend'] },
                { name: 'Sales Module', url: '/sales', icon: 'fa-cart-shopping', keys: ['sale', 'pos', 'peak', 'forecast', 'ledger', 'transaction'] },
                { name: 'Inventory Management', url: '/inventory', icon: 'fa-boxes-stacked', keys: ['inv', 'stock', 'product', 'sku'] },
                { name: 'Report Center', url: '/reports', icon: 'fa-file-invoice', keys: ['report', 'summary', 'audit', 'margin', 'archive'] },
                { name: 'DSS Insights', url: '/dss-insights', icon: 'fa-lightbulb', keys: ['dss', 'insight', 'pattern'] }
            ];

            let matchedModules = modules.filter(m => m.keys.some(k => q.includes(k)) || m.name.toLowerCase().includes(q));

            if (matchedModules.length > 0) {
                hasResults = true;
                resultsBox.innerHTML += `<div class="px-3 py-1.5 text-[10px] font-bold text-gray-400 uppercase tracking-wider bg-gray-50/50 mt-1 first:mt-0 rounded">System Pages</div>`;
                matchedModules.forEach(mod => {
                    resultsBox.innerHTML += `
                        <a href="${mod.url}" class="flex items-center gap-3 p-2.5 hover:bg-gray-100 rounded-lg transition-colors group cursor-pointer">
                            <div class="bg-navy-900 text-white p-1.5 rounded-md shadow-sm"><i class="fa-solid ${mod.icon} text-xs w-3 text-center"></i></div>
                            <div>
                                <h4 class="text-sm font-semibold text-gray-800 group-hover:text-navy-900">${mod.name}</h4>
                                <p class="text-[10px] text-gray-500 uppercase tracking-wide">Navigate to module</p>
                            </div>
                        </a>
                    `;
                });
            }

            // 2. RENDER PRODUCTS
            if (data.products.length > 0) {
                hasResults = true;
                resultsBox.innerHTML += `<div class="px-3 py-1.5 text-[10px] font-bold text-gray-400 uppercase tracking-wider bg-gray-50/50 mt-2 rounded">Inventory Items</div>`;
                data.products.forEach(product => {
                    resultsBox.innerHTML += `
                        <a href="/inventory?search=${encodeURIComponent(product.product_name)}" class="flex items-center gap-3 p-2.5 hover:bg-blue-50 rounded-lg transition-colors group cursor-pointer">
                            <div class="bg-blue-100 text-blue-600 p-1.5 rounded-md"><i class="fa-solid fa-box text-xs w-3 text-center"></i></div>
                            <div>
                                <h4 class="text-sm font-semibold text-gray-800 group-hover:text-blue-700">${product.product_name}</h4>
                                <p class="text-xs text-gray-500">SKU: ${product.sku} • Stock: ${product.in_stock}</p>
                            </div>
                        </a>
                    `;
                });
            }

            // 3. RENDER SALES
            if (data.sales.length > 0) {
                hasResults = true;
                resultsBox.innerHTML += `<div class="px-3 py-1.5 text-[10px] font-bold text-gray-400 uppercase tracking-wider bg-gray-50/50 mt-2 rounded">Sales & Receipts</div>`;
                data.sales.forEach(sale => {
                    let rcNum = "RC-" + String(sale.id).padStart(5, '0');
                    resultsBox.innerHTML += `
                        <a href="/sales" class="flex items-center gap-3 p-2.5 hover:bg-green-50 rounded-lg transition-colors group cursor-pointer">
                            <div class="bg-green-100 text-green-600 p-1.5 rounded-md"><i class="fa-solid fa-receipt text-xs w-3 text-center"></i></div>
                            <div>
                                <h4 class="text-sm font-semibold text-gray-800 group-hover:text-green-700">#${rcNum}</h4>
                                <p class="text-xs text-gray-500">Sold: ${sale.quantity_sold}x ${sale.product ? sale.product.product_name : 'Item'}</p>
                            </div>
                        </a>
                    `;
                });
            }

            // 4. RENDER REPORTS
            if (data.reports.length > 0) {
                hasResults = true;
                resultsBox.innerHTML += `<div class="px-3 py-1.5 text-[10px] font-bold text-gray-400 uppercase tracking-wider bg-gray-50/50 mt-2 rounded">Archived Reports</div>`;
                data.reports.forEach(report => {
                    let icon = report.format === 'pdf' ? '<i class="fa-solid fa-file-pdf text-red-500 text-xs w-3 text-center"></i>' : '<i class="fa-solid fa-file-excel text-green-600 text-xs w-3 text-center"></i>';
                    let link = report.format === 'pdf' ? `/reports/archive/${report.id}/view` : `/reports/archive/${report.id}/download`;
                    
                    resultsBox.innerHTML += `
                        <a href="${link}" target="_blank" class="flex items-center gap-3 p-2.5 hover:bg-red-50 rounded-lg transition-colors group cursor-pointer">
                            <div class="bg-gray-100 p-1.5 rounded-md">${icon}</div>
                            <div class="overflow-hidden">
                                <h4 class="text-sm font-semibold text-gray-800 group-hover:text-red-700 truncate">${report.report_name}</h4>
                            </div>
                        </a>
                    `;
                });
            }

            // If completely empty
            if (!hasResults) {
                resultsBox.innerHTML = `
                    <div class="p-6 text-center text-gray-500">
                        <i class="fa-solid fa-magnifying-glass text-2xl mb-2 text-gray-300"></i>
                        <p class="text-sm font-medium">No results found for "${query}"</p>
                    </div>
                `;
            }
        }

        function closeSearch() {
            resultsContainer.classList.add('hidden');
        }
        
        searchInput.addEventListener('click', function(e) {
            e.stopPropagation();
            if(this.value.trim().length >= 2) resultsContainer.classList.remove('hidden');
        });
    </script>
    
    <!-- ========================================== -->
    <!-- GLOBAL SPAM-CLICK FAILSAFE                 -->
    <!-- Protects all forms from double-submissions -->
    <!-- ========================================== -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Find every form on the website
            const forms = document.querySelectorAll('form');
            
            forms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    // Find the submit button inside the form
                    const submitBtn = this.querySelector('button[type="submit"]');
                    
                    if (submitBtn) {
                        // If the button is already processing, block any extra clicks
                        if (submitBtn.classList.contains('is-processing')) {
                            e.preventDefault();
                            return false;
                        }
                        
                        // 1. Lock the button
                        submitBtn.classList.add('is-processing');
                        submitBtn.style.pointerEvents = 'none';
                        submitBtn.style.opacity = '0.7';
                        
                        // 2. Change the button text to show a loading spinner
                        // Note: Requires FontAwesome (which you already use for your icons)
                        if (!submitBtn.dataset.originalText) {
                            submitBtn.dataset.originalText = submitBtn.innerHTML;
                        }
                        submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Processing...';
                    }
                });
            });
        });
    </script>
</body>
</html>