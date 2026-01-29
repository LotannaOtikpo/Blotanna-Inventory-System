
@extends('layouts.app')

@section('content')
<header class="bg-white dark:bg-[#111318] border-b border-[#dbdfe6] dark:border-gray-800 px-8 py-4 flex items-center justify-between sticky top-0 z-10 transition-colors">
    <div class="flex items-center gap-4">
        <a href="{{ route('invoices.index') }}" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-white">
            <span class="material-symbols-outlined">arrow_back</span>
        </a>
        <div>
            <h2 class="text-lg font-bold text-[#111318] dark:text-white">Invoice #{{ $invoice->invoice_number }}</h2>
            <div class="flex items-center gap-2">
                 <span class="text-xs px-2 py-0.5 rounded-full font-bold uppercase {{ $invoice->status_color }}">{{ $invoice->status }}</span>
                 <span class="text-sm text-[#616f89] dark:text-gray-400">Created {{ $invoice->created_at->format('M d, Y') }}</span>
            </div>
        </div>
    </div>
    <div class="flex items-center gap-3">
        <!-- Status Toggle -->
        <form action="{{ route('invoices.status', $invoice) }}" method="POST">
            @csrf
            @method('PATCH')
            <select name="status" onchange="this.form.submit()" class="text-sm rounded-lg border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 py-1.5 pl-3 pr-8 focus:ring-primary focus:border-primary">
                <option value="draft" {{ $invoice->status == 'draft' ? 'selected' : '' }}>Draft</option>
                <option value="sent" {{ $invoice->status == 'sent' ? 'selected' : '' }}>Sent</option>
                <option value="paid" {{ $invoice->status == 'paid' ? 'selected' : '' }}>Paid</option>
                <option value="cancelled" {{ $invoice->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>
        </form>

        <button onclick="window.print()" class="flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
            <span class="material-symbols-outlined text-[18px]">print</span>
            <span>Print</span>
        </button>
        
        <form action="{{ route('invoices.send', $invoice) }}" method="POST">
            @csrf
            <button type="button" onclick="confirmSend(this.form, '{{ $invoice->customer->email }}')" class="flex items-center gap-2 px-4 py-2 text-sm font-bold text-white bg-primary rounded-lg hover:bg-primary/90 shadow-sm transition-colors">
                <span class="material-symbols-outlined text-[18px]">send</span>
                <span>Send to Email</span>
            </button>
        </form>
        
        <!-- Delete Button -->
        <form action="{{ route('invoices.destroy', $invoice) }}" method="POST">
            @csrf
            @method('DELETE')
            <button type="button" onclick="confirmDelete(this.form)" class="flex items-center gap-2 px-4 py-2 text-sm font-bold text-red-600 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg hover:bg-red-100 dark:hover:bg-red-900/40 shadow-sm transition-colors">
                <span class="material-symbols-outlined text-[18px]">delete</span>
                <span>Delete</span>
            </button>
        </form>
    </div>
</header>

<div class="p-8 max-w-4xl mx-auto print:p-0 print:max-w-none">
    <div class="bg-white dark:bg-white rounded-xl shadow-lg border border-[#dbdfe6] dark:border-gray-800 overflow-hidden text-gray-900 print:shadow-none print:border-none">
        
        <!-- Professional Invoice Layout (Always White Background for Print/PDF Consistency) -->
        <div class="p-10">
            <!-- Top Section -->
            <div class="flex justify-between items-start mb-12">
                <div>
                    @php $logo = \App\Models\Setting::where('key', 'business_logo')->value('value'); @endphp
                    @if($logo)
                        <img src="{{ route('files.display', ['path' => $logo]) }}" class="h-16 w-auto object-contain mb-4">
                    @else
                        <h1 class="text-2xl font-bold text-primary mb-2">{{ \App\Models\Setting::where('key', 'business_name')->value('value') ?? 'Blotanna Nig Ltd' }}</h1>
                    @endif
                    <p class="text-sm text-gray-500">Invoice #{{ $invoice->invoice_number }}</p>
                </div>
                <div class="text-right">
                    <h1 class="text-4xl font-black text-gray-200 uppercase tracking-widest">Invoice</h1>
                    <div class="mt-4 space-y-1 text-sm">
                        <p class="text-gray-500">Issue Date: <span class="text-gray-900 font-medium">{{ $invoice->issue_date->format('M d, Y') }}</span></p>
                        <p class="text-gray-500">Due Date: <span class="text-gray-900 font-medium">{{ $invoice->due_date->format('M d, Y') }}</span></p>
                    </div>
                </div>
            </div>

            <!-- Addresses -->
            <div class="grid grid-cols-2 gap-12 mb-12">
                <div>
                    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Billed To</h3>
                    <p class="font-bold text-lg">{{ $invoice->customer->name }}</p>
                    <p class="text-gray-600">{{ $invoice->customer->email }}</p>
                    <p class="text-gray-600">{{ $invoice->customer->phone }}</p>
                    <p class="text-gray-600 whitespace-pre-line">{{ $invoice->customer->address }}</p>
                </div>
                <div>
                    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">From</h3>
                    <p class="font-bold text-lg">{{ \App\Models\Setting::where('key', 'business_name')->value('value') ?? 'Blotanna Nig Ltd' }}</p>
                    <p class="text-gray-600">admin@blotanna.com</p>
                    <!-- In a real app, pull address from settings -->
                </div>
            </div>

            <!-- Items -->
            <table class="w-full mb-8">
                <thead>
                    <tr class="bg-gray-50 border-y border-gray-200">
                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Item Description</th>
                        <th class="px-4 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Qty</th>
                        <th class="px-4 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Price</th>
                        <th class="px-4 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($invoice->items as $item)
                    <tr>
                        <td class="px-4 py-3 text-sm text-gray-900">{{ $item->product_name }}</td>
                        <td class="px-4 py-3 text-right text-sm text-gray-600">{{ $item->quantity }}</td>
                        <td class="px-4 py-3 text-right text-sm text-gray-600">{{ \App\Models\Setting::where('key', 'currency_symbol')->value('value') ?? '$' }}{{ number_format($item->unit_price, 2) }}</td>
                        <td class="px-4 py-3 text-right text-sm font-medium text-gray-900">{{ \App\Models\Setting::where('key', 'currency_symbol')->value('value') ?? '$' }}{{ number_format($item->total, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Totals -->
            <div class="flex justify-end border-t border-gray-200 pt-8">
                <div class="w-64 space-y-3">
                    @php 
                        $subtotal = $invoice->subtotal;
                        // Calculate rate dynamically based on stored tax amount
                        $rate = $subtotal > 0 ? round(($invoice->tax_amount / $subtotal) * 100, 1) : 0;
                    @endphp
                    <div class="flex justify-between text-sm text-gray-600">
                        <span>Subtotal</span>
                        <span class="font-medium">{{ \App\Models\Setting::where('key', 'currency_symbol')->value('value') ?? '$' }}{{ number_format($invoice->subtotal, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-sm text-gray-600">
                        <span>Tax ({{ $rate }}%)</span>
                        <span class="font-medium">{{ \App\Models\Setting::where('key', 'currency_symbol')->value('value') ?? '$' }}{{ number_format($invoice->tax_amount, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-xl font-bold text-gray-900 border-t border-gray-200 pt-3">
                        <span>Total Due</span>
                        <span>{{ \App\Models\Setting::where('key', 'currency_symbol')->value('value') ?? '$' }}{{ number_format($invoice->total_amount, 2) }}</span>
                    </div>
                </div>
            </div>

            <!-- Notes -->
            @if($invoice->notes)
            <div class="mt-12 pt-8 border-t border-gray-100">
                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Notes</h3>
                <p class="text-sm text-gray-600">{{ $invoice->notes }}</p>
            </div>
            @endif
        </div>
    </div>
</div>

<script>
    function confirmSend(form, email) {
        window.showConfirm('Send Invoice?', 'Are you sure you want to send this invoice to ' + email + '?')
            .then(result => { if(result) form.submit(); });
    }
    
    function confirmDelete(form) {
        window.showConfirm('Delete Invoice?', 'Are you sure? If this invoice was Paid, the revenue will also be removed from your reports.')
            .then(result => { if(result) form.submit(); });
    }
</script>

<style>
    @media print {
        body { visibility: hidden; }
        .print\:p-0 { padding: 0 !important; }
        .print\:max-w-none { max-width: none !important; }
        .print\:shadow-none { box-shadow: none !important; }
        .print\:border-none { border: none !important; }
        .print\:dark\:bg-white { background-color: white !important; }
        
        /* Show only the printable area */
        .p-8.max-w-4xl.mx-auto {
            visibility: visible;
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }
    }
</style>
@endsection
