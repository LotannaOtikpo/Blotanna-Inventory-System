
@extends('layouts.app')

@section('content')
<header class="bg-white dark:bg-[#111318] border-b border-[#dbdfe6] dark:border-gray-800 px-4 md:px-8 py-6 sticky top-0 z-10 transition-colors">
    <div class="flex flex-col md:flex-row justify-between md:items-center gap-4">
        <div>
            <h2 class="text-2xl md:text-3xl font-black tracking-tight text-[#111318] dark:text-white">Sales History</h2>
            <p class="text-sm text-[#616f89] dark:text-gray-400 mt-1">View past transactions and receipts.</p>
        </div>
        <a href="{{ route('sales.create') }}" class="bg-primary hover:bg-primary/90 text-white flex items-center justify-center gap-2 px-5 py-2.5 rounded-lg font-bold text-sm transition-all shadow-sm w-full md:w-auto">
            <span class="material-symbols-outlined text-[20px]">add</span>
            <span>New Sale</span>
        </a>
    </div>
</header>

<div class="flex-1 overflow-y-auto p-4 md:p-8 space-y-6">
    <!-- Filters -->
    <div class="flex flex-col md:flex-row items-center gap-4 bg-white dark:bg-[#1e232f] p-2 rounded-xl shadow-sm border border-[#dbdfe6] dark:border-gray-800 transition-colors">
        <div class="flex-1 w-full">
            <form action="{{ route('sales.index') }}" method="GET" class="relative">
                <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-gray-400">
                    <span class="material-symbols-outlined text-[20px]">search</span>
                </div>
                <input name="search" value="{{ request('search') }}" class="block w-full pl-11 pr-4 py-2.5 text-sm border-none bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white rounded-lg focus:ring-2 focus:ring-primary/20 placeholder:text-gray-400" placeholder="Search by Transaction ID, Cashier, Customer or Date..." type="text"/>
            </form>
        </div>
        <div class="flex items-center gap-2 w-full md:w-auto px-2 md:px-0">
            @if(request('search'))
            <a href="{{ route('sales.index') }}" class="flex items-center justify-center gap-2 px-4 py-2 text-sm font-medium text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-900/20 rounded-lg hover:bg-red-100 dark:hover:bg-red-900/30 transition-colors w-full md:w-auto">
                <span>Clear</span>
                <span class="material-symbols-outlined text-[18px]">close</span>
            </a>
            @endif
        </div>
    </div>

    <!-- Table -->
    <div id="sales-table" class="bg-white dark:bg-[#1e232f] rounded-xl shadow-sm border border-[#dbdfe6] dark:border-gray-800 overflow-hidden transition-colors">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse whitespace-nowrap">
                <thead>
                    <tr class="bg-gray-50/50 dark:bg-gray-800 border-b border-[#dbdfe6] dark:border-gray-700">
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Transaction ID</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Customer</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Cashier</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Method</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Items</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider text-right">Total</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($sales as $sale)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                        <td class="px-6 py-4">
                            <span class="font-mono text-sm text-gray-900 dark:text-white font-bold">{{ $sale->transaction_id }}</span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">
                            {{ $sale->created_at->setTimezone('Africa/Lagos')->format('M d, Y H:i') }}
                        </td>
                        <td class="px-6 py-4">
                            @if($sale->customer_name)
                                <div class="flex flex-col">
                                    <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ $sale->customer_name }}</span>
                                    <span class="text-xs text-gray-500 dark:text-gray-400">{{ $sale->customer_phone ?? 'No Phone' }}</span>
                                </div>
                            @else
                                <span class="text-xs text-gray-400 italic">Walk-in Customer</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <img src="{{ $sale->user->profile_photo_url }}" class="size-6 rounded-full bg-gray-200 object-cover">
                                <span class="text-sm font-medium text-gray-900 dark:text-gray-200">{{ $sale->user->name ?? 'Unknown' }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 capitalize">
                                {{ $sale->payment_method }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">
                            {{ $sale->saleItems->sum('quantity') }} items
                        </td>
                        <td class="px-6 py-4 text-right">
                            <span class="text-sm font-bold text-gray-900 dark:text-white">{{ \App\Models\Setting::where('key', 'currency_symbol')->value('value') ?? '$' }}{{ number_format($sale->total_amount, 2) }}</span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('sales.show', $sale) }}" class="text-primary hover:text-primary/80 font-medium text-sm">View</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                            <span class="material-symbols-outlined text-4xl mb-2 text-gray-300 dark:text-gray-600">search_off</span>
                            <p>No sales records found matching your search.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700">
            {{ $sales->links() }}
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const container = document.getElementById('sales-table');
        if (container) {
            container.addEventListener('click', function(e) {
                const link = e.target.closest('a');
                if (link && container.contains(link) && link.closest('nav')) {
                    e.preventDefault();
                    const url = link.href;
                    if (!url) return;
                    
                    container.style.opacity = '0.6';
                    container.style.pointerEvents = 'none';
                    
                    fetch(url)
                        .then(response => {
                            if (!response.ok) throw new Error('Network response was not ok');
                            return response.text();
                        })
                        .then(html => {
                            const parser = new DOMParser();
                            const doc = parser.parseFromString(html, 'text/html');
                            const newContent = doc.getElementById('sales-table');
                            if (newContent) {
                                container.innerHTML = newContent.innerHTML;
                                const rect = container.getBoundingClientRect();
                                if (rect.top < 0) {
                                    container.scrollIntoView({ behavior: 'smooth', block: 'start' });
                                }
                            } else {
                                window.location.href = url;
                            }
                        })
                        .catch(() => window.location.href = url)
                        .finally(() => {
                            container.style.opacity = '1';
                            container.style.pointerEvents = 'auto';
                        });
                }
            });
        }
    });
</script>
@endpush
@endsection
