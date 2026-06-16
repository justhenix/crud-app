<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>PC Laboratory Inventory</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

        <!-- Scripts & Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

        <style>
            [x-cloak] { display: none !important; }
        </style>
    </head>
    <body class="min-h-screen bg-slate-50 text-slate-900 font-sans antialiased flex flex-col" x-data="{ activeTab: 'all' }">
        <!-- App Header -->
        <header class="border-b border-slate-200 bg-white">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex h-16 items-center justify-between">
                    <div class="flex items-center">
                        <span class="text-lg font-semibold tracking-tight">PC Lab Inventory</span>
                    </div>
                    <div>
                        <span class="text-xs text-slate-400">System Shell</span>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content Area -->
        <main class="mx-auto max-w-7xl w-full px-4 sm:px-6 lg:px-8 py-8 flex-1">
            <!-- Navigation Tabs -->
            <div class="border-b border-slate-200 mb-6">
                <nav class="-mb-px flex space-x-6" aria-label="Tabs">
                    <button 
                        @click="activeTab = 'all'" 
                        :class="activeTab === 'all' ? 'border-slate-800 text-slate-900' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300'"
                        class="whitespace-nowrap py-3 px-1 border-b-2 font-medium text-sm transition-colors duration-150 cursor-pointer">
                        All Items
                    </button>
                    <button 
                        @click="activeTab = 'computers'" 
                        :class="activeTab === 'computers' ? 'border-slate-800 text-slate-900' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300'"
                        class="whitespace-nowrap py-3 px-1 border-b-2 font-medium text-sm transition-colors duration-150 cursor-pointer">
                        Computers
                    </button>
                    <button 
                        @click="activeTab = 'peripherals'" 
                        :class="activeTab === 'peripherals' ? 'border-slate-800 text-slate-900' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300'"
                        class="whitespace-nowrap py-3 px-1 border-b-2 font-medium text-sm transition-colors duration-150 cursor-pointer">
                        Peripherals
                    </button>
                </nav>
            </div>

            <!-- Tab Contents -->
            <div>
                <div x-show="activeTab === 'all'">
                    <span class="text-xs text-slate-400">Showing all items...</span>
                </div>
                <div x-show="activeTab === 'computers'" x-cloak>
                    <span class="text-xs text-slate-400">Showing computers...</span>
                </div>
                <div x-show="activeTab === 'peripherals'" x-cloak>
                    <span class="text-xs text-slate-400">Showing peripherals...</span>
                </div>
            </div>
        </main>

        <!-- App Footer -->
        <footer class="border-t border-slate-200 bg-white py-4 text-center text-xs text-slate-500">
            <span>PC Laboratory Inventory System &bull; Admin Panel</span>
        </footer>
    </body>
</html>
