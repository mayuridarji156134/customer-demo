<?php

namespace App\Http\Controllers\Api;

use App\Models\Contact;
use App\Models\Customer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ContactController extends Controller
{
    // List all contacts for a customer
    public function index(Customer $customer)
    {
        $contacts = $customer->contacts()->orderBy('id', 'desc')->get();
        return response()->json($contacts);
    }

    // Store a new contact for a customer
    public function store(Request $request, Customer $customer)
    {
        // Validate the request
        $request->validate([
            'first_name' => 'required|string|max:255',
        ]);

        // Create a new contact for the customer
        $contact = new Contact($request->only(['first_name', 'last_name']));
        $customer->contacts()->save($contact);

        return response()->json($contact, 201);
    }

    // Show a specific contact
    public function show(Contact $contact)
    {
        return response()->json($contact);
    }

    // Update an existing contact
    public function update(Request $request, Contact $contact)
    {
        // Validate the request
        $request->validate([
            'first_name' => 'required|string|max:255',
        ]);

        // Update the contact with new values
        $contact->update($request->only(['first_name', 'last_name']));

        return response()->json($contact, 200);
    }

    // Delete a contact
    public function destroy(Contact $contact)
    {
        $contact->delete();
        return response()->json(['message' => 'Contact deleted successfully'], 200);
    }
}
