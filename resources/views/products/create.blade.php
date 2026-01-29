@extends('layouts.app')

@section('content')
<header class="bg-white dark:bg-[#111318] border-b border-[#dbdfe6] dark:border-gray-800 px-8 py-4 flex items-center justify-between sticky top-0 z-10 transition-colors">
    <div>
        <h2 class="text-lg font-bold text-[#111318] dark:text-white">Add New Product</h2>
        <p class="text-sm text-[#616f89] dark:text-gray-400">Enter product details below</p>
    </div>
    <a href="{{ route('products.index') }}" class="text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white">Back to List</a>
</header>

<div class="p-8 max-w-3xl mx-auto">
    <form action="{{ route('products.store') }}" method="POST" enctype="multipart/form-data" class="bg-white dark:bg-[#1e232f] rounded-xl border border-[#dbdfe6] dark:border-gray-800 shadow-sm p-6 space-y-6 transition-colors">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-1">
                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Product Name <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" required class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:ring-primary focus:border-primary">
                @error('name') <p class="text-red-500 text-xs">{{ $message }}</p> @enderror
            </div>

            <div class="space-y-1">
                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">SKU <span class="text-red-500">*</span></label>
                <input type="text" name="sku" value="{{ old('sku') }}" required class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:ring-primary focus:border-primary">
                @error('sku') <p class="text-red-500 text-xs">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="space-y-1">
                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Category</label>
                <select name="category_id" class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:ring-primary focus:border-primary">
                    <option value="">Select Category</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="space-y-1">
                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Price <span class="text-red-500">*</span></label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 dark:text-gray-400">{{ \App\Models\Setting::where('key', 'currency_symbol')->value('value') ?? '$' }}</span>
                    <input type="number" step="0.01" name="price" value="{{ old('price') }}" required class="w-full pl-8 rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:ring-primary focus:border-primary">
                </div>
                @error('price') <p class="text-red-500 text-xs">{{ $message }}</p> @enderror
            </div>

            <div class="space-y-1">
                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Initial Quantity <span class="text-red-500">*</span></label>
                <input type="number" name="quantity" value="{{ old('quantity', 0) }}" required class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:ring-primary focus:border-primary">
                @error('quantity') <p class="text-red-500 text-xs">{{ $message }}</p> @enderror
            </div>
        </div>

        <div class="space-y-1">
            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Description</label>
            <textarea name="description" rows="3" class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white focus:ring-primary focus:border-primary">{{ old('description') }}</textarea>
        </div>

        <div class="space-y-1">
            <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Product Image</label>
            <input type="file" name="image" accept="image/*" class="w-full text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20">
            @error('image') <p class="text-red-500 text-xs">{{ $message }}</p> @enderror
        </div>

        <div class="pt-4 flex justify-end gap-3">
            <a href="{{ route('products.index') }}" class="px-5 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 font-medium hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">Cancel</a>
            <button type="submit" class="px-5 py-2.5 rounded-lg bg-primary text-white font-medium hover:bg-primary/90 shadow-sm transition-colors">Save Product</button>
        </div>
    </form>
</div>
@endsection