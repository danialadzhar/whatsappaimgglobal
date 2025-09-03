<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CustomerController extends Controller
{
    /**
     * Display a listing of customers.
     * Papar senarai pelanggan dari database
     */
    public function index()
    {
        $customers = Customer::orderBy('created_at', 'desc')->get();

        return Inertia::render('Customers/Index', [
            'customers' => $customers
        ]);
    }

    /**
     * Store a newly created customer in storage.
     * Simpan customer baru ke database
     */
    public function store(Request $request)
    {
        // Validate input data
        $request->validate([
            'name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
        ]);

        // Create new customer
        $customer = Customer::create([
            'name' => $request->name,
            'phone_number' => $request->phone_number,
        ]);

        // Return success response
        return response()->json([
            'success' => true,
            'message' => 'Customer berjaya ditambah!',
            'customer' => $customer
        ], 201);
    }

    /**
     * Get customer data from database for API.
     * Dapatkan data customer dari database untuk API
     */
    public function getCustomerDataFromDB(Request $request)
    {
        try {
            // Get all customers with optional search parameter
            $query = Customer::query();

            // Apply search filter if provided
            if ($request->has('search') && !empty($request->search)) {
                $searchTerm = $request->search;
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('name', 'like', '%' . $searchTerm . '%')
                        ->orWhere('phone_number', 'like', '%' . $searchTerm . '%');
                });
            }

            // Apply pagination if requested
            $perPage = $request->get('per_page', 15);
            $customers = $query->orderBy('created_at', 'desc')->paginate($perPage);

            // Return API response
            return response()->json([
                'success' => true,
                'message' => 'Data customer berjaya diambil',
                'data' => [
                    'customers' => $customers->items(),
                    'pagination' => [
                        'current_page' => $customers->currentPage(),
                        'last_page' => $customers->lastPage(),
                        'per_page' => $customers->perPage(),
                        'total' => $customers->total(),
                        'from' => $customers->firstItem(),
                        'to' => $customers->lastItem(),
                    ]
                ]
            ], 200);
        } catch (\Exception $e) {
            // Return error response
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data customer: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    /**
     * Get customer by phone number for API.
     * Dapatkan customer berdasarkan phone number untuk API
     */
    public function getCustomerByPhone(Request $request)
    {
        try {
            // Validate phone number parameter
            $request->validate([
                'phone_number' => 'required|string|max:20',
            ]);

            $phoneNumber = $request->phone_number;

            // Find customer by exact phone number match
            $customer = Customer::where('phone_number', $phoneNumber)->first();

            if ($customer) {
                // Return customer data if found
                return response()->json([
                    'success' => true,
                    'message' => 'Customer ditemui',
                    'data' => [
                        'customer' => $customer,
                        'found' => true
                    ]
                ], 200);
            } else {
                // Return not found response
                return response()->json([
                    'success' => true,
                    'message' => 'Customer tidak ditemui dengan phone number: ' . $phoneNumber,
                    'data' => [
                        'customer' => null,
                        'found' => false,
                        'searched_phone' => $phoneNumber
                    ]
                ], 404);
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Return validation error response
            return response()->json([
                'success' => false,
                'message' => 'Validation error: ' . $e->getMessage(),
                'errors' => $e->errors(),
                'data' => null
            ], 422);
        } catch (\Exception $e) {
            // Return error response
            return response()->json([
                'success' => false,
                'message' => 'Gagal mencari customer: ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }
}
