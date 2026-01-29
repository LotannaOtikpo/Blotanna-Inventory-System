
@extends('layouts.app')

@section('content')
<!-- Header -->
<header class="bg-white dark:bg-[#111318] border-b border-[#dbdfe6] dark:border-gray-800 px-4 md:px-8 py-4 flex flex-col md:flex-row md:items-center justify-between gap-4 sticky top-0 z-10 transition-colors">
    <h2 class="text-lg font-bold tracking-tight text-[#111318] dark:text-white">Business Dashboard Overview</h2>
    <div class="flex items-center gap-4 self-end md:self-auto">
        <!-- Notifications Dropdown -->
        <div class="relative" x-data="{ open: false }">
            <button @click="open = !open" class="size-10 flex items-center justify-center rounded-lg bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors relative focus:ring-2 focus:ring-primary/20">
                <span class="material-symbols-outlined text-gray-600 dark:text-gray-300">notifications</span>
                @if($lowStockItems > 0)
                <span class="absolute top-2 right-2 size-2.5 bg-red-500 rounded-full border border-white dark:border-gray-800"></span>
                @endif
            </button>
            
            <!-- Dropdown Content -->
            <div x-show="open" @click.away="open = false" style="display: none;" class="absolute right-0 top-12 w-80 bg-white dark:bg-[#1e232f] rounded-xl shadow-2xl border border-gray-100 dark:border-gray-700 z-50 overflow-hidden ring-1 ring-black/5">
                <div class="p-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50 flex justify-between items-center">
                    <h3 class="font-bold text-sm text-gray-900 dark:text-white">Notifications</h3>
                    <span class="text-xs bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 px-2 py-0.5 rounded-full font-bold shadow-sm">{{ $lowStockItems }} New</span>
                </div>
                <div class="max-h-64 overflow-y-auto">
                    @forelse($lowStockProducts as $product)
                    <a href="{{ route('products.edit', $product) }}" class="flex items-start gap-3 p-3 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors border-b border-gray-50 dark:border-gray-700 last:border-0 group">
                        <div class="bg-red-50 dark:bg-red-900/20 text-red-500 rounded-lg p-2 shrink-0">
                            <span class="material-symbols-outlined text-lg">inventory_2</span>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-800 dark:text-gray-200">{{ $product->name }}</p>
                            <p class="text-xs text-red-600 dark:text-red-400 font-medium">Low stock: Only {{ $product->quantity }} left</p>
                        </div>
                    </a>
                    @empty
                    <div class="p-8 text-center text-gray-400 dark:text-gray-500">
                        <span class="material-symbols-outlined text-3xl mb-2 opacity-50">notifications_off</span>
                        <p class="text-sm">No new notifications</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        <a href="{{ route('sales.create') }}" class="flex items-center gap-2 bg-primary text-white px-4 py-2 rounded-lg text-sm font-semibold shadow-lg shadow-primary/30 hover:bg-primary/90 hover:-translate-y-0.5 transition-all active:scale-95 focus:ring-4 focus:ring-primary/20">
            <span class="material-symbols-outlined text-[18px]">add</span>
            <span>New Sale</span>
        </a>
    </div>
</header>

<div class="p-4 md:p-8 overflow-y-auto">
    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4 md:gap-6 mb-8">
        <!-- Total Products -->
        <div class="bg-white dark:bg-[#1e232f] p-6 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm flex flex-col gap-2 transition-all">
            <div class="flex justify-between items-start">
                <p class="text-[#616f89] dark:text-gray-400 text-sm font-medium">Total Products</p>
                <span class="material-symbols-outlined text-primary/60">inventory</span>
            </div>
            <p class="text-[#111318] dark:text-white text-3xl font-bold truncate" title="{{ number_format($totalProducts) }}">{{ number_format($totalProducts) }}</p>
            <div class="flex items-center gap-1 mt-1">
                <span class="material-symbols-outlined text-[#07883b] text-sm">trending_up</span>
                <p class="text-[#07883b] text-xs font-bold">Active</p>
                <span class="text-[#616f89] dark:text-gray-400 text-xs ml-1">in inventory</span>
            </div>
        </div>

        <!-- Monthly Sales -->
        <div class="bg-white dark:bg-[#1e232f] p-6 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm flex flex-col gap-2 transition-all">
            <div class="flex justify-between items-start">
                <p class="text-[#616f89] dark:text-gray-400 text-sm font-medium">Total Sales (Monthly)</p>
                <span class="material-symbols-outlined text-primary/60">account_balance_wallet</span>
            </div>
            <p class="text-[#111318] dark:text-white text-3xl font-bold truncate" title="{{ \App\Models\Setting::where('key', 'currency_symbol')->value('value') ?? '$' }}{{ number_format($monthlySales, 2) }}">
                {{ \App\Models\Setting::where('key', 'currency_symbol')->value('value') ?? '$' }}{{ number_format($monthlySales, 2) }}
            </p>
            <div class="flex items-center gap-1 mt-1">
                <span class="material-symbols-outlined text-[#07883b] text-sm">calendar_month</span>
                <p class="text-[#07883b] text-xs font-bold">Current Month</p>
            </div>
        </div>

        <!-- Today's Revenue -->
        <div class="bg-white dark:bg-[#1e232f] p-6 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm flex flex-col gap-2 transition-all">
            <div class="flex justify-between items-start">
                <p class="text-[#616f89] dark:text-gray-400 text-sm font-medium">Today's Revenue</p>
                <span class="material-symbols-outlined text-primary/60">today</span>
            </div>
            <p class="text-[#111318] dark:text-white text-3xl font-bold truncate" title="{{ \App\Models\Setting::where('key', 'currency_symbol')->value('value') ?? '$' }}{{ number_format($todaysRevenue, 2) }}">
                {{ \App\Models\Setting::where('key', 'currency_symbol')->value('value') ?? '$' }}{{ number_format($todaysRevenue, 2) }}
            </p>
             <div class="flex items-center gap-1 mt-1">
                <span class="material-symbols-outlined text-[#07883b] text-sm">schedule</span>
                <p class="text-[#07883b] text-xs font-bold">Updated Now</p>
            </div>
        </div>

        <!-- Low Stock -->
        <div class="bg-orange-50/60 dark:bg-orange-900/10 p-6 rounded-xl border border-orange-200 dark:border-orange-800 shadow-sm flex flex-col gap-2 transition-all hover:bg-orange-50 dark:hover:bg-orange-900/20">
            <div class="flex justify-between items-start">
                <p class="text-orange-700 dark:text-orange-400 text-sm font-medium">Low Stock Alerts</p>
                <span class="material-symbols-outlined text-orange-600 dark:text-orange-400">warning</span>
            </div>
            <p class="text-orange-900 dark:text-orange-200 text-3xl font-bold truncate" title="{{ $lowStockItems }} Items">{{ $lowStockItems }} Items</p>
            <div class="flex items-center gap-1 mt-1">
                <p class="text-orange-700 dark:text-orange-400 text-xs font-bold">Action required</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Dynamic Chart Section -->
        <div class="lg:col-span-2 bg-white dark:bg-[#1e232f] p-4 md:p-8 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm transition-all">
            <div class="flex items-center justify-between mb-2">
                <div>
                    <h3 class="text-[#111318] dark:text-white text-lg font-bold">Weekly Sales Trends</h3>
                    <p class="text-[#616f89] dark:text-gray-400 text-sm font-medium">Revenue performance for this week (Sun - Sat)</p>
                </div>
            </div>
            
            <div id="sales-chart" class="w-full h-[320px]"></div>
        </div>

        <!-- Top Selling -->
        <div class="bg-white dark:bg-[#1e232f] rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm flex flex-col h-full overflow-hidden transition-all">
            <div class="p-6 border-b border-[#f0f2f4] dark:border-gray-700 flex items-center justify-between">
                <h3 class="text-[#111318] dark:text-white text-base font-bold">Top Selling Products</h3>
            </div>
            <div class="flex-1 flex flex-col">
                @forelse($topProducts as $product)
                <div class="flex items-center gap-4 p-4 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors border-b border-[#f0f2f4] dark:border-gray-700 last:border-0 group cursor-pointer">
                    <div class="size-12 rounded-lg bg-gray-100 dark:bg-gray-700 flex items-center justify-center overflow-hidden shrink-0 shadow-sm">
                        @if($product->image_path)
                            <img src="{{ $product->image_url }}" class="w-full h-full object-cover">
                        @else
                            <span class="material-symbols-outlined text-gray-400">inventory_2</span>
                        @endif
                    </div>
                    <div class="flex-1 overflow-hidden min-w-0">
                        <p class="text-sm font-bold truncate text-gray-900 dark:text-white">{{ $product->name }}</p>
                        <p class="text-xs text-[#616f89] dark:text-gray-400">{{ $product->sale_items_count }} sales</p>
                    </div>
                    <div class="text-right shrink-0">
                        <p class="text-sm font-bold text-gray-900 dark:text-white">
                            {{ \App\Models\Setting::where('key', 'currency_symbol')->value('value') ?? '$' }}{{ number_format($product->price, 0) }}
                        </p>
                    </div>
                </div>
                @empty
                <div class="p-8 text-center text-gray-400">
                    <p>No sales data yet.</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
    
    <!-- Revenue Calendar -->
    <div class="mt-8 bg-white dark:bg-[#1e232f] rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden transition-all"
         x-data="revenueCalendar">
        
        <div class="p-6 border-b border-[#dbdfe6] dark:border-gray-800 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h3 class="text-lg font-bold text-[#111318] dark:text-white">Revenue Calendar</h3>
                <p class="text-sm text-[#616f89] dark:text-gray-400">Click on any date to view detailed transactions.</p>
            </div>
            
            <div class="flex items-center gap-4 bg-gray-50/50 dark:bg-gray-800/50 p-1 rounded-lg border border-gray-200 dark:border-gray-700">
                <button @click="prevMonth()" class="p-1 hover:bg-white dark:hover:bg-gray-700 rounded-md shadow-sm transition-all text-gray-500 dark:text-gray-400 hover:text-primary">
                    <span class="material-symbols-outlined">chevron_left</span>
                </button>
                <span class="text-sm font-bold min-w-[140px] text-center text-gray-900 dark:text-white" x-text="monthName + ' ' + currentYear"></span>
                <button @click="nextMonth()" class="p-1 hover:bg-white dark:hover:bg-gray-700 rounded-md shadow-sm transition-all text-gray-500 dark:text-gray-400 hover:text-primary">
                    <span class="material-symbols-outlined">chevron_right</span>
                </button>
            </div>
        </div>

        <div class="p-2 md:p-6">
            <div class="w-full">
                <!-- Calendar Grid Header -->
                <div class="grid grid-cols-8 gap-px bg-gray-200/50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-700 rounded-t-lg overflow-hidden">
                    <template x-for="day in ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat']">
                        <div class="bg-gray-50/80 dark:bg-gray-800/80 p-1 md:p-3 text-[10px] md:text-xs font-semibold uppercase text-gray-500 dark:text-gray-400 text-center flex items-center justify-center break-all" x-text="day"></div>
                    </template>
                    <div class="bg-primary/5 dark:bg-primary/20 p-1 md:p-3 text-[10px] md:text-xs font-bold uppercase text-primary text-center flex items-center justify-center break-all">Wkly</div>
                </div>

                <!-- Calendar Rows -->
                <div class="border-x border-b border-gray-200 dark:border-gray-700 rounded-b-lg overflow-hidden">
                    <template x-for="(week, index) in weeks" :key="index">
                        <div class="grid grid-cols-8 gap-px bg-gray-200/50 dark:bg-gray-700/50">
                            <!-- Days -->
                            <template x-for="day in week.days" :key="day.dateString">
                                <div class="bg-white dark:bg-[#1e232f] p-1 md:p-3 min-h-[60px] md:min-h-[100px] flex flex-col justify-between transition-all relative cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-800/80 group overflow-hidden"
                                     :class="{'bg-gray-50/50 dark:bg-gray-800/30': !day.isCurrentMonth, 'ring-1 ring-inset ring-primary/50 z-10 bg-primary/5': isToday(day.dateString)}"
                                     @click="fetchDayDetails(day.dateString)">
                                    
                                    <span class="text-[10px] md:text-xs font-medium z-10 relative" 
                                          :class="isToday(day.dateString) ? 'text-primary font-bold' : (day.isCurrentMonth ? 'text-gray-700 dark:text-gray-300' : 'text-gray-400 dark:text-gray-600')" 
                                          x-text="day.dayOfMonth"></span>
                                    
                                    <div x-show="day.revenue > 0" class="mt-1 md:mt-2 z-10 relative">
                                        <span class="hidden md:block text-[10px] text-gray-500 dark:text-gray-400 uppercase tracking-wide group-hover:text-primary transition-colors">Rev</span>
                                        <span class="block text-[10px] md:text-sm font-bold text-gray-900 dark:text-white truncate transition-all group-hover:scale-105 origin-left" x-text="formatCurrency(day.revenue)"></span>
                                    </div>
                                    
                                    <!-- Hover Indicator -->
                                    <div class="absolute inset-0 bg-primary/5 opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none"></div>
                                </div>
                            </template>
                            
                            <!-- Weekly Total Column -->
                            <div class="bg-primary/5 dark:bg-primary/10 p-1 md:p-3 min-h-[60px] md:min-h-[100px] flex flex-col justify-center items-center text-center overflow-hidden">
                                <span class="hidden md:block text-[10px] uppercase font-bold text-primary/60 mb-1">Total</span>
                                <span class="text-[10px] md:text-sm font-black text-primary truncate w-full" x-text="formatCurrency(week.total)"></span>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>
        
        <!-- Monthly Summary Footer -->
        <div class="bg-gray-50/50 dark:bg-gray-800/50 p-4 border-t border-[#dbdfe6] dark:border-gray-800 flex flex-col sm:flex-row justify-between items-center gap-4">
            <div class="text-xs text-gray-500 dark:text-gray-400 flex items-center gap-2">
                 <span class="size-2 rounded-full bg-primary animate-pulse"></span>
                 Current day highlighted
            </div>
            <div class="flex items-center gap-3">
                 <span class="text-sm font-medium text-gray-600 dark:text-gray-300">Total Revenue (<span x-text="monthName"></span>):</span>
                 <span class="text-xl font-black text-gray-900 dark:text-white" x-text="formatCurrency(monthlyTotal)"></span>
            </div>
        </div>

        <!-- Day Details Modal -->
        <div x-show="showDayModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
            <div x-show="showDayModal" x-transition.opacity class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm"></div>

            <div x-show="showDayModal" x-transition.scale @click.away="showDayModal = false" class="relative min-h-screen flex items-center justify-center p-4">
                <div class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-[#1e232f] text-left shadow-2xl transition-all w-full max-w-2xl border border-white/20 dark:border-gray-700">
                    
                    <!-- Header -->
                    <div class="bg-white/50 dark:bg-[#1e232f]/50 px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Transaction Analysis</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400" x-text="dayDetails ? dayDetails.date_formatted : 'Loading...'">Loading...</p>
                        </div>
                        <button @click="showDayModal = false" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 p-1 rounded-full transition-all">
                            <span class="material-symbols-outlined">close</span>
                        </button>
                    </div>

                    <!-- Content -->
                    <div class="p-6">
                        <template x-if="isLoadingDay">
                            <div class="flex flex-col items-center justify-center py-12">
                                <span class="material-symbols-outlined animate-spin text-3xl text-primary mb-2">progress_activity</span>
                                <p class="text-sm text-gray-500">Fetching transactions...</p>
                            </div>
                        </template>

                        <template x-if="!isLoadingDay && dayDetails">
                            <div class="space-y-6">
                                <!-- Summary Cards -->
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="p-4 bg-gray-50 dark:bg-gray-800/50 rounded-xl border border-gray-100 dark:border-gray-700">
                                        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase font-semibold">Total Revenue</p>
                                        <p class="text-xl font-bold text-primary" x-text="formatCurrency(dayDetails.total_revenue)"></p>
                                    </div>
                                    <div class="p-4 bg-gray-50 dark:bg-gray-800/50 rounded-xl border border-gray-100 dark:border-gray-700">
                                        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase font-semibold">Transactions</p>
                                        <p class="text-xl font-bold text-gray-900 dark:text-white" x-text="dayDetails.total_count"></p>
                                    </div>
                                </div>

                                <!-- Transactions List -->
                                <div>
                                    <h4 class="text-sm font-bold text-gray-900 dark:text-white mb-3">Transactions Log</h4>
                                    <div class="border rounded-xl border-gray-100 dark:border-gray-700 overflow-hidden shadow-sm">
                                        <table class="w-full text-sm text-left">
                                            <thead class="bg-gray-50 dark:bg-gray-800 text-gray-500 dark:text-gray-400">
                                                <tr>
                                                    <th class="px-4 py-2">Time</th>
                                                    <th class="px-4 py-2">Details</th>
                                                    <th class="px-4 py-2 text-right">Amount</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                                <template x-for="sale in dayDetails.sales" :key="sale.id">
                                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                                                        <td class="px-4 py-3 text-gray-500 dark:text-gray-400 font-mono text-xs" x-text="sale.time"></td>
                                                        <td class="px-4 py-3">
                                                            <a :href="'/sales/' + sale.id" class="font-bold text-gray-900 dark:text-white hover:text-primary block transition-colors" x-text="sale.transaction_id"></a>
                                                            <p class="text-xs text-gray-500 dark:text-gray-400" x-text="sale.customer + ' • ' + sale.payment_method"></p>
                                                        </td>
                                                        <td class="px-4 py-3 text-right font-medium text-gray-900 dark:text-white" x-text="formatCurrency(sale.amount)"></td>
                                                    </tr>
                                                </template>
                                                <template x-if="dayDetails.sales.length === 0">
                                                    <tr>
                                                        <td colspan="3" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                                            No transactions found for this date.
                                                        </td>
                                                    </tr>
                                                </template>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-800/50 px-6 py-4 flex justify-end">
                         <button @click="showDayModal = false" class="px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium hover:bg-gray-50 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 transition-colors shadow-sm hover:-translate-y-0.5 active:scale-95">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Initialize global data variable
    window.dashboardCalendarData = @json($calendarSales);
    window.dashboardCurrency = "{{ \App\Models\Setting::where('key', 'currency_symbol')->value('value') ?? '$' }}";

    document.addEventListener('DOMContentLoaded', function () {
        // Chart Logic with Animation config
        const rawData = @json($chartData);
        const labels = rawData.map(item => item.day);
        const data = rawData.map(item => item.amount);
        
        const isDark = document.documentElement.classList.contains('dark');

        const options = {
            series: [{
                name: 'Revenue',
                data: data
            }],
            chart: {
                type: 'bar',
                height: 320,
                toolbar: { show: false },
                fontFamily: 'Inter, sans-serif',
                background: 'transparent',
                animations: {
                    enabled: true,
                    easing: 'easeinout',
                    speed: 800,
                    animateGradually: {
                        enabled: true,
                        delay: 150
                    },
                    dynamicAnimation: {
                        enabled: true,
                        speed: 350
                    }
                }
            },
            colors: ['#135bec'],
            plotOptions: {
                bar: {
                    borderRadius: 6,
                    columnWidth: '45%',
                    distributed: false,
                }
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                show: true,
                width: 0,
                colors: ['transparent']
            },
            xaxis: {
                categories: labels,
                axisBorder: { show: false },
                axisTicks: { show: false },
                labels: {
                    style: {
                        colors: isDark ? '#9ca3af' : '#64748b',
                        fontSize: '12px',
                        fontWeight: 500
                    }
                }
            },
            yaxis: {
                labels: {
                    style: {
                        colors: isDark ? '#9ca3af' : '#64748b',
                        fontSize: '12px',
                        fontWeight: 500
                    },
                    formatter: function (value) {
                        return window.dashboardCurrency + value.toFixed(0); 
                    }
                }
            },
            grid: {
                borderColor: isDark ? '#374151' : '#e2e8f0',
                strokeDashArray: 4,
                yaxis: {
                    lines: {
                        show: true
                    }
                },
                xaxis: {
                    lines: {
                        show: false
                    }
                },
                padding: {
                    top: 0,
                    right: 0,
                    bottom: 0,
                    left: 10
                }
            },
            tooltip: {
                theme: isDark ? 'dark' : 'light',
                y: {
                    formatter: function (val) {
                        return window.dashboardCurrency + val.toFixed(2)
                    }
                },
                style: {
                    fontSize: '12px',
                    fontFamily: 'Inter, sans-serif',
                },
                marker: {
                    show: false,
                },
            },
            theme: {
                mode: isDark ? 'dark' : 'light'
            }
        };

        const chart = new ApexCharts(document.querySelector("#sales-chart"), options);
        chart.render();

        // Observer for Dark Mode changes
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.attributeName === "class") {
                    const isDarkMode = document.documentElement.classList.contains('dark');
                    chart.updateOptions({
                        theme: { mode: isDarkMode ? 'dark' : 'light' },
                        grid: { borderColor: isDarkMode ? '#374151' : '#e2e8f0' },
                        xaxis: { labels: { style: { colors: isDarkMode ? '#9ca3af' : '#64748b' } } },
                        yaxis: { labels: { style: { colors: isDarkMode ? '#9ca3af' : '#64748b' } } },
                        tooltip: { theme: isDarkMode ? 'dark' : 'light' }
                    });
                }
            });
        });
        
        observer.observe(document.documentElement, { attributes: true });
    });

    // Calendar Component Logic
    document.addEventListener('alpine:init', () => {
        Alpine.data('revenueCalendar', () => ({
            sales: window.dashboardCalendarData || {},
            currentYear: new Date().getFullYear(),
            currentMonth: new Date().getMonth(),
            currency: window.dashboardCurrency,
            showDayModal: false,
            dayDetails: null,
            isLoadingDay: false,

            get monthName() {
                return new Date(this.currentYear, this.currentMonth).toLocaleString('default', { month: 'long' });
            },

            prevMonth() {
                if (this.currentMonth === 0) {
                    this.currentMonth = 11;
                    this.currentYear--;
                } else {
                    this.currentMonth--;
                }
            },

            nextMonth() {
                if (this.currentMonth === 11) {
                    this.currentMonth = 0;
                    this.currentYear++;
                } else {
                    this.currentMonth++;
                }
            },

            isToday(dateString) {
                const today = new Date();
                const pad = (n) => String(n).padStart(2, '0');
                const todayString = `${today.getFullYear()}-${pad(today.getMonth()+1)}-${pad(today.getDate())}`;
                return dateString === todayString;
            },

            formatCurrency(amount) {
                return this.currency + new Intl.NumberFormat('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(amount);
            },
            
            async fetchDayDetails(date) {
                this.showDayModal = true;
                this.isLoadingDay = true;
                this.dayDetails = null;
                
                try {
                    const response = await fetch(`{{ route('dashboard.sales-by-date') }}?date=${date}`);
                    if (!response.ok) throw new Error('Failed to fetch data');
                    this.dayDetails = await response.json();
                } catch (error) {
                    console.error(error);
                    window.showAlert('Error', 'Could not load sales data.');
                    this.showDayModal = false;
                } finally {
                    this.isLoadingDay = false;
                }
            },

            get weeks() {
                const firstDay = new Date(this.currentYear, this.currentMonth, 1);
                const lastDay = new Date(this.currentYear, this.currentMonth + 1, 0);
                const startDayOfWeek = firstDay.getDay(); 
                
                let weeks = [];
                let currentWeek = { days: [], total: 0 };
                
                // Previous Month Padding
                if (startDayOfWeek > 0) {
                    const prevMonthLastDay = new Date(this.currentYear, this.currentMonth, 0).getDate();
                    for (let i = startDayOfWeek - 1; i >= 0; i--) {
                        const d = prevMonthLastDay - i;
                        const date = new Date(this.currentYear, this.currentMonth - 1, d);
                        const pad = (n) => String(n).padStart(2, '0');
                        const dateString = `${date.getFullYear()}-${pad(date.getMonth()+1)}-${pad(date.getDate())}`;
                        const revenue = parseFloat(this.sales[dateString]) || 0;
                        
                        currentWeek.days.push({
                            dayOfMonth: d,
                            dateString: dateString,
                            isCurrentMonth: false,
                            revenue: revenue
                        });
                        currentWeek.total += revenue;
                    }
                }

                // Current Month Days
                for (let d = 1; d <= lastDay.getDate(); d++) {
                    if (currentWeek.days.length === 7) {
                        weeks.push(currentWeek);
                        currentWeek = { days: [], total: 0 };
                    }
                    
                    const pad = (n) => String(n).padStart(2, '0');
                    const dateString = `${this.currentYear}-${pad(this.currentMonth+1)}-${pad(d)}`;
                    const revenue = parseFloat(this.sales[dateString]) || 0;
                    
                    currentWeek.days.push({
                        dayOfMonth: d,
                        dateString: dateString,
                        isCurrentMonth: true,
                        revenue: revenue
                    });
                    currentWeek.total += revenue;
                }

                // Next Month Padding
                let nextMonthDay = 1;
                while (currentWeek.days.length < 7) {
                    const date = new Date(this.currentYear, this.currentMonth + 1, nextMonthDay);
                    const pad = (n) => String(n).padStart(2, '0');
                    const dateString = `${date.getFullYear()}-${pad(date.getMonth()+1)}-${pad(date.getDate())}`;
                    const revenue = parseFloat(this.sales[dateString]) || 0;
                    
                    currentWeek.days.push({
                        dayOfMonth: nextMonthDay,
                        dateString: dateString,
                        isCurrentMonth: false,
                        revenue: revenue
                    });
                    currentWeek.total += revenue;
                    nextMonthDay++;
                }
                weeks.push(currentWeek);

                return weeks;
            },
            
            get monthlyTotal() {
                let total = 0;
                const lastDay = new Date(this.currentYear, this.currentMonth + 1, 0).getDate();
                for (let d = 1; d <= lastDay; d++) {
                    const pad = (n) => String(n).padStart(2, '0');
                    const dateString = `${this.currentYear}-${pad(this.currentMonth+1)}-${pad(d)}`;
                    total += (parseFloat(this.sales[dateString]) || 0);
                }
                return total;
            }
        }));
    });
</script>
@endpush
@endsection
