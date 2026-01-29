
@extends('layouts.app')

@section('content')
<header class="bg-white dark:bg-[#111318] border-b border-[#dbdfe6] dark:border-gray-800 px-8 py-4 flex items-center justify-between sticky top-0 z-10 transition-colors">
    <div>
        <h2 class="text-lg font-bold text-[#111318] dark:text-white">Create New Invoice</h2>
        <p class="text-sm text-[#616f89] dark:text-gray-400">Fill in the details below</p>
    </div>
    <a href="{{ route('invoices.index') }}" class="text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white">Back to List</a>
</header>

<div class="p-8 max-w-5xl mx-auto" x-data="invoiceForm()">
    <form action="{{ route('invoices.store') }}" method="POST" class="bg-white dark:bg-[#1e232f] rounded-xl border border-[#dbdfe6] dark:border-gray-800 shadow-sm transition-colors overflow-hidden">
        @csrf
        
        <!-- Header Info -->
        <div class="p-6 border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Customer <span class="text-red-500">*</span></label>
                    <select name="customer_id" required class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:ring-primary focus:border-primary">
                        <option value="">Select Customer</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}">{{ $customer->name }} ({{ $customer->email }})</option>
                        @endforeach
                    </select>
                    @if($customers->isEmpty())
                        <p class="text-xs text-red-500 mt-1">No customers found. <a href="{{ route('customers.index') }}" class="underline">Create one</a></p>
                    @endif
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Invoice Number</label>
                    <input type="text" name="invoice_number" value="{{ $invoiceNumber }}" readonly class="w-full rounded-lg border-gray-200 dark:border-gray-700 bg-gray-100 dark:bg-gray-800 text-gray-500 font-mono">
                </div>
                <div class="grid grid-cols-2 gap-2">
                    @php $defaultDays = \App\Models\Setting::getValue('invoice_due_days', 7); @endphp
                    <div>
                         <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Issue Date</label>
                         <input type="date" name="issue_date" value="{{ date('Y-m-d') }}" required class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:ring-primary focus:border-primary">
                    </div>
                    <div>
                         <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Due Date</label>
                         <input type="date" name="due_date" value="{{ date('Y-m-d', strtotime('+' . $defaultDays . ' days')) }}" required class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:ring-primary focus:border-primary">
                    </div>
                </div>
            </div>
        </div>

        <!-- Items Table -->
        <div class="p-6">
            <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-3">Invoice Items</h3>
            <div class="overflow-x-auto border rounded-lg dark:border-gray-700 mb-4">
                <table class="w-full text-left text-sm">
                    <thead class="bg-gray-50 dark:bg-gray-800 text-gray-500 dark:text-gray-400">
                        <tr>
                            <th class="px-4 py-2 w-1/2">Product / Service</th>
                            <th class="px-4 py-2 w-24">Qty</th>
                            <th class="px-4 py-2 w-32">Price</th>
                            <th class="px-4 py-2 w-32 text-right">Total</th>
                            <th class="px-4 py-2 w-10"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        <template x-for="(item, index) in items" :key="index">
                            <tr>
                                <td class="px-4 py-2">
                                    <div class="flex gap-2">
                                        <!-- Product Select (Optional) -->
                                        <select class="rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white text-xs py-1"
                                                x-model="item.product_id"
                                                @change="fillProductInfo(index, $event.target.value)">
                                            <option value="">Custom Item</option>
                                            @foreach($products as $p)
                                                <option value="{{ $p->id }}" data-name="{{ $p->name }}" data-price="{{ $p->price }}">{{ $p->name }}</option>
                                            @endforeach
                                        </select>
                                        <input type="hidden" :name="'items['+index+'][product_id]'" x-model="item.product_id">
                                        <input type="text" :name="'items['+index+'][product_name]'" x-model="item.name" placeholder="Item Name" required class="flex-1 rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white text-sm py-1">
                                    </div>
                                </td>
                                <td class="px-4 py-2">
                                    <input type="number" :name="'items['+index+'][quantity]'" x-model="item.qty" min="1" required class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white text-sm py-1">
                                </td>
                                <td class="px-4 py-2">
                                    <input type="number" step="0.01" :name="'items['+index+'][unit_price]'" x-model="item.price" min="0" required class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white text-sm py-1">
                                </td>
                                <td class="px-4 py-2 text-right font-medium text-gray-900 dark:text-white">
                                    <span x-text="formatMoney(item.qty * item.price)"></span>
                                </td>
                                <td class="px-4 py-2 text-center">
                                    <button type="button" @click="removeItem(index)" class="text-red-500 hover:text-red-700" title="Remove Item">
                                        <span class="material-symbols-outlined text-lg">delete</span>
                                    </button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <button type="button" @click="addItem()" class="text-primary hover:text-primary/80 font-medium text-sm flex items-center gap-1">
                <span class="material-symbols-outlined text-lg">add</span> Add Line Item
            </button>

            <!-- Totals -->
            <div class="mt-8 flex justify-end">
                <div class="w-64 space-y-2">
                    @php $taxRate = \App\Models\Setting::getValue('tax_rate', 8); @endphp
                    <div class="flex justify-between text-sm text-gray-500">
                        <span>Subtotal:</span>
                        <span class="font-medium text-gray-900 dark:text-white" x-text="formatMoney(subtotal)"></span>
                    </div>
                    <div class="flex justify-between text-sm text-gray-500">
                        <span>Tax ({{ $taxRate }}%):</span>
                        <span class="font-medium text-gray-900 dark:text-white" x-text="formatMoney(subtotal * {{ $taxRate / 100 }})"></span>
                    </div>
                    <div class="flex justify-between text-lg font-bold text-gray-900 dark:text-white border-t pt-2 dark:border-gray-700">
                        <span>Total:</span>
                        <span x-text="formatMoney(subtotal * {{ 1 + ($taxRate / 100) }})"></span>
                    </div>
                </div>
            </div>
            
            <div class="mt-6">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Notes</label>
                <textarea name="notes" rows="2" class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:ring-primary focus:border-primary" placeholder="Payment instructions, thanks, etc."></textarea>
            </div>
        </div>

        <div class="p-6 bg-gray-50 dark:bg-gray-800/50 border-t border-gray-100 dark:border-gray-700 flex justify-end gap-3">
             <button type="submit" class="px-6 py-2.5 rounded-lg bg-primary text-white font-bold shadow-sm hover:bg-primary/90 transition-all">Create Invoice</button>
        </div>
    </form>
</div>

<script>
    function invoiceForm() {
        return {
            items: [
                { product_id: '', name: '', qty: 1, price: 0 }
            ],
            
            addItem() {
                this.items.push({ product_id: '', name: '', qty: 1, price: 0 });
            },
            
            removeItem(index) {
                if(this.items.length > 1) {
                    this.items.splice(index, 1);
                }
            },
            
            fillProductInfo(index, productId) {
                if(!productId) {
                    this.items[index].name = '';
                    this.items[index].price = 0;
                    return;
                }
                const select = document.querySelectorAll('select')[index+1]; // +1 because first select is Customer
                const option = select.querySelector(`option[value="${productId}"]`);
                if(option) {
                    this.items[index].name = option.dataset.name;
                    this.items[index].price = parseFloat(option.dataset.price);
                }
            },
            
            get subtotal() {
                return this.items.reduce((sum, item) => sum + (item.qty * item.price), 0);
            },
            
            formatMoney(amount) {
                return "{{ \App\Models\Setting::where('key', 'currency_symbol')->value('value') ?? '$' }}" + amount.toFixed(2);
            }
        }
    }
</script>
@endsection
