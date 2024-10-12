<?php

namespace App\Http\Controllers\Api;

use App\Models\Customer;
use App\Models\Contact;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        // Fetch customers with search and category filters
        $query = Customer::with('category')->withCount('contacts');

        if ($request->has('search')) {
            // Group the 'or' conditions inside a closure
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('reference', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->has('category_id') && $request->category_id != '') {
            $query->where('category_id', $request->category_id);
        }
        $query->orderBy('created_at', 'desc');
        $customers = $query->paginate(5);
        return response()->json(['customers' => $customers]);
    }


    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'reference' => 'required|string|max:255',
            'category_id' => 'required|exists:customer_categories,id', 
            'start_date' => 'required|date',
            'description' => 'nullable|string',
        ]);

        $customer = Customer::create($validatedData);
        return response()->json($customer);
    }

    public function show(Customer $customer)
    {
        // Fetch specific customer with contacts
        return $customer->load('contacts');
    }

    public function update(Request $request, Customer $customer)
    {
         $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'reference' => 'required|string|max:255',
            'category_id' => 'required|exists:customer_categories,id', // Ensure category_id is valid
            'start_date' => 'required|date',
            'description' => 'nullable|string',
        ]);
        $customer->update($validatedData);
        return response()->json($customer);
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();
        return response()->json(['message' => 'Customer deleted successfully']);
    }
}
