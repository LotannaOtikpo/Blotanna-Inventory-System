
@extends('layouts.app')

@section('content')
<header class="bg-white dark:bg-[#111318] border-b border-[#dbdfe6] dark:border-gray-800 px-4 md:px-8 py-6 sticky top-0 z-10 transition-colors">
    <div class="flex flex-col md:flex-row justify-between md:items-center gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('settings.index') }}" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-white transition-colors">
                <span class="material-symbols-outlined text-xl">arrow_back</span>
            </a>
            <div>
                <h2 class="text-2xl md:text-3xl font-black tracking-tight text-[#111318] dark:text-white">Recycle Bin</h2>
                <p class="text-sm text-[#616f89] dark:text-gray-400 mt-1">Restore or permanently remove deleted items.</p>
            </div>
        </div>
    </div>
</header>

<div class="flex-1 overflow-y-auto p-4 md:p-8" x-data="{ activeTab: 'products' }">
    <div class="max-w-6xl mx-auto">
        <!-- Tabs -->
        <div class="flex space-x-1 rounded-xl bg-gray-100 dark:bg-gray-800 p-1 mb-6 max-w-lg">
            <button @click="activeTab = 'products'"
                :class="activeTab === 'products' ? 'bg-white dark:bg-gray-700 shadow text-gray-900 dark:text-white' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-700'"
                class="w-full rounded-lg py-2.5 text-sm font-bold leading-5 ring-white ring-opacity-60 ring-offset-2 ring-offset-blue-400 focus:outline-none focus:ring-2 transition-all">
                Products
            </button>
            <button @click="activeTab = 'customers'"
                :class="activeTab === 'customers' ? 'bg-white dark:bg-gray-700 shadow text-gray-900 dark:text-white' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-700'"
                class="w-full rounded-lg py-2.5 text-sm font-bold leading-5 ring-white ring-opacity-60 ring-offset-2 ring-offset-blue-400 focus:outline-none focus:ring-2 transition-all">
                Customers
            </button>
            <button @click="activeTab = 'invoices'"
                :class="activeTab === 'invoices' ? 'bg-white dark:bg-gray-700 shadow text-gray-900 dark:text-white' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-700'"
                class="w-full rounded-lg py-2.5 text-sm font-bold leading-5 ring-white ring-opacity-60 ring-offset-2 ring-offset-blue-400 focus:outline-none focus:ring-2 transition-all">
                Invoices
            </button>
        </div>

        <!-- Products List -->
        <div x-show="activeTab === 'products'" class="bg-white dark:bg-[#1e232f] rounded-xl shadow-sm border border-[#dbdfe6] dark:border-gray-800 overflow-hidden" style="display: none;">
            @if($products->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm whitespace-nowrap">
                        <thead class="bg-gray-50 dark:bg-gray-800 text-gray-500 dark:text-gray-400 font-medium">
                            <tr>
                                <th class="px-6 py-4">Name</th>
                                <th class="px-6 py-4">Category</th>
                                <th class="px-6 py-4">Deleted At</th>
                                <th class="px-6 py-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @foreach($products as $product)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                                <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">{{ $product->name }} <span class="text-gray-400 font-normal">({{ $product->sku }})</span></td>
                                <td class="px-6 py-4 text-gray-600 dark:text-gray-300">{{ $product->category->name ?? 'Uncategorized' }}</td>
                                <td class="px-6 py-4 text-gray-500 dark:text-gray-400">{{ $product->deleted_at->diffForHumans() }}</td>
                                <td class="px-6 py-4 text-right flex justify-end gap-2">
                                    <form action="{{ route('trash.restore', ['product', $product->id]) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="text-primary hover:bg-primary/10 px-3 py-1.5 rounded-lg transition-colors text-xs font-bold">Restore</button>
                                    </form>
                                    <form action="{{ route('trash.destroy', ['product', $product->id]) }}" method="POST">
                                        @csrf @method('DELETE')
                                        <button type="button" onclick="confirmForceDelete(this.form)" class="text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 px-3 py-1.5 rounded-lg transition-colors text-xs font-bold">Delete Forever</button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="p-12 text-center text-gray-500 dark:text-gray-400">
                    <span class="material-symbols-outlined text-4xl mb-2 text-gray-300 dark:text-gray-600">inventory_2</span>
                    <p>No deleted products found.</p>
                </div>
            @endif
        </div>

        <!-- Customers List -->
        <div x-show="activeTab === 'customers'" class="bg-white dark:bg-[#1e232f] rounded-xl shadow-sm border border-[#dbdfe6] dark:border-gray-800 overflow-hidden" style="display: none;">
            @if($customers->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm whitespace-nowrap">
                        <thead class="bg-gray-50 dark:bg-gray-800 text-gray-500 dark:text-gray-400 font-medium">
                            <tr>
                                <th class="px-6 py-4">Name</th>
                                <th class="px-6 py-4">Email</th>
                                <th class="px-6 py-4">Deleted At</th>
                                <th class="px-6 py-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @foreach($customers as $customer)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                                <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">{{ $customer->name }}</td>
                                <td class="px-6 py-4 text-gray-600 dark:text-gray-300">{{ $customer->email }}</td>
                                <td class="px-6 py-4 text-gray-500 dark:text-gray-400">{{ $customer->deleted_at->diffForHumans() }}</td>
                                <td class="px-6 py-4 text-right flex justify-end gap-2">
                                    <form action="{{ route('trash.restore', ['customer', $customer->id]) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="text-primary hover:bg-primary/10 px-3 py-1.5 rounded-lg transition-colors text-xs font-bold">Restore</button>
                                    </form>
                                    <form action="{{ route('trash.destroy', ['customer', $customer->id]) }}" method="POST">
                                        @csrf @method('DELETE')
                                        <button type="button" onclick="confirmForceDelete(this.form)" class="text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 px-3 py-1.5 rounded-lg transition-colors text-xs font-bold">Delete Forever</button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="p-12 text-center text-gray-500 dark:text-gray-400">
                    <span class="material-symbols-outlined text-4xl mb-2 text-gray-300 dark:text-gray-600">group_off</span>
                    <p>No deleted customers found.</p>
                </div>
            @endif
        </div>

        <!-- Invoices List -->
        <div x-show="activeTab === 'invoices'" class="bg-white dark:bg-[#1e232f] rounded-xl shadow-sm border border-[#dbdfe6] dark:border-gray-800 overflow-hidden" style="display: none;">
            @if($invoices->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm whitespace-nowrap">
                        <thead class="bg-gray-50 dark:bg-gray-800 text-gray-500 dark:text-gray-400 font-medium">
                            <tr>
                                <th class="px-6 py-4">Invoice #</th>
                                <th class="px-6 py-4">Customer</th>
                                <th class="px-6 py-4">Amount</th>
                                <th class="px-6 py-4">Deleted At</th>
                                <th class="px-6 py-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @foreach($invoices as $invoice)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                                <td class="px-6 py-4 font-mono font-medium text-gray-900 dark:text-white">{{ $invoice->invoice_number }}</td>
                                <td class="px-6 py-4 text-gray-600 dark:text-gray-300">{{ $invoice->customer->name ?? 'Unknown' }}</td>
                                <td class="px-6 py-4 text-gray-900 dark:text-white font-bold">{{ \App\Models\Setting::where('key', 'currency_symbol')->value('value') ?? '$' }}{{ number_format($invoice->total_amount, 2) }}</td>
                                <td class="px-6 py-4 text-gray-500 dark:text-gray-400">{{ $invoice->deleted_at->diffForHumans() }}</td>
                                <td class="px-6 py-4 text-right flex justify-end gap-2">
                                    <form action="{{ route('trash.restore', ['invoice', $invoice->id]) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="text-primary hover:bg-primary/10 px-3 py-1.5 rounded-lg transition-colors text-xs font-bold">Restore</button>
                                    </form>
                                    <form action="{{ route('trash.destroy', ['invoice', $invoice->id]) }}" method="POST">
                                        @csrf @method('DELETE')
                                        <button type="button" onclick="confirmForceDelete(this.form)" class="text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 px-3 py-1.5 rounded-lg transition-colors text-xs font-bold">Delete Forever</button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="p-12 text-center text-gray-500 dark:text-gray-400">
                    <span class="material-symbols-outlined text-4xl mb-2 text-gray-300 dark:text-gray-600">description</span>
                    <p>No deleted invoices found.</p>
                </div>
            @endif
        </div>
        
        <div class="mt-6 p-4 bg-blue-50 dark:bg-blue-900/10 border border-blue-100 dark:border-blue-900/30 rounded-xl text-sm text-blue-700 dark:text-blue-300 flex items-start gap-3">
             <span class="material-symbols-outlined text-xl shrink-0">info</span>
             <p>Items in the recycle bin are automatically permanently deleted after 30 days. Restoring an invoice will also attempt to restore its associated sale record if it was paid.</p>
        </div>
    </div>
</div>

<script>
    function confirmForceDelete(form) {
        window.showConfirm('Delete Permanently?', 'This action cannot be undone. The item will be removed from the database forever.', 'Delete Forever', 'Cancel', 'danger')
            .then(result => { if(result) form.submit(); });
    }
</script>
@endsection
