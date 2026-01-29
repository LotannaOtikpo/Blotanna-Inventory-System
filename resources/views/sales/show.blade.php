
@extends('layouts.app')

@section('content')
<header class="bg-white dark:bg-[#111318] border-b border-[#dbdfe6] dark:border-gray-800 px-8 py-4 flex items-center justify-between sticky top-0 z-10 transition-colors">
    <div>
        <h2 class="text-lg font-bold text-[#111318] dark:text-white">Sale Details</h2>
        <p class="text-sm text-[#616f89] dark:text-gray-400">Transaction ID: <span class="font-mono">{{ $sale->transaction_id }}</span></p>
    </div>
    <div class="flex items-center gap-3">
        <button onclick="window.print()" class="flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
            <span class="material-symbols-outlined text-[18px]">print</span>
            <span>Print Receipt</span>
        </button>
        <a href="{{ route('sales.index') }}" class="text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white">Back to History</a>
    </div>
</header>

<div class="p-8 max-w-3xl mx-auto print:p-0 print:max-w-none">
    <div class="bg-white dark:bg-[#1e232f] rounded-xl shadow-lg overflow-hidden border border-[#dbdfe6] dark:border-gray-800 transition-colors print:shadow-none print:border-none print:dark:bg-white print:dark:text-black">
        <!-- Receipt Header -->
        <div class="bg-gray-50 dark:bg-gray-800/50 p-8 border-b border-gray-100 dark:border-gray-700 text-center print:bg-white print:border-none">
            @php $logo = \App\Models\Setting::where('key', 'business_logo')->value('value'); @endphp
            @if($logo)
                <div class="flex justify-center mb-4">
                     <img src="{{ route('files.display', ['path' => $logo]) }}" class="h-16 w-auto object-contain">
                </div>
            @else
                <div class="inline-flex items-center justify-center size-12 bg-primary text-white rounded-lg mb-4 print:text-black print:bg-gray-200">
                    <span class="material-symbols-outlined">storefront</span>
                </div>
            @endif
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white print:text-black">{{ App\Models\Setting::where('key', 'business_name')->value('value') ?? 'BizTrack Pro' }}</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 print:text-gray-600">Sales Receipt</p>
            <p class="text-xs text-gray-400 dark:text-gray-500 mt-2 print:text-gray-500">{{ $sale->created_at->setTimezone(\App\Models\Setting::getValue('timezone', 'Africa/Lagos'))->format('F d, Y h:i A') }}</p>
        </div>

        <!-- Details -->
        <div class="p-8">
            <div class="grid grid-cols-2 gap-8 mb-8 text-sm">
                <div>
                    <div class="mb-4">
                        <p class="text-gray-500 dark:text-gray-400 mb-1">Sold By</p>
                        <p class="font-semibold text-gray-900 dark:text-white print:text-black">{{ $sale->user->name ?? 'Unknown Staff' }}</p>
                    </div>
                    
                    @if($sale->customer_name)
                    <div>
                        <p class="text-gray-500 dark:text-gray-400 mb-1">Customer</p>
                        <p class="font-semibold text-gray-900 dark:text-white print:text-black">{{ $sale->customer_name }}</p>
                        @if($sale->customer_email)<p class="text-xs text-gray-400 dark:text-gray-500">{{ $sale->customer_email }}</p>@endif
                        @if($sale->customer_phone)<p class="text-xs text-gray-400 dark:text-gray-500">{{ $sale->customer_phone }}</p>@endif
                    </div>
                    @endif
                </div>
                <div class="text-right">
                    <p class="text-gray-500 dark:text-gray-400 mb-1">Payment Method</p>
                    <p class="font-semibold text-gray-900 dark:text-white capitalize print:text-black">{{ $sale->payment_method }}</p>
                    <p class="text-xs text-green-600 dark:text-green-400 font-bold uppercase">{{ $sale->status }}</p>
                </div>
            </div>

            <!-- Items Table -->
            <div class="border dark:border-gray-700 rounded-lg overflow-hidden mb-8 print:border-gray-300">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 dark:bg-gray-800 text-gray-500 dark:text-gray-400 font-medium print:bg-gray-100 print:text-gray-700">
                        <tr>
                            <th class="px-4 py-3">Item</th>
                            <th class="px-4 py-3 text-right">Qty</th>
                            <th class="px-4 py-3 text-right">Price</th>
                            <th class="px-4 py-3 text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700 print:divide-gray-200">
                        @foreach($sale->saleItems as $item)
                        <tr>
                            <td class="px-4 py-3">
                                <p class="font-medium text-gray-900 dark:text-white print:text-black">{{ $item->product->name }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 font-mono print:text-gray-600">{{ $item->product->sku }}</p>
                            </td>
                            <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-300 print:text-black">{{ $item->quantity }}</td>
                            <td class="px-4 py-3 text-right text-gray-600 dark:text-gray-300 print:text-black">{{ \App\Models\Setting::where('key', 'currency_symbol')->value('value') ?? '$' }}{{ number_format($item->price, 2) }}</td>
                            <td class="px-4 py-3 text-right font-medium text-gray-900 dark:text-white print:text-black">{{ \App\Models\Setting::where('key', 'currency_symbol')->value('value') ?? '$' }}{{ number_format($item->price * $item->quantity, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Totals -->
            <div class="flex flex-col gap-2 items-end text-sm">
                @php 
                    $subtotal = $sale->total_amount - $sale->tax_amount;
                    // Calculate rate to show (avoid div by zero)
                    $rate = $subtotal > 0 ? round(($sale->tax_amount / $subtotal) * 100, 1) : 0;
                @endphp
                <div class="w-full max-w-xs flex justify-between text-gray-500 dark:text-gray-400 print:text-gray-600">
                    <span>Subtotal</span>
                    <span>{{ \App\Models\Setting::where('key', 'currency_symbol')->value('value') ?? '$' }}{{ number_format($subtotal, 2) }}</span>
                </div>
                <div class="w-full max-w-xs flex justify-between text-gray-500 dark:text-gray-400 print:text-gray-600">
                    <span>Tax ({{ $rate }}%)</span>
                    <span>{{ \App\Models\Setting::where('key', 'currency_symbol')->value('value') ?? '$' }}{{ number_format($sale->tax_amount, 2) }}</span>
                </div>
                <div class="w-full max-w-xs flex justify-between text-lg font-bold text-gray-900 dark:text-white pt-2 border-t border-gray-100 dark:border-gray-700 mt-2 print:text-black print:border-gray-300">
                    <span>Total</span>
                    <span>{{ \App\Models\Setting::where('key', 'currency_symbol')->value('value') ?? '$' }}{{ number_format($sale->total_amount, 2) }}</span>
                </div>
            </div>
        </div>
        
        <!-- Footer -->
        <div class="bg-gray-50 dark:bg-gray-800/50 p-6 text-center border-t border-gray-100 dark:border-gray-700 print:bg-white print:border-none">
            <p class="text-xs text-gray-400 dark:text-gray-500 print:text-gray-600">Thank you for your business!</p>
            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1 print:text-gray-600">For questions, please quote transaction ID: {{ $sale->transaction_id }}</p>
        </div>
    </div>
</div>

<style>
    @media print {
        body { visibility: hidden; }
        .print\:p-0 { padding: 0 !important; }
        .print\:max-w-none { max-width: none !important; }
        .print\:shadow-none { box-shadow: none !important; }
        .print\:border-none { border: none !important; }
        .print\:bg-white { background-color: white !important; }
        .print\:text-black { color: black !important; }
        .print\:text-gray-600 { color: #4b5563 !important; }
        .print\:text-gray-500 { color: #6b7280 !important; }
        .print\:divide-gray-200 { border-color: #e5e7eb !important; }
        .print\:bg-gray-100 { background-color: #f3f4f6 !important; }
        .print\:bg-gray-200 { background-color: #e5e7eb !important; }
        .print\:border-gray-300 { border-color: #d1d5db !important; }
        
        /* Show only the printable area */
        .p-8.max-w-3xl.mx-auto {
            visibility: visible;
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }
    }
</style>

@if(request('print'))
<script>
    window.addEventListener('load', () => {
        setTimeout(() => window.print(), 500);
    });
</script>
@endif
@endsection
