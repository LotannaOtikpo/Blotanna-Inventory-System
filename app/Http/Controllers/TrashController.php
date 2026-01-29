<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Sale;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class TrashController extends Controller
{
    public function index()
    {
        $products = Product::onlyTrashed()->with('category')->latest('deleted_at')->get();
        $customers = Customer::onlyTrashed()->latest('deleted_at')->get();
        $invoices = Invoice::onlyTrashed()->with('customer')->latest('deleted_at')->get();

        return view('settings.trash', compact('products', 'customers', 'invoices'));
    }

    public function restore(Request $request, $type, $id)
    {
        try {
            DB::beginTransaction();
            
            $model = $this->getModel($type);
            $item = $model::onlyTrashed()->findOrFail($id);
            $item->restore();

            if ($type === 'invoice' && $item->status === 'paid') {
                Sale::onlyTrashed()->where('transaction_id', $item->invoice_number)->restore();
            }

            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'Restored Item',
                'description' => "Restored {$type} (ID: {$id}) from trash.",
                'ip_address' => $request->ip()
            ]);

            DB::commit();
            return back()->with('success', ucfirst($type) . ' restored successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to restore item: ' . $e->getMessage());
        }
    }

    public function destroy(Request $request, $type, $id)
    {
        try {
            DB::beginTransaction();

            $model = $this->getModel($type);
            $item = $model::onlyTrashed()->findOrFail($id);
            
            if ($type === 'invoice') {
                $item->items()->delete(); 
                 if ($item->status === 'paid') {
                    Sale::withTrashed()->where('transaction_id', $item->invoice_number)->forceDelete();
                }
            }
            
            $item->forceDelete();
            
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'Permanently Deleted Item',
                'description' => "Permanently removed {$type} (ID: {$id}) from database.",
                'ip_address' => $request->ip()
            ]);

            DB::commit();
            return back()->with('success', ucfirst($type) . ' permanently deleted.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to delete item: ' . $e->getMessage());
        }
    }

    private function getModel($type)
    {
        return match ($type) {
            'product' => Product::class,
            'customer' => Customer::class,
            'invoice' => Invoice::class,
            default => abort(404),
        };
    }
}