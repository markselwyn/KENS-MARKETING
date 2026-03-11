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
        </nav>

        <div class="p-3 border-t border-navy-700/50">
            <a href="/" class="flex items-center px-3 py-2 text-gray-300 hover:text-white transition-colors duration-200 group" title="Logout">
                <i class="fa-solid fa-right-from-bracket w-6 text-center text-lg shrink-0"></i>
                <span class="font-medium sidebar-text text-expanded whitespace-nowrap overflow-hidden">Logout</span>
            </a>
        </div>
    </aside>

    <main class="flex-1 flex flex-col h-screen overflow-hidden">
        <header class="h-16 bg-white border-b border-gray-200 flex items-center justify-between px-6 z-10 shrink-0">
            <div class="flex items-center gap-4">
                <button onclick="toggleSidebar()" class="text-gray-500 hover:text-navy-900 transition-colors focus:outline-none p-2 rounded-lg hover:bg-gray-100">
                    <i class="fa-solid fa-bars text-xl"></i>
                </button>
                <h1 class="text-xl font-semibold text-gray-800">@yield('header_title', 'Dashboard')</h1>
            </div>
            
            <div class="flex items-center gap-6">
                <div class="relative hidden md:block">
                    <i class="fa-solid fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                    <input type="text" placeholder="Search insights..." class="pl-10 pr-4 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-navy-700 focus:bg-white w-64 transition-all">
                </div>
                <div class="flex items-center gap-3 cursor-pointer">
                    <div class="w-9 h-9 bg-navy-900 text-white rounded-full flex items-center justify-center text-sm font-semibold shadow-sm hover:bg-navy-700 transition-colors">
                        AD
                    </div>
                    <div class="hidden md:block text-sm">
                        <p class="font-medium text-gray-700">Admin</p>
                    </div>
                </div>
            </div>
        </header>

        <div class="flex-1 overflow-y-auto p-6 md:p-8 bg-[#F9FAFB] scroll-smooth">
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
                
                // Switch to collapsed state
                texts.forEach(text => {
                    text.classList.remove('text-expanded');
                    text.classList.add('text-collapsed');
                });
            } else {
                sidebar.classList.remove('w-20');
                sidebar.classList.add('w-64');
                
                // Switch to expanded state
                texts.forEach(text => {
                    text.classList.remove('text-collapsed');
                    text.classList.add('text-expanded');
                });
            }
        }
    </script>
</body>
</html>