<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @php
        // Centralized Settings Fetch
        $businessName = \App\Models\Setting::getValue('business_name', 'Blotanna Nig Ltd');
        $businessLogo = \App\Models\Setting::getValue('business_logo');
        $currencySymbol = \App\Models\Setting::getValue('currency_symbol', '$');
    @endphp

    <title>{{ $businessName }}</title>
    
    @if($businessLogo)
        <link rel="icon" href="{{ route('files.display', ['path' => $businessLogo]) }}">
    @else
        <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>📦</text></svg>">
    @endif

    <!-- Theme Script (Prevent FOUC) -->
    <script>
        // Check local storage or system preference
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
        
        // Global Currency
        window.APP_CURRENCY = "{{ $currencySymbol }}";
    </script>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">

    <!-- Local Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-background-light dark:bg-[#0f1115] text-[#111318] dark:text-gray-100 font-display min-h-screen transition-colors duration-200 overflow-hidden" x-data="{ sidebarOpen: false }">
    
    @php
        $routeName = request()->route() ? request()->route()->getName() : 'dashboard';
        $skeletonType = 'dashboard';

        if ($routeName === 'sales.create') {
            $skeletonType = 'pos';
        } elseif (in_array($routeName, ['products.index', 'customers.index', 'sales.index', 'invoices.index', 'trash.index', 'reports.index'])) {
            $skeletonType = 'table';
        } elseif (str_contains($routeName, 'create') || str_contains($routeName, 'edit') || $routeName === 'settings.index' || str_contains($routeName, 'show')) {
            $skeletonType = 'form';
        }
    @endphp

    <!-- Intelligent Page-Specific Skeleton Loader -->
    <div id="app-skeleton" class="fixed inset-0 z-[9999] bg-[#f6f6f8] dark:bg-[#101622] flex transition-opacity duration-300">
        <!-- Sidebar Skeleton -->
        <div class="hidden md:flex w-64 flex-col gap-8 p-4 border-r border-gray-200 dark:border-gray-800 bg-white dark:bg-[#111318] shrink-0">
            <div class="flex items-center gap-3">
                <div class="size-10 bg-gray-200 dark:bg-gray-800 rounded-lg animate-pulse"></div>
                <div class="flex flex-col gap-2">
                    <div class="h-3 w-32 bg-gray-200 dark:bg-gray-800 rounded animate-pulse"></div>
                    <div class="h-2 w-20 bg-gray-100 dark:bg-gray-800/50 rounded animate-pulse"></div>
                </div>
            </div>
            <div class="space-y-3 mt-4">
                @for($i = 0; $i < 7; $i++)
                <div class="h-10 w-full bg-gray-100 dark:bg-gray-800/40 rounded-lg animate-pulse"></div>
                @endfor
            </div>
        </div>

        <!-- Main Content Skeleton -->
        <div class="flex-1 flex flex-col min-w-0 h-screen overflow-hidden">
            <!-- Header Skeleton -->
            <div class="h-16 border-b border-gray-200 dark:border-gray-800 bg-white dark:bg-[#111318] px-4 md:px-8 flex items-center justify-between shrink-0">
                <div class="h-6 w-48 bg-gray-200 dark:bg-gray-800 rounded animate-pulse"></div>
                <div class="h-9 w-32 bg-gray-200 dark:bg-gray-800 rounded animate-pulse"></div>
            </div>

            <!-- Dynamic Content Area -->
            <div class="flex-1 overflow-hidden p-4 md:p-8">
                
                @if($skeletonType == 'dashboard')
                    <!-- Dashboard Skeleton -->
                    <div class="animate-pulse space-y-6">
                        <!-- Stats Grid -->
                        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
                            @for($i=0; $i<4; $i++)
                            <div class="h-32 bg-white dark:bg-[#1e232f] border border-gray-200 dark:border-gray-800 rounded-xl p-6">
                                <div class="h-4 w-24 bg-gray-200 dark:bg-gray-800 rounded mb-4"></div>
                                <div class="h-8 w-32 bg-gray-200 dark:bg-gray-800 rounded"></div>
                            </div>
                            @endfor
                        </div>
                        <!-- Charts Area -->
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                            <div class="lg:col-span-2 h-80 bg-white dark:bg-[#1e232f] border border-gray-200 dark:border-gray-800 rounded-xl p-6">
                                <div class="h-6 w-48 bg-gray-200 dark:bg-gray-800 rounded mb-8"></div>
                                <div class="flex items-end gap-2 h-48">
                                    <div class="w-full bg-gray-100 dark:bg-gray-800 h-[40%] rounded-t"></div>
                                    <div class="w-full bg-gray-100 dark:bg-gray-800 h-[70%] rounded-t"></div>
                                    <div class="w-full bg-gray-100 dark:bg-gray-800 h-[50%] rounded-t"></div>
                                    <div class="w-full bg-gray-100 dark:bg-gray-800 h-[80%] rounded-t"></div>
                                    <div class="w-full bg-gray-100 dark:bg-gray-800 h-[60%] rounded-t"></div>
                                </div>
                            </div>
                            <div class="h-80 bg-white dark:bg-[#1e232f] border border-gray-200 dark:border-gray-800 rounded-xl p-6">
                                <div class="h-6 w-32 bg-gray-200 dark:bg-gray-800 rounded mb-6"></div>
                                <div class="space-y-4">
                                    @for($i=0; $i<4; $i++)
                                    <div class="flex items-center gap-3">
                                        <div class="size-10 bg-gray-200 dark:bg-gray-800 rounded-lg"></div>
                                        <div class="flex-1">
                                            <div class="h-3 w-3/4 bg-gray-200 dark:bg-gray-800 rounded mb-1"></div>
                                            <div class="h-2 w-1/2 bg-gray-200 dark:bg-gray-800 rounded"></div>
                                        </div>
                                    </div>
                                    @endfor
                                </div>
                            </div>
                        </div>
                    </div>

                @elseif($skeletonType == 'pos')
                    <!-- POS Skeleton -->
                    <div class="animate-pulse flex h-full gap-0 md:gap-4 flex-col md:flex-row">
                        <!-- Product Grid -->
                        <div class="flex-1 bg-white dark:bg-[#1e232f] border border-gray-200 dark:border-gray-800 rounded-xl p-4 flex flex-col">
                            <div class="h-10 bg-gray-100 dark:bg-gray-800 rounded-lg w-full mb-4"></div>
                            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 overflow-hidden flex-1">
                                @for($i=0; $i<12; $i++)
                                <div class="bg-gray-100 dark:bg-gray-800 rounded-xl flex flex-col p-3">
                                    <div class="aspect-square bg-gray-200 dark:bg-gray-700 rounded-lg mb-2"></div>
                                    <div class="h-3 w-3/4 bg-gray-200 dark:bg-gray-700 rounded mb-1"></div>
                                    <div class="h-3 w-1/2 bg-gray-200 dark:bg-gray-700 rounded"></div>
                                </div>
                                @endfor
                            </div>
                        </div>
                        <!-- Cart Sidebar -->
                        <div class="w-full md:w-96 bg-white dark:bg-[#1e232f] border border-gray-200 dark:border-gray-800 rounded-xl hidden md:flex flex-col p-4">
                            <div class="h-8 w-32 bg-gray-200 dark:bg-gray-800 rounded mb-6"></div>
                            <div class="space-y-3 flex-1">
                                <div class="h-16 bg-gray-100 dark:bg-gray-800 rounded-lg w-full"></div>
                                <div class="h-16 bg-gray-100 dark:bg-gray-800 rounded-lg w-full"></div>
                                <div class="h-16 bg-gray-100 dark:bg-gray-800 rounded-lg w-full"></div>
                            </div>
                            <div class="h-32 bg-gray-100 dark:bg-gray-800 rounded-lg mt-4"></div>
                        </div>
                    </div>

                @elseif($skeletonType == 'table')
                    <!-- Table Skeleton -->
                    <div class="animate-pulse space-y-4">
                        <!-- Filters -->
                        <div class="h-14 bg-white dark:bg-[#1e232f] border border-gray-200 dark:border-gray-800 rounded-xl w-full p-2 flex items-center gap-2">
                            <div class="h-10 bg-gray-100 dark:bg-gray-800 rounded-lg flex-1"></div>
                            <div class="h-10 w-24 bg-gray-100 dark:bg-gray-800 rounded-lg"></div>
                        </div>
                        <!-- Table Rows -->
                        <div class="bg-white dark:bg-[#1e232f] rounded-xl border border-gray-200 dark:border-gray-800 overflow-hidden">
                            <div class="h-12 bg-gray-50 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 w-full mb-0"></div> <!-- Header -->
                            <div class="p-4 space-y-4">
                                @for($i=0; $i<8; $i++)
                                <div class="flex gap-4 items-center">
                                    <div class="size-8 rounded bg-gray-200 dark:bg-gray-800 shrink-0"></div>
                                    <div class="h-4 bg-gray-100 dark:bg-gray-800/50 rounded w-1/4"></div>
                                    <div class="h-4 bg-gray-100 dark:bg-gray-800/50 rounded w-1/4"></div>
                                    <div class="h-4 bg-gray-100 dark:bg-gray-800/50 rounded w-1/6"></div>
                                    <div class="h-4 bg-gray-100 dark:bg-gray-800/50 rounded w-1/12 ml-auto"></div>
                                </div>
                                @endfor
                            </div>
                        </div>
                    </div>

                @elseif($skeletonType == 'form')
                    <!-- Form Skeleton -->
                    <div class="animate-pulse max-w-3xl mx-auto space-y-6">
                        <div class="bg-white dark:bg-[#1e232f] p-8 rounded-xl border border-gray-200 dark:border-gray-800 space-y-8">
                            <div class="space-y-4">
                                <div class="grid grid-cols-2 gap-6">
                                    <div class="space-y-2"><div class="h-4 w-24 bg-gray-200 dark:bg-gray-800 rounded"></div><div class="h-10 w-full bg-gray-100 dark:bg-gray-800/50 rounded-lg"></div></div>
                                    <div class="space-y-2"><div class="h-4 w-24 bg-gray-200 dark:bg-gray-800 rounded"></div><div class="h-10 w-full bg-gray-100 dark:bg-gray-800/50 rounded-lg"></div></div>
                                </div>
                                <div class="grid grid-cols-3 gap-6">
                                    <div class="space-y-2"><div class="h-4 w-24 bg-gray-200 dark:bg-gray-800 rounded"></div><div class="h-10 w-full bg-gray-100 dark:bg-gray-800/50 rounded-lg"></div></div>
                                    <div class="space-y-2"><div class="h-4 w-24 bg-gray-200 dark:bg-gray-800 rounded"></div><div class="h-10 w-full bg-gray-100 dark:bg-gray-800/50 rounded-lg"></div></div>
                                    <div class="space-y-2"><div class="h-4 w-24 bg-gray-200 dark:bg-gray-800 rounded"></div><div class="h-10 w-full bg-gray-100 dark:bg-gray-800/50 rounded-lg"></div></div>
                                </div>
                                <div class="space-y-2"><div class="h-4 w-32 bg-gray-200 dark:bg-gray-800 rounded"></div><div class="h-24 w-full bg-gray-100 dark:bg-gray-800/50 rounded-lg"></div></div>
                            </div>
                            <div class="flex justify-end gap-3">
                                <div class="h-10 w-24 bg-gray-200 dark:bg-gray-800 rounded-lg"></div>
                                <div class="h-10 w-32 bg-gray-300 dark:bg-gray-700 rounded-lg"></div>
                            </div>
                        </div>
                    </div>
                @endif

            </div>
        </div>
    </div>
    
    <!-- Global Custom Modal -->
    <div id="global-modal" class="fixed inset-0 z-[100] hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-gray-900/50 transition-opacity" aria-hidden="true"></div>

        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                <!-- Modal Panel -->
                <div class="relative transform overflow-hidden rounded-lg bg-white dark:bg-[#1e232f] text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                    <div class="px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div id="modal-icon" class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-red-100 dark:bg-red-900/30 sm:mx-0 sm:h-10 sm:w-10">
                                <!-- Icon injected via JS -->
                            </div>
                            <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left w-full">
                                <h3 class="text-lg font-bold leading-6 text-gray-900 dark:text-white" id="modal-title">Title</h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500 dark:text-gray-400" id="modal-message">Message</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-800/50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 gap-3">
                        <button type="button" id="modal-confirm-btn" class="inline-flex w-full justify-center rounded-lg bg-primary px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary/90 sm:w-auto">Confirm</button>
                        <button type="button" id="modal-cancel-btn" class="mt-3 inline-flex w-full justify-center rounded-lg bg-white dark:bg-transparent px-3 py-2 text-sm font-semibold text-gray-900 dark:text-gray-300 shadow-sm ring-1 ring-inset ring-gray-300 dark:ring-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 sm:mt-0 sm:w-auto">Cancel</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Modal Logic
        window.modal = {
            el: document.getElementById('global-modal'),
            title: document.getElementById('modal-title'),
            message: document.getElementById('modal-message'),
            icon: document.getElementById('modal-icon'),
            confirmBtn: document.getElementById('modal-confirm-btn'),
            cancelBtn: document.getElementById('modal-cancel-btn'),
            resolve: null,
            onConfirmCallback: null,

            show(options) {
                this.onConfirmCallback = options.onConfirm; 
                
                return new Promise((resolve) => {
                    this.resolve = resolve;
                    this.title.innerText = options.title || 'Alert';
                    this.message.innerText = options.message || '';
                    
                    let iconHtml = '';
                    let btnClass = '';
                    let iconBgClass = '';
                    
                    if(options.type === 'danger') {
                        iconHtml = '<span class="material-symbols-outlined text-red-600 dark:text-red-400">warning</span>';
                        iconBgClass = "mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-red-100 dark:bg-red-900/30 sm:mx-0 sm:h-10 sm:w-10";
                        btnClass = "inline-flex w-full justify-center rounded-lg bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500 sm:w-auto";
                    } else if (options.type === 'success') {
                         iconHtml = '<span class="material-symbols-outlined text-green-600 dark:text-green-400">check_circle</span>';
                        iconBgClass = "mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-green-100 dark:bg-green-900/30 sm:mx-0 sm:h-10 sm:w-10";
                        btnClass = "inline-flex w-full justify-center rounded-lg bg-green-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-green-500 sm:w-auto";
                    } else {
                        iconHtml = '<span class="material-symbols-outlined text-primary">info</span>';
                        iconBgClass = "mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-primary/10 sm:mx-0 sm:h-10 sm:w-10";
                        btnClass = "inline-flex w-full justify-center rounded-lg bg-primary px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary/90 sm:w-auto";
                    }
                    
                    this.icon.innerHTML = iconHtml;
                    this.icon.className = iconBgClass;
                    this.confirmBtn.className = btnClass;

                    this.confirmBtn.innerText = options.confirmText || 'OK';
                    this.cancelBtn.innerText = options.cancelText || 'Cancel';
                    
                    this.cancelBtn.style.display = options.hideCancel ? 'none' : 'inline-flex';
                    this.el.classList.remove('hidden');
                });
            },

            confirm() {
                if (this.onConfirmCallback) this.onConfirmCallback();
                this.hide(true);
            },

            hide(result) {
                this.el.classList.add('hidden');
                if(this.resolve) {
                    this.resolve = null;
                }
            }
        };

        window.modal.confirmBtn.onclick = () => window.modal.confirm();
        window.modal.cancelBtn.onclick = () => window.modal.hide(false);

        window.showAlert = (title, message) => window.modal.show({ title, message, hideCancel: true, confirmText: 'OK', type: 'info' });
        window.showConfirm = (title, message, confirmText = 'Yes, Proceed', cancelText = 'Cancel', type = 'danger') => window.modal.show({ title, message, type, confirmText, cancelText });

        window.addEventListener('load', function() {
            const skeleton = document.getElementById('app-skeleton');
            if(skeleton) {
                // Add fade out effect
                skeleton.style.opacity = '0';
                skeleton.style.pointerEvents = 'none';
                setTimeout(() => {
                    skeleton.remove();
                }, 300);
            }
        });
    </script>

    <!-- Mobile Menu Backdrop -->
    <div x-show="sidebarOpen" @click="sidebarOpen = false" x-transition.opacity class="fixed inset-0 z-40 bg-gray-900/50 backdrop-blur-sm md:hidden" style="display: none;"></div>

    <div class="flex h-screen overflow-hidden relative z-10">
        <!-- Sidebar Navigation -->
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'" class="fixed inset-y-0 left-0 z-50 w-64 border-r border-gray-200 dark:border-gray-800 bg-white dark:bg-[#111318] flex flex-col justify-between p-4 shrink-0 transition-transform duration-300 md:relative md:translate-x-0 transform">
            <div class="flex flex-col gap-8">
                <div class="flex items-center justify-between">
                    <div class="flex gap-3 items-center">
                        @if($businessLogo)
                            <img src="{{ route('files.display', ['path' => $businessLogo]) }}" class="size-10 rounded-lg object-cover bg-white shrink-0 border border-gray-100 dark:border-gray-700 shadow-sm">
                        @else
                            <div class="bg-gradient-to-br from-primary to-blue-600 rounded-lg size-10 flex items-center justify-center text-white shrink-0 shadow-lg shadow-primary/30">
                                <span class="material-symbols-outlined">inventory_2</span>
                            </div>
                        @endif
                        <div class="flex flex-col min-w-0">
                            <h1 class="text-base font-bold leading-none truncate">{{ $businessName }}</h1>
                            <p class="text-[#616f89] dark:text-gray-400 text-xs mt-1">Admin Console</p>
                        </div>
                    </div>
                    <button @click="sidebarOpen = false" class="md:hidden text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-white">
                        <span class="material-symbols-outlined">close</span>
                    </button>
                </div>

                <nav class="flex flex-col gap-1 relative z-50">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg {{ request()->routeIs('dashboard') ? 'bg-primary text-white shadow-sm' : 'text-[#616f89] dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-primary' }} transition-colors cursor-pointer group">
                        <span class="material-symbols-outlined">dashboard</span>
                        <span class="text-sm font-medium">Dashboard</span>
                    </a>
                    
                    <a href="{{ route('customers.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg {{ request()->routeIs('customers.*') ? 'bg-primary text-white shadow-sm' : 'text-[#616f89] dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-primary' }} transition-colors cursor-pointer group">
                        <span class="material-symbols-outlined">group</span>
                        <span class="text-sm font-medium">Customers</span>
                    </a>

                    <a href="{{ route('products.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg {{ request()->routeIs('products.*') ? 'bg-primary text-white shadow-sm' : 'text-[#616f89] dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-primary' }} transition-colors cursor-pointer group">
                        <span class="material-symbols-outlined">package_2</span>
                        <span class="text-sm font-medium">Inventory</span>
                    </a>

                    <a href="{{ route('sales.create') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg {{ request()->routeIs('sales.create') ? 'bg-primary text-white shadow-sm' : 'text-[#616f89] dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-primary' }} transition-colors cursor-pointer group">
                        <span class="material-symbols-outlined">point_of_sale</span>
                        <span class="text-sm font-medium">New Sale (POS)</span>
                    </a>
                    
                    <a href="{{ route('invoices.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg {{ request()->routeIs('invoices.*') ? 'bg-primary text-white shadow-sm' : 'text-[#616f89] dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-primary' }} transition-colors cursor-pointer group">
                        <span class="material-symbols-outlined">description</span>
                        <span class="text-sm font-medium">Invoices</span>
                    </a>

                    <a href="{{ route('sales.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg {{ request()->routeIs('sales.index') ? 'bg-primary text-white shadow-sm' : 'text-[#616f89] dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-primary' }} transition-colors cursor-pointer group">
                        <span class="material-symbols-outlined">receipt_long</span>
                        <span class="text-sm font-medium">Sales History</span>
                    </a>
                    
                    <a href="{{ route('reports.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg {{ request()->routeIs('reports.index') ? 'bg-primary text-white shadow-sm' : 'text-[#616f89] dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-primary' }} transition-colors cursor-pointer group">
                        <span class="material-symbols-outlined">bar_chart</span>
                        <span class="text-sm font-medium">Reports</span>
                    </a>
                    
                    <a href="{{ route('settings.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg {{ request()->routeIs('settings.index') ? 'bg-primary text-white shadow-sm' : 'text-[#616f89] dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-primary' }} transition-colors cursor-pointer group">
                        <span class="material-symbols-outlined">settings</span>
                        <span class="text-sm font-medium">Settings</span>
                    </a>
                </nav>
            </div>
            
            <div class="border-t border-gray-200 dark:border-gray-700 pt-4 relative z-50">
                <div class="flex items-center justify-between gap-2 px-2">
                    <div class="flex items-center gap-3 overflow-hidden">
                        <img src="{{ Auth::user()->profile_photo_url }}" class="size-8 rounded-full bg-gray-200 object-cover shrink-0 ring-2 ring-white dark:ring-gray-700">
                        <div class="flex-1 overflow-hidden">
                            <p class="text-sm font-semibold truncate text-gray-700 dark:text-gray-200">{{ Auth::user()->name ?? 'Admin User' }}</p>
                            <p class="text-[10px] text-[#616f89] dark:text-gray-500 truncate">{{ Auth::user()->email ?? 'admin@blotanna.com' }}</p>
                        </div>
                    </div>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="text-gray-400 hover:text-red-500 transition-colors cursor-pointer relative z-10" title="Sign Out">
                            <span class="material-symbols-outlined text-[20px]">logout</span>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- Main Content Wrapper -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
            <!-- Mobile Header -->
            <div class="md:hidden h-16 bg-white dark:bg-[#111318] border-b border-gray-200 dark:border-gray-800 flex items-center px-4 justify-between shrink-0 transition-colors z-20">
                <div class="flex items-center gap-3">
                    <button @click="sidebarOpen = true" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 p-1 rounded-md hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                        <span class="material-symbols-outlined text-2xl">menu</span>
                    </button>
                    @if($businessLogo)
                         <img src="{{ route('files.display', ['path' => $businessLogo]) }}" class="size-8 rounded-lg object-cover bg-white shrink-0 border border-gray-100 dark:border-gray-700 shadow-sm">
                    @else
                        <div class="bg-gradient-to-br from-primary to-blue-600 rounded-lg size-8 flex items-center justify-center text-white shrink-0 shadow-lg shadow-primary/30">
                            <span class="material-symbols-outlined text-sm">inventory_2</span>
                        </div>
                    @endif
                    <span class="font-bold text-lg text-gray-900 dark:text-white truncate max-w-[200px]">{{ $businessName }}</span>
                </div>
                <div class="flex items-center gap-2">
                     <img src="{{ Auth::user()->profile_photo_url }}" class="size-8 rounded-full bg-gray-200 object-cover shrink-0">
                </div>
            </div>

            <!-- Main Page Content -->
            <main class="flex-1 flex flex-col min-w-0 overflow-hidden">
                <!-- Global Flash Messages (Floating) -->
                @if(session('success'))
                    <div class="absolute top-4 right-4 z-50">
                        <div class="bg-green-600 text-white px-6 py-3 rounded-lg shadow-xl flex items-center gap-3 border border-green-500">
                            <span class="material-symbols-outlined text-xl">check_circle</span>
                            <p class="font-medium text-sm">{{ session('success') }}</p>
                            <button onclick="this.parentElement.parentElement.remove()" class="ml-2 hover:bg-white/20 rounded-full p-1 transition-colors">
                                <span class="material-symbols-outlined text-lg">close</span>
                            </button>
                        </div>
                    </div>
                @endif
                @if(session('error'))
                    <div class="absolute top-4 right-4 z-50">
                        <div class="bg-red-600 text-white px-6 py-3 rounded-lg shadow-xl flex items-center gap-3 border border-red-500">
                            <span class="material-symbols-outlined text-xl">error</span>
                            <p class="font-medium text-sm">{{ session('error') }}</p>
                            <button onclick="this.parentElement.parentElement.remove()" class="ml-2 hover:bg-white/20 rounded-full p-1 transition-colors">
                                <span class="material-symbols-outlined text-lg">close</span>
                            </button>
                        </div>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>
    
    <form id="auto-logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">@csrf</form>

    @stack('scripts')
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Smooth scroll sidebar logic
            document.querySelectorAll('aside nav a').forEach(link => {
                link.addEventListener('click', function(e) {
                    if (this.href === window.location.href || this.href.split('?')[0] === window.location.href.split('?')[0]) {
                        e.preventDefault();
                    }
                });
            });

            @auth
            const SESSION_KEY = 'app_active_session';
            const INACTIVITY_LIMIT = 15 * 60 * 1000; 
            let inactivityTimer;

            if (!sessionStorage.getItem(SESSION_KEY)) {
                const form = document.getElementById('auto-logout-form');
                if(form) form.submit();
                return; 
            }

            function resetInactivityTimer() {
                if(inactivityTimer) clearTimeout(inactivityTimer);
                inactivityTimer = setTimeout(() => {
                    const form = document.getElementById('auto-logout-form');
                    if(form) form.submit();
                }, INACTIVITY_LIMIT);
            }

            ['mousemove', 'keydown', 'touchmove', 'scroll', 'click'].forEach(evt => {
                document.addEventListener(evt, resetInactivityTimer, false);
            });

            resetInactivityTimer();
            @else
            sessionStorage.removeItem('app_active_session');
            @endauth
        });
    </script>
</body>
</html>