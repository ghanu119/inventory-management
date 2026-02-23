<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCustomerRequest;
use App\Models\Customer;
use App\Models\Invoice;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = Customer::query()->withCount('invoices');

        if ($request->has('search')) {
            $query->searchByContact($request->search);
        }

        $customers = $query->latest()->paginate(15);

        return view('customers.index', compact('customers'));
    }

    public function create()
    {
        return view('customers.create');
    }

    public function store(StoreCustomerRequest $request)
    {
        Customer::create($request->validated());

        return redirect()->route('customers.index')
            ->with('success', 'Customer created successfully.');
    }

    public function edit(Customer $customer)
    {
        return view('customers.edit', compact('customer'));
    }

    public function update(StoreCustomerRequest $request, Customer $customer)
    {
        $customer->update($request->validated());

        return redirect()->route('customers.index')
            ->with('success', 'Customer updated successfully.');
    }

    public function destroy(Customer $customer)
    {
        if ($customer->invoices()->exists()) {
            return redirect()->back()
                ->with('error', 'This customer cannot be deleted because one or more invoices exist for them. Delete or reassign those invoices first.');
        }

        $customer->delete();

return redirect()->route('customers.index')
                ->with('success', 'Customer deleted successfully.');
    }

    /**
     * Show truncate customers form. If invoices exist, show error and instructions to truncate invoices first.
     */
    public function truncateForm()
    {
        $invoicesCount = Invoice::count();
        $customersCount = Customer::count();
        $canTruncate = $invoicesCount === 0;

        return view('customers.truncate', [
            'invoicesCount' => $invoicesCount,
            'customersCount' => $customersCount,
            'canTruncate' => $canTruncate,
        ]);
    }

    /**
     * Truncate all customers. Only allowed when no invoices exist.
     */
    public function truncate(Request $request)
    {
        $request->validate([
            'confirm' => ['required', 'in:1'],
        ], [
            'confirm.in' => __('You must confirm that you want to truncate all customers.'),
        ]);

        if (Invoice::count() > 0) {
            return redirect()->route('customers.truncate')
                ->with('error', __('Truncate not possible: invoices still exist. Please truncate invoices first, then return here to truncate customers.'));
        }

        $count = Customer::count();
        Customer::query()->delete();

        return redirect()->route('customers.truncate')
            ->with('success', __(':count customer(s) truncated.', ['count' => $count]));
    }
}
