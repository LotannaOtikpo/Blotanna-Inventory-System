
@extends('layouts.app')

@section('content')
<div x-data="{ showCategoryModal: false }" class="h-full flex flex-col">
    <header class="bg-white dark:bg-[#111318] border-b border-[#dbdfe6] dark:border-gray-800 px-4 md:px-8 py-6 sticky top-0 z-10 transition-colors">
        <div class="flex flex-col md:flex-row justify-between md:items-center gap-4">
            <div>
                <h2 class="text-2xl md:text-3xl font-black tracking-tight text-[#111318] dark:text-white">Products</h2>
                <p class="text-sm text-[#616f89] dark:text-gray-400 mt-1">Manage your inventory, prices, and stock levels.</p>
            </div>
            <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
                <button @click="showCategoryModal = true" class="bg-white dark:bg-[#1e232f] text-gray-700 dark:text-gray-200 border border-gray-300 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800 flex items-center justify-center gap-2 px-5 py-2.5 rounded-lg font-bold text-sm transition-all shadow-sm w-full sm:w-auto">
                    <span class="material-symbols-outlined text-[20px]">category</span>
                    <span>Add Category</span>
                </button>
                <a href="{{ route('products.create') }}" class="bg-primary hover:bg-primary/90 text-white flex items-center justify-center gap-2 px-5 py-2.5 rounded-lg font-bold text-sm transition-all shadow-sm w-full sm:w-auto">
                    <span class="material-symbols-outlined text-[20px]">add</span>
                    <span>Add New Product</span>
                </a>
            </div>
        </div>
    </header>

    <div class="flex-1 overflow-y-auto p-4 md:p-8 space-y-6">
        <!-- Filters -->
        <div class="bg-white dark:bg-[#1e232f] p-2 rounded-xl shadow-sm border border-[#dbdfe6] dark:border-gray-800 transition-colors">
            <form action="{{ route('products.index') }}" method="GET" class="flex flex-col md:flex-row items-center gap-2">
                <div class="relative flex-1 w-full">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-gray-400">
                        <span class="material-symbols-outlined text-[20px]">search</span>
                    </div>
                    <input name="search" value="{{ request('search') }}" class="block w-full pl-11 pr-4 py-2.5 text-sm border-none bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white rounded-lg focus:ring-2 focus:ring-primary/20 placeholder:text-gray-400" placeholder="Search by name, SKU..." type="text"/>
                </div>
                
                <div class="w-full md:w-48">
                    <select name="category_id" onchange="this.form.submit()" class="w-full border-none bg-gray-50 dark:bg-gray-900 text-gray-600 dark:text-gray-300 rounded-lg py-2.5 px-4 text-sm focus:ring-2 focus:ring-primary/20 cursor-pointer">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <button type="submit" class="w-full md:w-auto px-4 py-2.5 bg-gray-900 dark:bg-gray-700 text-white rounded-lg text-sm font-medium hover:bg-gray-800 dark:hover:bg-gray-600 transition-colors">
                    Search
                </button>
                
                @if(request('search') || request('category_id'))
                <a href="{{ route('products.index') }}" class="flex items-center justify-center gap-1 px-4 py-2.5 text-sm font-medium text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-900/20 rounded-lg hover:bg-red-100 dark:hover:bg-red-900/30 transition-colors w-full md:w-auto">
                    <span class="material-symbols-outlined text-[18px]">close</span>
                    <span>Reset</span>
                </a>
                @endif
            </form>
        </div>

        <!-- Table -->
        <div id="products-table" class="bg-white dark:bg-[#1e232f] rounded-xl shadow-sm border border-[#dbdfe6] dark:border-gray-800 overflow-hidden transition-colors">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse whitespace-nowrap">
                    <thead>
                        <tr class="bg-gray-50/50 dark:bg-gray-800 border-b border-[#dbdfe6] dark:border-gray-700">
                            <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider w-16">Image</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Product Name</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">SKU</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Price</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Quantity</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-4 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse($products as $product)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors group">
                            <td class="px-6 py-4">
                                <div class="w-12 h-12 rounded-lg bg-gray-100 dark:bg-gray-700 flex items-center justify-center overflow-hidden shrink-0">
                                    @if($product->image_path)
                                        <img src="{{ $product->image_url }}" class="w-full h-full object-cover" onerror="this.onerror=null; this.parentElement.innerHTML='<span class=\'material-symbols-outlined text-gray-400\'>image_not_supported</span>';">
                                    @else
                                        <span class="material-symbols-outlined text-gray-400">image</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-semibold text-gray-900 dark:text-white">{{ $product->name }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ $product->category->name ?? 'Uncategorized' }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300 font-mono">{{ $product->sku }}</td>
                            <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white">{{ \App\Models\Setting::where('key', 'currency_symbol')->value('value') ?? '$' }}{{ number_format($product->price, 2) }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">{{ $product->quantity }}</td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $product->status_color }}">
                                    {{ $product->stock_status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('products.edit', $product) }}" class="p-1.5 text-gray-400 hover:text-primary transition-colors" title="Edit">
                                        <span class="material-symbols-outlined text-[20px]">edit</span>
                                    </a>
                                    <form action="{{ route('products.destroy', $product) }}" method="POST" class="delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" onclick="confirmDelete(this.form)" class="p-1.5 text-gray-400 hover:text-red-500 transition-colors" title="Delete">
                                            <span class="material-symbols-outlined text-[20px]">delete</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                                <span class="material-symbols-outlined text-4xl mb-2 text-gray-300 dark:text-gray-600">inventory_2</span>
                                <p>No products found matching your search.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700">
                {{ $products->links() }}
            </div>
        </div>
    </div>

    <!-- Category Modal -->
    <div x-show="showCategoryModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <!-- Backdrop -->
        <div x-show="showCategoryModal" x-transition.opacity class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm"></div>

        <div x-show="showCategoryModal" x-transition.scale class="relative min-h-screen flex items-center justify-center p-4">
            <div @click.away="showCategoryModal = false" class="relative transform overflow-hidden rounded-xl bg-white dark:bg-[#1e232f] text-left shadow-xl transition-all w-full max-w-md border border-gray-200 dark:border-gray-800">
                <form action="{{ route('categories.store') }}" method="POST">
                    @csrf
                    <div class="p-6">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Add New Category</h3>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Category Name</label>
                            <input type="text" name="name" required class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:ring-primary focus:border-primary placeholder-gray-400" placeholder="e.g., Electronics">
                        </div>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-800/50 px-6 py-4 flex flex-row-reverse gap-3">
                        <button type="submit" class="inline-flex justify-center rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary/90 transition-colors">Save Category</button>
                        <button type="button" @click="showCategoryModal = false" class="inline-flex justify-center rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-transparent px-4 py-2 text-sm font-semibold text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function confirmDelete(form) {
        window.showConfirm('Delete Product?', 'Are you sure you want to delete this product? This action cannot be undone.')
            .then(result => {
                if(result) form.submit();
            });
    }

    document.addEventListener('DOMContentLoaded', function() {
        const container = document.getElementById('products-table');
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
                            const newContent = doc.getElementById('products-table');
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
