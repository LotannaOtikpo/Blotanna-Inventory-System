
@extends('layouts.app')

@section('content')
<header class="bg-white dark:bg-[#111318] border-b border-[#dbdfe6] dark:border-gray-800 px-4 md:px-8 py-6 sticky top-0 z-10 transition-colors">
    <div class="flex flex-col md:flex-row justify-between md:items-center gap-4">
        <div>
            <h2 class="text-2xl md:text-3xl font-black tracking-tight text-[#111318] dark:text-white">Invoices</h2>
            <p class="text-sm text-[#616f89] dark:text-gray-400 mt-1">Track and manage client invoices.</p>
        </div>
        <a href="{{ route('invoices.create') }}" class="bg-primary hover:bg-primary/90 text-white flex items-center justify-center gap-2 px-5 py-2.5 rounded-lg font-bold text-sm transition-all shadow-sm w-full md:w-auto">
            <span class="material-symbols-outlined text-[20px]">add_circle</span>
            <span>Create Invoice</span>
        </a>
    </div>
</header>

<div class="flex-1 overflow-y-auto p-4 md:p-8 space-y-6">
    <!-- Search -->
    <div class="bg-white dark:bg-[#1e232f] p-2 rounded-xl shadow-sm border border-[#dbdfe6] dark:border-gray-800 transition-colors">
        <form action="{{ route('invoices.index') }}" method="GET" class="relative">
            <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-gray-400">
                <span class="material-symbols-outlined text-[20px]">search</span>
            </div>
            <input name="search" value="{{ request('search') }}" class="block w-full pl-11 pr-4 py-2.5 text-sm border-none bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white rounded-lg focus:ring-2 focus:ring-primary/20 placeholder:text-gray-400" placeholder="Search by invoice number or customer name..." type="text"/>
        </form>
    </div>

    <!-- Table -->
    <div id="invoices-table" class="bg-white dark:bg-[#1e232f] rounded-xl shadow-sm border border-[#dbdfe6] dark:border-gray-800 overflow-hidden transition-colors">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse whitespace-nowrap">
                <thead>
                    <tr class="bg-gray-50/50 dark:bg-gray-800 border-b border-[#dbdfe6] dark:border-gray-700">
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Invoice #</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Customer</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Due Date</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Amount</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($invoices as $invoice)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors group">
                        <td class="px-6 py-4">
                            <span class="font-mono font-bold text-gray-900 dark:text-white">{{ $invoice->invoice_number }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-col">
                                <span class="font-medium text-gray-900 dark:text-white">{{ $invoice->customer->name }}</span>
                                <span class="text-xs text-gray-500">{{ $invoice->customer->email }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">
                            {{ $invoice->issue_date->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">
                            {{ $invoice->due_date->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4 text-sm font-bold text-gray-900 dark:text-white">
                            {{ \App\Models\Setting::where('key', 'currency_symbol')->value('value') ?? '$' }}{{ number_format($invoice->total_amount, 2) }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium uppercase {{ $invoice->status_color }}">
                                {{ $invoice->status }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('invoices.show', $invoice) }}" class="text-primary hover:underline text-sm font-medium p-1">View</a>
                                
                                <form action="{{ route('invoices.destroy', $invoice) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" onclick="confirmDelete(this.form)" class="text-gray-400 hover:text-red-600 p-1" title="Delete Invoice">
                                        <span class="material-symbols-outlined text-[18px]">delete</span>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                            <span class="material-symbols-outlined text-4xl mb-2 text-gray-300 dark:text-gray-600">description</span>
                            <p>No invoices found.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700">
            {{ $invoices->links() }}
        </div>
    </div>
</div>

@push('scripts')
<script>
    function confirmDelete(form) {
        window.showConfirm('Delete Invoice?', 'Are you sure? If this invoice was Paid, the revenue will also be removed from your reports.')
            .then(result => { if(result) form.submit(); });
    }

    document.addEventListener('DOMContentLoaded', function() {
        const container = document.getElementById('invoices-table');
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
                            const newContent = doc.getElementById('invoices-table');
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
