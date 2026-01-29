
@extends('layouts.app')

@section('content')
<!-- Header -->
<header class="bg-white dark:bg-[#111318] border-b border-[#dbdfe6] dark:border-gray-800 px-4 md:px-8 py-6 shrink-0 transition-colors">
    <div class="flex flex-col md:flex-row justify-between md:items-center gap-4">
        <div>
            <h2 class="text-2xl md:text-3xl font-black tracking-tight text-[#111318] dark:text-white">Point of Sale</h2>
            <p class="text-sm text-[#616f89] dark:text-gray-400 mt-1">Process sales and manage customer orders.</p>
        </div>
        <div>
             <a href="{{ route('dashboard') }}" class="text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white">Back to Dashboard</a>
        </div>
    </div>
</header>

<div class="flex flex-col md:flex-row flex-1 overflow-hidden" id="pos-app">
    <!-- Product Grid Section -->
    <div class="flex-1 flex flex-col border-r border-[#dbdfe6] dark:border-gray-800 bg-white dark:bg-[#1e232f] overflow-hidden transition-colors">
        <!-- Search Bar -->
        <div class="p-3 md:p-4 border-b border-[#dbdfe6] dark:border-gray-800 flex flex-col md:flex-row gap-3 bg-white dark:bg-[#1e232f] transition-colors shadow-sm z-10 shrink-0">
            <div class="relative flex-1">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">search</span>
                <input type="text" id="search-input" class="w-full pl-10 pr-4 py-2.5 border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-white rounded-lg focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all outline-none font-medium text-sm" placeholder="Search product name or SKU...">
            </div>
            <select id="category-filter" class="border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-white rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-primary/50 focus:border-primary outline-none cursor-pointer w-full md:w-auto">
                <option value="">All Categories</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>
        </div>

        <!-- Products Grid (Standard Viewport Breakpoints) -->
        <div class="flex-1 overflow-y-auto p-3 md:p-4 bg-[#f6f6f8] dark:bg-[#111318] transition-colors relative pb-24 md:pb-4">
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 2xl:grid-cols-5 gap-3 md:gap-4" id="product-grid">
                @foreach($products as $product)
                <button class="product-card text-left bg-white dark:bg-[#1e232f] p-3 rounded-xl border border-[#dbdfe6] dark:border-gray-800 shadow-sm hover:border-primary hover:shadow-md active:scale-95 cursor-pointer transition-all flex flex-col gap-2 group h-full"
                     onclick="addToCart(this)"
                     data-id="{{ $product->id }}"
                     data-name="{{ $product->name }}"
                     data-sku="{{ $product->sku }}"
                     data-price="{{ $product->price }}"
                     data-stock="{{ $product->quantity }}"
                     data-image="{{ $product->image_url }}"
                     data-category="{{ $product->category_id }}">
                    
                    <div class="aspect-square bg-gray-100 dark:bg-gray-800 rounded-lg flex items-center justify-center overflow-hidden relative w-full">
                        @if($product->image_path)
                            <img src="{{ $product->image_url }}" class="w-full h-full object-cover" onerror="this.onerror=null; this.parentElement.innerHTML='<span class=\'material-symbols-outlined text-gray-400\'>image_not_supported</span>';">
                        @else
                            <span class="material-symbols-outlined text-gray-400">inventory_2</span>
                        @endif
                        
                        <!-- Stock Badge -->
                        <div class="absolute top-1 right-1">
                            <span class="text-[10px] px-1.5 py-0.5 rounded-full {{ $product->quantity > 0 ? 'bg-green-100/90 text-green-700 backdrop-blur-sm' : 'bg-red-100/90 text-red-700 backdrop-blur-sm' }} font-bold shadow-sm">
                                {{ $product->quantity }}
                            </span>
                        </div>
                    </div>
                    
                    <div class="flex-1 flex flex-col min-w-0 w-full">
                        <h4 class="text-sm font-bold text-gray-800 dark:text-gray-200 truncate leading-tight mb-1" title="{{ $product->name }}">{{ $product->name }}</h4>
                        <p class="text-[10px] text-gray-500 dark:text-gray-400 font-mono truncate">{{ $product->sku }}</p>
                        
                        <div class="mt-auto pt-2 flex items-center justify-between">
                            <span class="text-sm font-black text-primary">
                                {{ \App\Models\Setting::where('key', 'currency_symbol')->value('value') ?? '$' }}{{ number_format($product->price, 2) }}
                            </span>
                            <div class="size-6 rounded-full bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400 flex items-center justify-center group-hover:bg-primary group-hover:text-white transition-colors">
                                <span class="material-symbols-outlined text-[16px]">add</span>
                            </div>
                        </div>
                    </div>
                </button>
                @endforeach
            </div>
            
            <!-- No Results Message -->
            <div id="no-results" class="hidden absolute inset-0 flex flex-col items-center justify-center text-gray-400 dark:text-gray-500 pointer-events-none">
                <span class="material-symbols-outlined text-5xl mb-2 opacity-50">search_off</span>
                <p class="font-medium">No products found</p>
            </div>
        </div>
    </div>

    <!-- Cart Section (Fixed Bottom Sheet on Mobile, Sidebar on Desktop) -->
    <div class="fixed bottom-0 left-0 right-0 md:relative md:w-80 lg:w-96 xl:w-[450px] shrink-0 flex flex-col bg-white dark:bg-[#1e232f] shadow-[0_-10px_40px_-10px_rgba(0,0,0,0.1)] md:shadow-xl z-30 border-t md:border-t-0 md:border-l border-[#dbdfe6] dark:border-gray-800 transition-all h-[40vh] md:h-auto" 
         :class="mobileCartOpen ? 'h-[80vh]' : 'h-[80px] md:h-auto'"
         x-data="{ mobileCartOpen: false }">
         
        <!-- Mobile Toggle Handle -->
        <div class="md:hidden flex justify-center pt-2 pb-1 cursor-pointer bg-white dark:bg-[#1e232f]" @click="mobileCartOpen = !mobileCartOpen">
            <div class="w-12 h-1.5 rounded-full bg-gray-300 dark:bg-gray-600"></div>
        </div>

        <div class="p-3 md:p-5 border-b border-[#dbdfe6] dark:border-gray-800 flex justify-between items-center bg-white dark:bg-[#1e232f] shrink-0" @click="if(window.innerWidth < 768) mobileCartOpen = !mobileCartOpen">
            <div>
                <h2 class="text-lg md:text-xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <span class="material-symbols-outlined text-primary">shopping_cart</span>
                    <span class="md:inline">Current Sale</span>
                    <span class="md:hidden text-sm font-normal text-gray-500" x-text="mobileCartOpen ? '(Tap to close)' : '(Tap to expand)'"></span>
                </h2>
                <p class="hidden md:block text-xs text-gray-500 dark:text-gray-400 mt-0.5">Transaction ID: <span class="font-mono font-medium">{{ $transactionId }}</span></p>
            </div>
            <div class="flex items-center gap-2">
                 <!-- Mobile Total Preview -->
                <span class="md:hidden font-bold text-primary" id="mobile-total-preview">{{ \App\Models\Setting::where('key', 'currency_symbol')->value('value') ?? '$' }}0.00</span>
                
                <button onclick="clearCart(); event.stopPropagation();" class="p-2 text-gray-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-all" title="Clear Cart">
                    <span class="material-symbols-outlined">delete_sweep</span>
                </button>
            </div>
        </div>

        <!-- Customer Details Section -->
        <div class="px-3 md:px-5 py-3 bg-gray-50 dark:bg-gray-800/50 border-b border-[#dbdfe6] dark:border-gray-800">
            <div x-data="{ open: false }">
                <button @click="open = !open" type="button" class="flex items-center gap-2 text-sm font-bold text-gray-700 dark:text-gray-300 hover:text-primary transition-colors w-full">
                    <span class="material-symbols-outlined text-[20px]" x-text="open ? 'expand_less' : 'person_add'">person_add</span>
                    <span x-text="open ? 'Hide Customer Details' : 'Add Customer (Optional)'">Add Customer (Optional)</span>
                </button>
                <div x-show="open" class="mt-3 space-y-3" style="display: none;">
                    <input type="text" id="cust-name" placeholder="Full Name" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm focus:ring-primary focus:border-primary placeholder-gray-400 dark:placeholder-gray-500">
                    <input type="email" id="cust-email" placeholder="Email Address" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm focus:ring-primary focus:border-primary placeholder-gray-400 dark:placeholder-gray-500">
                    <input type="tel" id="cust-phone" placeholder="Phone Number" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm focus:ring-primary focus:border-primary placeholder-gray-400 dark:placeholder-gray-500">
                </div>
            </div>
        </div>

        <!-- Cart Items List -->
        <div class="flex-1 overflow-y-auto p-3 md:p-5 space-y-3 bg-white dark:bg-[#1e232f]" id="cart-items-container">
            <!-- Items injected via JS -->
        </div>

        <!-- Empty State -->
        <div id="empty-cart-msg" class="hidden flex-1 flex flex-col items-center justify-center text-gray-400 dark:text-gray-500 p-8 bg-white dark:bg-[#1e232f]">
            <div class="bg-gray-100 dark:bg-gray-800 p-4 rounded-full mb-3">
                <span class="material-symbols-outlined text-3xl">shopping_cart_off</span>
            </div>
            <p class="font-medium">Cart is empty</p>
            <p class="text-sm opacity-70">Add items to start a sale</p>
        </div>

        <!-- Totals & Actions -->
        <div class="p-3 md:p-5 bg-white dark:bg-[#1e232f] border-t border-[#dbdfe6] dark:border-gray-800 space-y-4 shrink-0">
            <div class="space-y-2">
                <div class="flex justify-between text-sm text-gray-900 dark:text-gray-300">
                    <span class="text-gray-500 dark:text-gray-400">Subtotal</span>
                    <span class="font-mono font-medium" id="subtotal-display">{{ \App\Models\Setting::where('key', 'currency_symbol')->value('value') ?? '$' }}0.00</span>
                </div>
                <div class="flex justify-between text-sm text-gray-900 dark:text-gray-300">
                    @php $taxRate = \App\Models\Setting::getValue('tax_rate', 8); @endphp
                    <span class="text-gray-500 dark:text-gray-400">Tax ({{ $taxRate }}%)</span>
                    <span class="font-mono font-medium" id="tax-display">{{ \App\Models\Setting::where('key', 'currency_symbol')->value('value') ?? '$' }}0.00</span>
                </div>
                <div class="pt-3 border-t border-dashed border-gray-200 dark:border-gray-700 flex justify-between items-end">
                    <span class="text-base font-bold text-gray-900 dark:text-white">Grand Total</span>
                    <span class="text-2xl font-black text-primary tracking-tight" id="total-display">{{ \App\Models\Setting::where('key', 'currency_symbol')->value('value') ?? '$' }}0.00</span>
                </div>
            </div>

            <div class="space-y-3">
                <!-- Payment Method -->
                <div class="grid grid-cols-3 gap-2" id="payment-methods">
                    <button type="button" onclick="setPaymentMethod('cash')" class="payment-method-btn active bg-primary/10 text-primary border-primary border" data-method="cash">
                        <span class="material-symbols-outlined text-lg">payments</span>
                        <span class="text-xs font-bold">Cash</span>
                    </button>
                    <button type="button" onclick="setPaymentMethod('transfer')" class="payment-method-btn bg-gray-50 dark:bg-gray-800 text-gray-600 dark:text-gray-400 border border-transparent" data-method="transfer">
                        <span class="material-symbols-outlined text-lg">account_balance</span>
                        <span class="text-xs font-bold">Transfer</span>
                    </button>
                    <button type="button" onclick="setPaymentMethod('card')" class="payment-method-btn bg-gray-50 dark:bg-gray-800 text-gray-600 dark:text-gray-400 border border-transparent" data-method="card">
                        <span class="material-symbols-outlined text-lg">credit_card</span>
                        <span class="text-xs font-bold">Card</span>
                    </button>
                </div>

                <button onclick="processSale()" id="checkout-btn" disabled class="w-full py-3.5 bg-primary disabled:bg-gray-300 disabled:dark:bg-gray-700 text-white rounded-xl font-bold shadow-lg shadow-primary/30 disabled:shadow-none hover:bg-primary/90 transition-all active:scale-[0.98] flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined">check_circle</span>
                    <span>Complete Sale</span>
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    .payment-method-btn {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 0.25rem;
        padding: 0.75rem;
        border-radius: 0.75rem;
        transition: all 0.2s;
    }
    .payment-method-btn.active {
        background-color: rgb(19 91 236 / 0.1);
        color: #135bec;
        border-color: #135bec;
    }
    .payment-method-btn:hover:not(.active) {
        background-color: #f3f4f6;
    }
    .dark .payment-method-btn:hover:not(.active) {
        background-color: #374151;
    }
</style>

@push('scripts')
<script>
    let cart = [];
    let currentPaymentMethod = 'cash';
    const TAX_RATE = {{ $taxRate }};
    const CURRENCY = "{{ \App\Models\Setting::where('key', 'currency_symbol')->value('value') ?? '$' }}";

    // --- Core Functions ---

    function formatMoney(amount) {
        return CURRENCY + amount.toFixed(2);
    }

    function addToCart(btn) {
        const id = parseInt(btn.dataset.id);
        const name = btn.dataset.name;
        const price = parseFloat(btn.dataset.price);
        const stock = parseInt(btn.dataset.stock);
        const image = btn.dataset.image;

        if (stock <= 0) {
            window.showAlert('Out of Stock', 'This product is currently unavailable.');
            return;
        }

        const existingItem = cart.find(item => item.id === id);

        if (existingItem) {
            if (existingItem.qty < stock) {
                existingItem.qty++;
            } else {
                window.showAlert('Stock Limit', 'Cannot add more than available stock.');
            }
        } else {
            cart.push({ id, name, price, qty: 1, stock, image });
        }

        updateCartUI();
        
        // Visual Feedback on Card
        const originalBg = btn.style.backgroundColor;
        btn.classList.add('ring-2', 'ring-primary', 'ring-inset');
        setTimeout(() => {
             btn.classList.remove('ring-2', 'ring-primary', 'ring-inset');
        }, 200);
    }

    function updateQty(index, delta) {
        const item = cart[index];
        const newQty = item.qty + delta;

        if (newQty > 0 && newQty <= item.stock) {
            item.qty = newQty;
        } else if (newQty > item.stock) {
            window.showAlert('Stock Limit', 'Cannot add more than available stock.');
        } else {
            cart.splice(index, 1);
        }
        updateCartUI();
    }

    function removeFromCart(index) {
        cart.splice(index, 1);
        updateCartUI();
    }

    function clearCart() {
        if(cart.length > 0) {
            window.showConfirm('Clear Cart?', 'Are you sure you want to remove all items?', 'Clear', 'Cancel')
            .then(result => {
                if(result) {
                    cart = [];
                    updateCartUI();
                }
            });
        }
    }

    function updateCartUI() {
        const container = document.getElementById('cart-items-container');
        const emptyMsg = document.getElementById('empty-cart-msg');
        const checkoutBtn = document.getElementById('checkout-btn');

        container.innerHTML = '';
        
        if (cart.length === 0) {
            emptyMsg.classList.remove('hidden');
            checkoutBtn.disabled = true;
            document.getElementById('subtotal-display').innerText = formatMoney(0);
            document.getElementById('tax-display').innerText = formatMoney(0);
            document.getElementById('total-display').innerText = formatMoney(0);
            document.getElementById('mobile-total-preview').innerText = formatMoney(0);
            return;
        }

        emptyMsg.classList.add('hidden');
        checkoutBtn.disabled = false;

        let subtotal = 0;

        cart.forEach((item, index) => {
            subtotal += item.price * item.qty;
            
            const div = document.createElement('div');
            div.className = 'flex gap-3 bg-gray-50 dark:bg-gray-800/50 p-3 rounded-xl border border-gray-100 dark:border-gray-700';
            div.innerHTML = `
                <div class="size-14 rounded-lg bg-white dark:bg-gray-700 shrink-0 overflow-hidden border border-gray-100 dark:border-gray-600">
                    <img src="${item.image}" class="w-full h-full object-cover" onerror="this.style.display='none'">
                </div>
                <div class="flex-1 min-w-0 flex flex-col justify-between">
                    <div class="flex justify-between items-start gap-2">
                        <p class="text-sm font-bold text-gray-900 dark:text-white truncate leading-tight">${item.name}</p>
                        <p class="text-sm font-bold text-gray-900 dark:text-white shrink-0">${formatMoney(item.price * item.qty)}</p>
                    </div>
                    <div class="flex items-center justify-between mt-1">
                        <p class="text-xs text-gray-500 dark:text-gray-400">${formatMoney(item.price)} each</p>
                        <div class="flex items-center gap-3 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 px-1 py-0.5">
                            <button onclick="updateQty(${index}, -1)" class="size-6 flex items-center justify-center text-gray-500 hover:text-red-500 transition-colors">
                                <span class="material-symbols-outlined text-sm">remove</span>
                            </button>
                            <span class="text-sm font-bold w-4 text-center text-gray-900 dark:text-white">${item.qty}</span>
                            <button onclick="updateQty(${index}, 1)" class="size-6 flex items-center justify-center text-gray-500 hover:text-green-500 transition-colors">
                                <span class="material-symbols-outlined text-sm">add</span>
                            </button>
                        </div>
                    </div>
                </div>
            `;
            container.appendChild(div);
        });

        const tax = subtotal * (TAX_RATE / 100);
        const total = subtotal + tax;

        document.getElementById('subtotal-display').innerText = formatMoney(subtotal);
        document.getElementById('tax-display').innerText = formatMoney(tax);
        document.getElementById('total-display').innerText = formatMoney(total);
        document.getElementById('mobile-total-preview').innerText = formatMoney(total);
    }

    function setPaymentMethod(method) {
        currentPaymentMethod = method;
        document.querySelectorAll('.payment-method-btn').forEach(btn => {
            if (btn.dataset.method === method) {
                btn.classList.add('active', 'bg-primary/10', 'text-primary', 'border-primary');
                btn.classList.remove('bg-gray-50', 'dark:bg-gray-800', 'text-gray-600', 'dark:text-gray-400', 'border-transparent');
            } else {
                btn.classList.remove('active', 'bg-primary/10', 'text-primary', 'border-primary');
                btn.classList.add('bg-gray-50', 'dark:bg-gray-800', 'text-gray-600', 'dark:text-gray-400', 'border-transparent');
            }
        });
    }

    async function processSale() {
        if (cart.length === 0) return;

        const btn = document.getElementById('checkout-btn');
        const originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<span class="material-symbols-outlined animate-spin">progress_activity</span> Processing...';

        try {
            const payload = {
                items: cart.map(item => ({ id: item.id, quantity: item.qty })),
                payment_method: currentPaymentMethod,
                customer_name: document.getElementById('cust-name').value,
                customer_email: document.getElementById('cust-email').value,
                customer_phone: document.getElementById('cust-phone').value,
            };

            const response = await fetch("{{ route('sales.store') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(payload)
            });

            const data = await response.json();

            if (data.success) {
                cart = [];
                updateCartUI();
                
                // Clear customer fields
                document.getElementById('cust-name').value = '';
                document.getElementById('cust-email').value = '';
                document.getElementById('cust-phone').value = '';

                // Show success modal
                window.modal.show({
                    title: 'Sale Successful',
                    message: 'Transaction completed. Would you like to view the receipt?',
                    type: 'success',
                    confirmText: 'View Receipt',
                    cancelText: 'New Sale',
                    onConfirm: () => {
                        window.location.href = `/sales/${data.sale_id}`;
                    }
                });
            } else {
                throw new Error(data.message || 'Unknown error');
            }

        } catch (error) {
            console.error(error);
            window.showAlert('Transaction Failed', error.message || 'Something went wrong. Please try again.');
        } finally {
            btn.disabled = false;
            btn.innerHTML = originalText;
        }
    }

    // --- Search & Filter Logic ---
    const searchInput = document.getElementById('search-input');
    const categoryFilter = document.getElementById('category-filter');
    const productGrid = document.getElementById('product-grid');
    const noResults = document.getElementById('no-results');

    function filterProducts() {
        const term = searchInput.value.toLowerCase();
        const category = categoryFilter.value;
        let visibleCount = 0;

        const products = document.querySelectorAll('.product-card');
        products.forEach(el => {
            const name = el.dataset.name.toLowerCase();
            const sku = el.dataset.sku.toLowerCase();
            const cat = el.dataset.category;

            const matchesSearch = name.includes(term) || sku.includes(term);
            const matchesCategory = category === '' || cat === category;

            if (matchesSearch && matchesCategory) {
                el.style.display = 'flex';
                visibleCount++;
            } else {
                el.style.display = 'none';
            }
        });

        if (visibleCount === 0) {
            noResults.classList.remove('hidden');
        } else {
            noResults.classList.add('hidden');
        }
    }

    searchInput.addEventListener('input', filterProducts);
    categoryFilter.addEventListener('change', filterProducts);

    // Initial Load
    updateCartUI();
</script>
@endpush
@endsection
