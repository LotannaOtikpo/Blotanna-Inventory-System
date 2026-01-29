<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\ActivityLog;
use App\Models\Setting;
use App\Mail\InvoiceMail;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $query = Invoice::with('customer');

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where('invoice_number', 'like', "%{$search}%")
                  ->orWhereHas('customer', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
        }

        $invoices = $query->latest()->paginate(10);
        return view('invoices.index', compact('invoices'));
    }

    public function create()
    {
        $customers = Customer::all();
        $products = Product::select('id', 'name', 'price')->get();
        $invoiceNumber = 'INV-' . strtoupper(Str::random(8));
        return view('invoices.create', compact('customers', 'products', 'invoiceNumber'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'issue_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:issue_date',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'nullable|exists:products,id',
            'items.*.product_name' => 'required|string',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            $subtotal = 0;
            foreach ($request->items as $item) {
                $subtotal += ($item['quantity'] * $item['unit_price']);
            }
            
            // Fetch dynamic tax rate
            $taxRate = Setting::getValue('tax_rate', 8);
            $tax = $subtotal * ($taxRate / 100);
            $total = $subtotal + $tax;

            $invoice = Invoice::create([
                'invoice_number' => $request->invoice_number,
                'customer_id' => $request->customer_id,
                'issue_date' => $request->issue_date,
                'due_date' => $request->due_date,
                'subtotal' => $subtotal,
                'tax_amount' => $tax,
                'total_amount' => $total,
                'status' => 'draft',
                'notes' => $request->notes
            ]);

            foreach ($request->items as $item) {
                $invoice->items()->create([
                    'product_id' => $item['product_id'] ?? null,
                    'product_name' => $item['product_name'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total' => $item['quantity'] * $item['unit_price']
                ]);
            }
            
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'Created Invoice',
                'description' => "Generated invoice #{$invoice->invoice_number} for customer.",
                'ip_address' => $request->ip()
            ]);

            DB::commit();
            return redirect()->route('invoices.show', $invoice)->with('success', 'Invoice generated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to create invoice: ' . $e->getMessage());
        }
    }

    public function show(Invoice $invoice)
    {
        $invoice->load(['customer', 'items']);
        return view('invoices.show', compact('invoice'));
    }

    public function updateStatus(Request $request, Invoice $invoice)
    {
        $request->validate(['status' => 'required|in:draft,sent,paid,cancelled']);
        
        $oldStatus = $invoice->status;
        $newStatus = $request->status;

        if ($newStatus === 'paid' && $oldStatus !== 'paid') {
            try {
                DB::beginTransaction();

                $sale = Sale::create([
                    'transaction_id' => $invoice->invoice_number,
                    'total_amount' => $invoice->total_amount,
                    'tax_amount' => $invoice->tax_amount,
                    'payment_method' => 'bank_transfer',
                    'user_id' => Auth::id() ?? 1,
                    'status' => 'paid',
                    'customer_name' => $invoice->customer->name,
                    'customer_email' => $invoice->customer->email,
                    'customer_phone' => $invoice->customer->phone,
                    'created_at' => now(),
                ]);

                foreach ($invoice->items as $invItem) {
                    if ($invItem->product_id) {
                        $product = Product::lockForUpdate()->find($invItem->product_id);
                        if ($product) {
                            $product->decrement('quantity', $invItem->quantity);
                            SaleItem::create([
                                'sale_id' => $sale->id,
                                'product_id' => $product->id,
                                'quantity' => $invItem->quantity,
                                'price' => $invItem->unit_price,
                            ]);
                        }
                    }
                }

                $invoice->update(['status' => 'paid']);
                
                ActivityLog::create([
                    'user_id' => Auth::id(),
                    'action' => 'Invoice Paid',
                    'description' => "Marked invoice #{$invoice->invoice_number} as Paid. Stock updated.",
                    'ip_address' => $request->ip()
                ]);

                DB::commit();
                
                return back()->with('success', 'Invoice marked as Paid. Revenue recorded and stock updated.');

            } catch (\Exception $e) {
                DB::rollBack();
                return back()->with('error', 'Error updating status: ' . $e->getMessage());
            }
        }

        $invoice->update(['status' => $newStatus]);
        
        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'Updated Invoice Status',
            'description' => "Changed invoice #{$invoice->invoice_number} status to {$newStatus}.",
            'ip_address' => $request->ip()
        ]);

        return back()->with('success', 'Invoice status updated.');
    }

    public function sendEmail(Request $request, Invoice $invoice)
    {
        config(['queue.default' => 'sync']);

        $gmailConfig = [
            'transport' => 'smtp',
            'host' => env('EMAIL_HOST', 'smtp.gmail.com'),
            'port' => (int)env('EMAIL_PORT', 587),
            'encryption' => env('EMAIL_USE_TLS') ? 'tls' : 'ssl',
            'username' => env('EMAIL_HOST_USER'),
            'password' => env('EMAIL_HOST_PASSWORD'),
            'timeout' => null,
            'auth_mode' => null,
        ];

        config(['mail.mailers.gmail_runtime' => $gmailConfig]);
        
        $senderEmail = env('EMAIL_HOST_USER'); 
        config(['mail.from.address' => $senderEmail]);
        config(['mail.from.name' => env('EMAIL_FROM_NAME', 'Blotanna Nig Ltd')]);

        try {
            Mail::mailer('gmail_runtime')
                ->to($invoice->customer->email)
                ->send(new InvoiceMail($invoice));
            
            if($invoice->status === 'draft') {
                $invoice->update(['status' => 'sent']);
            }
            
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'Sent Invoice Email',
                'description' => "Emailed invoice #{$invoice->invoice_number} to {$invoice->customer->email}",
                'ip_address' => $request->ip()
            ]);
            
            return back()->with('success', 'Invoice successfully sent to ' . $invoice->customer->email);

        } catch (\Exception $e) {
            Log::error('Mail Failure: ' . $e->getMessage());
            
            if (str_contains($e->getMessage(), '127.0.0.1')) {
                return back()->with('error', 'Connection Error: Server tried to use localhost.');
            }

            return back()->with('error', 'Email Sending Failed: ' . $e->getMessage());
        }
    }

    public function destroy(Request $request, Invoice $invoice)
    {
        try {
            DB::beginTransaction();

            $id = $invoice->invoice_number;

            if ($invoice->status === 'paid') {
                $sale = Sale::where('transaction_id', $invoice->invoice_number)->first();
                if ($sale) {
                    $sale->delete();
                }
            }

            $invoice->delete();
            
            ActivityLog::create([
                'user_id' => Auth::id(),
                'action' => 'Deleted Invoice',
                'description' => "Moved invoice #{$id} to trash.",
                'ip_address' => $request->ip()
            ]);
            
            DB::commit();
            return redirect()->route('invoices.index')->with('success', 'Invoice deleted successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to delete invoice: ' . $e->getMessage());
        }
    }
}
