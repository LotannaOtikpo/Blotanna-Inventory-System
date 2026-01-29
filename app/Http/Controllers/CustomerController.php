<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = Customer::query();

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
        }

        $customers = $query->latest()->paginate(10);
        return view('customers.index', compact('customers'));
    }

    public function store(Request $request)
    {
        $this->validateCustomer($request);

        $customer = Customer::create($request->all());

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'Created Customer',
            'description' => "Added new customer: {$customer->name} ({$customer->email})",
            'ip_address' => $request->ip()
        ]);

        return back()->with('success', 'Customer added successfully.');
    }

    public function update(Request $request, Customer $customer)
    {
        $this->validateCustomer($request, $customer->id);

        $customer->update($request->all());

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'Updated Customer',
            'description' => "Updated details for customer: {$customer->name}",
            'ip_address' => $request->ip()
        ]);

        return back()->with('success', 'Customer updated successfully.');
    }

    public function destroy(Request $request, Customer $customer)
    {
        if($customer->invoices()->whereNotIn('status', ['paid', 'cancelled'])->exists()) {
            return back()->with('error', 'Cannot delete customer. There are unpaid or pending invoices attached to this account.');
        }
        
        $name = $customer->name;
        $customer->delete();

        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => 'Deleted Customer',
            'description' => "Moved customer to trash: {$name}",
            'ip_address' => $request->ip()
        ]);

        return back()->with('success', 'Customer deleted successfully.');
    }

    private function validateCustomer(Request $request, $ignoreId = null)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                'unique:customers,email' . ($ignoreId ? ",$ignoreId" : ''),
                function ($attribute, $value, $fail) {
                    $domain = substr(strrchr($value, "@"), 1);
                    if ($domain && !checkdnsrr($domain, 'MX') && !checkdnsrr($domain, 'A')) {
                        $fail("The domain \"$domain\" does not exist or cannot receive emails. Please check for typos.");
                    }
                },
            ],
        ], [
            'email.unique' => 'This email address is already registered to another customer.',
            'email.email' => 'Please enter a valid email address format.',
        ]);

        if ($validator->fails()) {
            $validator->validate();
        }
    }
}