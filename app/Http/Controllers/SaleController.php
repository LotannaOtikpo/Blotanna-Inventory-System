<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\ActivityLog;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class SaleController extends Controller
{
    public function index(Request $request)
    {
        $query = Sale::with(['user', 'saleItems.product']);

        if ($request->has('search') && $request->search != '') {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('transaction_id', 'like', "%{$search}%")
                  ->orWhereHas('user', function($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%");
                  })
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_email', 'like', "%{$search}%")
                  ->orWhere('customer_phone', 'like', "%{$search}%")
                  ->orWhere('created_at', 'like', "%{$search}%");
            });
        }

        $sales = $query->latest()->paginate(10)->withQueryString();
        return view('sales.index', compact('sales'));
    }

    public function show(Sale $sale)
    {
        $sale->load(['user', 'saleItems.product']);
        $taxRate = ($sale->tax_amount / ($sale->total_amount - $sale->tax_amount)) * 100;
        // Basic protection against division by zero if total is 0 or it was tax free, 
        // though typically we use the stored amount. 
        // For display purposes, we can calculate it or just show the amount.
        return view('sales.show', compact('sale'));
    }

    public function create()
    {
        $products = Product::all();
        $categories = Category::all();
        $transactionId = '#TXN-' . strtoupper(Str::random(8));
        return view('sales.create', compact('products', 'categories', 'transactionId'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1|max:1000',
            'payment_method' => 'required|string|in:cash,card,transfer',
            'customer_name' => 'nullable|string|max:255',
            'customer_email' => 'nullable|email|max:255',
            'customer_phone' => 'nullable|string|max:20',
        ]);

        try {
            DB::beginTransaction();

            $totalAmount = 0;
            $saleItemsData = [];

            foreach ($request->items as $item) {
                $product = Product::lockForUpdate()->find($item['id']);
                
                if ($product->quantity < $item['quantity']) {
                    throw new \Exception("Insufficient stock for {$product->name}");
                }

                $product->decrement('quantity', $item['quantity']);

                $lineTotal = $product->price * $item['quantity'];
                $totalAmount += $lineTotal;

                $saleItemsData[] = [
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'price' => $product->price,
                ];
            }

            // Fetch dynamic tax rate
            $taxRate = Setting::getValue('tax_rate', 8);
            $tax = $totalAmount * ($taxRate / 100);
            $grandTotal = $totalAmount + $tax;

            $sale = Sale::create([
                'transaction_id' => '#TXN-' . strtoupper(Str::random(8)),
                'total_amount' => $grandTotal,
                'tax_amount' => $tax,
                'payment_method' => $request->payment_method,
                'user_id' => Auth::id(),
                'status' => 'paid',
                'customer_name' => $request->customer_name,
                'customer_email' => $request->customer_email,
                'customer_phone' => $request->customer_phone,
            ]);

            foreach ($saleItemsData as $data) {
                $sale->saleItems()->create($data);
            }
            
            // Log Sale
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'New Sale',
                'description' => "Processed sale {$sale->transaction_id} Amount: {$sale->total_amount}",
                'ip_address' => $request->ip()
            ]);

            DB::commit();

            return response()->json([
                'success' => true, 
                'message' => 'Sale completed successfully',
                'sale_id' => $sale->id
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }
}
