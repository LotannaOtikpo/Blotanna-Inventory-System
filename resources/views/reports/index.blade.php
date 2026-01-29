
@extends('layouts.app')

@section('content')
<header class="bg-white dark:bg-[#111318] border-b border-[#dbdfe6] dark:border-gray-800 px-4 md:px-8 py-6 sticky top-0 z-10 transition-colors">
    <div>
        <h2 class="text-2xl md:text-3xl font-black tracking-tight text-[#111318] dark:text-white">Reports & Analytics</h2>
        <p class="text-sm text-[#616f89] dark:text-gray-400 mt-1">Detailed insights into your business performance.</p>
    </div>
</header>

<div class="flex-1 overflow-y-auto p-4 md:p-8 space-y-6 md:space-y-8">
    <!-- Key Metrics -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4 md:gap-6">
        <div class="bg-white dark:bg-[#1e232f] p-6 rounded-xl border border-[#dbdfe6] dark:border-gray-800 shadow-sm transition-colors overflow-hidden">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Total Revenue</p>
            <h3 class="text-3xl font-bold text-gray-900 dark:text-white truncate" title="{{ \App\Models\Setting::where('key', 'currency_symbol')->value('value') ?? '$' }}{{ number_format($totalRevenue, 2) }}">
                {{ \App\Models\Setting::where('key', 'currency_symbol')->value('value') ?? '$' }}{{ number_format($totalRevenue, 2) }}
            </h3>
        </div>
        <div class="bg-white dark:bg-[#1e232f] p-6 rounded-xl border border-[#dbdfe6] dark:border-gray-800 shadow-sm transition-colors overflow-hidden">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Total Orders</p>
            <h3 class="text-3xl font-bold text-gray-900 dark:text-white truncate" title="{{ number_format($totalOrders) }}">
                {{ number_format($totalOrders) }}
            </h3>
        </div>
        <div class="bg-white dark:bg-[#1e232f] p-6 rounded-xl border border-[#dbdfe6] dark:border-gray-800 shadow-sm transition-colors overflow-hidden">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Average Order Value</p>
            <h3 class="text-3xl font-bold text-gray-900 dark:text-white truncate" title="{{ \App\Models\Setting::where('key', 'currency_symbol')->value('value') ?? '$' }}{{ number_format($averageOrderValue, 2) }}">
                {{ \App\Models\Setting::where('key', 'currency_symbol')->value('value') ?? '$' }}{{ number_format($averageOrderValue, 2) }}
            </h3>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 md:gap-8">
        <!-- Daily Sales (Table representation of chart) -->
        <div class="bg-white dark:bg-[#1e232f] rounded-xl border border-[#dbdfe6] dark:border-gray-800 shadow-sm p-6 transition-colors">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Last 30 Days Sales</h3>
            <div class="overflow-y-auto max-h-80">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-800 sticky top-0">
                        <tr>
                            <th class="px-4 py-2 text-left text-gray-500 dark:text-gray-400">Date</th>
                            <th class="px-4 py-2 text-right text-gray-500 dark:text-gray-400">Revenue</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @foreach($dailySales as $day)
                        <tr>
                            <td class="px-4 py-3 text-gray-700 dark:text-gray-300">{{ Carbon\Carbon::parse($day->date)->format('M d, Y') }}</td>
                            <td class="px-4 py-3 text-right font-medium text-gray-900 dark:text-white">{{ \App\Models\Setting::where('key', 'currency_symbol')->value('value') ?? '$' }}{{ number_format($day->total, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Top Categories -->
        <div class="bg-white dark:bg-[#1e232f] rounded-xl border border-[#dbdfe6] dark:border-gray-800 shadow-sm p-6 transition-colors">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Top Performing Categories</h3>
            <div class="space-y-4">
                @foreach($topCategories as $cat)
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="size-8 rounded-lg bg-primary/10 flex items-center justify-center text-primary font-bold shrink-0">
                            {{ substr($cat->name, 0, 1) }}
                        </div>
                        <span class="font-medium text-gray-700 dark:text-gray-300">{{ $cat->name }}</span>
                    </div>
                    <span class="text-sm font-bold text-gray-900 dark:text-white">{{ $cat->total_sales }} Sales</span>
                </div>
                <!-- Simple bar visual -->
                <div class="w-full bg-gray-100 dark:bg-gray-700 rounded-full h-1.5">
                    <div class="bg-primary h-1.5 rounded-full" style="width: {{ min(100, ($cat->total_sales / max(1, $topCategories->first()->total_sales)) * 100) }}%"></div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Low Stock Alert Report -->
    <div class="bg-white dark:bg-[#1e232f] rounded-xl border border-[#dbdfe6] dark:border-gray-800 shadow-sm overflow-hidden transition-colors">
        <div class="p-6 border-b border-[#dbdfe6] dark:border-gray-700 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="material-symbols-outlined text-red-500">warning</span>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Low Stock Alerts</h3>
            </div>
            <span class="bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400 px-3 py-1 rounded-full text-xs font-bold">{{ $lowStockProducts->count() }} Items</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700">
                        <th class="px-6 py-3 font-semibold text-gray-500 dark:text-gray-400">Product</th>
                        <th class="px-6 py-3 font-semibold text-gray-500 dark:text-gray-400">SKU</th>
                        <th class="px-6 py-3 font-semibold text-gray-500 dark:text-gray-400">Current Qty</th>
                        <th class="px-6 py-3 font-semibold text-gray-500 dark:text-gray-400 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($lowStockProducts as $product)
                    <tr>
                        <td class="px-6 py-3 font-medium text-gray-900 dark:text-white">{{ $product->name }}</td>
                        <td class="px-6 py-3 text-gray-600 dark:text-gray-300 font-mono">{{ $product->sku }}</td>
                        <td class="px-6 py-3 text-red-600 dark:text-red-400 font-bold">{{ $product->quantity }}</td>
                        <td class="px-6 py-3 text-right">
                            <a href="{{ route('products.edit', $product) }}" class="text-primary hover:underline">Restock</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">All stock levels are healthy.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- System Activity Log (New) -->
    <div class="bg-white dark:bg-[#1e232f] rounded-xl border border-[#dbdfe6] dark:border-gray-800 shadow-sm overflow-hidden transition-colors" id="activity-log">
        <div class="p-6 border-b border-[#dbdfe6] dark:border-gray-700 flex items-center gap-2">
            <span class="material-symbols-outlined text-primary">history</span>
            <h3 class="text-lg font-bold text-gray-900 dark:text-white">System Activity Log</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm whitespace-nowrap">
                <thead>
                    <tr class="bg-gray-50 dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700">
                        <th class="px-6 py-3 font-semibold text-gray-500 dark:text-gray-400">Date/Time</th>
                        <th class="px-6 py-3 font-semibold text-gray-500 dark:text-gray-400">User</th>
                        <th class="px-6 py-3 font-semibold text-gray-500 dark:text-gray-400">Action</th>
                        <th class="px-6 py-3 font-semibold text-gray-500 dark:text-gray-400">Description</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($activityLogs as $log)
                    <tr>
                        <td class="px-6 py-3 text-gray-600 dark:text-gray-300">
                            {{ $log->created_at->format('M d, Y H:i:s') }}
                        </td>
                        <td class="px-6 py-3">
                            <div class="flex items-center gap-2">
                                @if($log->user)
                                <img src="{{ $log->user->profile_photo_url }}" class="size-6 rounded-full bg-gray-200">
                                <span class="font-medium text-gray-900 dark:text-white">{{ $log->user->name }}</span>
                                @else
                                <span class="text-gray-400 italic">System/Deleted</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-3">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold {{ $log->action_color }}">
                                {{ $log->action }}
                            </span>
                        </td>
                        <td class="px-6 py-3 text-gray-600 dark:text-gray-300 max-w-md truncate" title="{{ $log->description }}">
                            {{ $log->description }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">No activity recorded yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700">
            {{ $activityLogs->links() }}
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const logContainer = document.getElementById('activity-log');
        
        if (logContainer) {
            logContainer.addEventListener('click', function(e) {
                // Find closest anchor tag
                const link = e.target.closest('a');
                
                // Ensure it's a link, inside this container, and looks like pagination (usually inside a nav)
                if (link && logContainer.contains(link) && link.closest('nav')) {
                    e.preventDefault();
                    
                    const url = link.href;
                    if (!url) return;

                    // Loading State
                    logContainer.style.opacity = '0.6';
                    logContainer.style.pointerEvents = 'none';

                    fetch(url)
                        .then(response => {
                            if (!response.ok) throw new Error('Network response was not ok');
                            return response.text();
                        })
                        .then(html => {
                            // Parse the response
                            const parser = new DOMParser();
                            const doc = parser.parseFromString(html, 'text/html');
                            const newContent = doc.getElementById('activity-log');

                            if (newContent) {
                                logContainer.innerHTML = newContent.innerHTML;
                                
                                // Scroll logic: Only scroll if the container is above viewport (unlikely in reports page usually, 
                                // but keeps user focused on data if they scrolled down far)
                                const rect = logContainer.getBoundingClientRect();
                                if (rect.top < 0) {
                                    logContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });
                                }
                            } else {
                                window.location.href = url; // Fallback if parsing fails
                            }
                        })
                        .catch(error => {
                            console.error('Pagination Error:', error);
                            window.location.href = url; // Fallback to normal navigation
                        })
                        .finally(() => {
                            logContainer.style.opacity = '1';
                            logContainer.style.pointerEvents = 'auto';
                        });
                }
            });
        }
    });
</script>
@endpush
@endsection
