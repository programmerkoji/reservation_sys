<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $selectedCustomerId = $request->integer('customer_id');
        $selectedCustomer = null;
        if ($selectedCustomerId !== null) {
            $selectedCustomer = Customer::query()
                ->with(['reservations' => function ($query) {
                    $query->orderByDesc('start_at');
                }])
                ->find($selectedCustomerId);
        }

        return view('dashboard.customers.index', [
            'customers' => Customer::query()->orderBy('name')->paginate(50),
            'selectedCustomer' => $selectedCustomer,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'memo' => ['nullable', 'string'],
        ]);

        $customer = Customer::query()->create([
            'name' => $validated['name'],
            'phone' => trim((string) ($validated['phone'] ?? '')),
            'memo' => $validated['memo'] ?? null,
        ]);

        return redirect()->route('dashboard.customers.index', ['customer_id' => $customer->id]);
    }

    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'memo' => ['nullable', 'string'],
        ]);

        $customer->update([
            'name' => $validated['name'],
            'phone' => trim((string) ($validated['phone'] ?? '')),
            'memo' => $validated['memo'] ?? null,
        ]);

        return redirect()->route('dashboard.customers.index', ['customer_id' => $customer->id]);
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();

        return redirect()->route('dashboard.customers.index');
    }
}
