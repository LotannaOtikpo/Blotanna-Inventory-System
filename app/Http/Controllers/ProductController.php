<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('category');
        $categories = Category::all();
        
        if($request->has('search') && $request->search != '') {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        if($request->has('category_id') && $request->category_id != '') {
            $query->where('category_id', $request->category_id);
        }

        $products = $query->latest()->paginate(10)->withQueryString();
        return view('products.index', compact('products', 'categories'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|min:3|max:255',
            'sku' => 'required|string|alpha_dash|max:50|unique:products',
            'category_id' => 'nullable|exists:categories,id',
            'price' => 'required|numeric|min:0.01|max:999999.99',
            'quantity' => 'required|integer|min:0|max:100000',
            'description' => 'nullable|string|max:1000',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048'
        ]);

        $data = $request->except('image');

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('products', 'public');
            $data['image_path'] = $path;
        }

        $product = Product::create($data);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'Created Product',
            'description' => "Added new product: {$product->name} (SKU: {$product->sku})",
            'ip_address' => $request->ip()
        ]);

        return redirect()->route('products.index')->with('success', 'Product created successfully.');
    }

    public function edit(Product $product)
    {
        $categories = Category::all();
        return view('products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string|min:3|max:255',
            'sku' => 'required|string|alpha_dash|max:50|unique:products,sku,'.$product->id,
            'category_id' => 'nullable|exists:categories,id',
            'price' => 'required|numeric|min:0.01|max:999999.99',
            'quantity' => 'required|integer|min:0|max:100000',
            'description' => 'nullable|string|max:1000',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048'
        ]);

        $data = $request->except('image');

        if ($request->hasFile('image')) {
            if ($product->image_path) {
                Storage::disk('public')->delete($product->image_path);
            }
            $path = $request->file('image')->store('products', 'public');
            $data['image_path'] = $path;
        }

        $product->update($data);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'Updated Product',
            'description' => "Updated product details: {$product->name}",
            'ip_address' => $request->ip()
        ]);

        return redirect()->route('products.index')->with('success', 'Product updated successfully.');
    }

    public function destroy(Request $request, Product $product)
    {
        $name = $product->name;
        if ($product->image_path) {
            Storage::disk('public')->delete($product->image_path);
        }
        $product->delete();

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'Deleted Product',
            'description' => "Moved product to trash: {$name}",
            'ip_address' => $request->ip()
        ]);

        return redirect()->route('products.index')->with('success', 'Product deleted successfully.');
    }
}