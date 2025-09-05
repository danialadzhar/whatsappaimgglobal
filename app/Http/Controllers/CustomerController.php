<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\MessageLog;
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
     * Get customer data from database by phone number for API.
     * Dapatkan data customer dari database berdasarkan phone number untuk API
     */
    public function getCustomerDataFromDB($phone_number)
    {
        try {
            // Find customer by phone number
            $customer = Customer::where('phone_number', $phone_number)->first();

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
                // Return false if customer not found
                return response()->json([
                    'success' => false,
                    'message' => 'Customer tidak ditemui dengan phone number: ' . $phone_number,
                    'data' => [
                        'customer' => null,
                        'found' => false,
                        'searched_phone' => $phone_number
                    ]
                ], 200);
            }
        } catch (\Exception $e) {
            // Return error response
            return response()->json([
                'success' => false,
                'message' => 'Gagal mencari customer',
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
                'message' => 'Validation error',
                'errors' => $e->errors(),
                'data' => null
            ], 422);
        } catch (\Exception $e) {
            // Return error response
            return response()->json([
                'success' => false,
                'message' => 'Gagal mencari customer',
                'data' => null
            ], 500);
        }
    }

    /**
     * Store message log for customer conversation.
     * Simpan log mesej untuk perbualan customer
     */
    public function storeMessageLog(Request $request)
    {
        try {
            // Validate input data
            $request->validate([
                'customer_messages' => 'required|string',
                'ai_messages' => 'required|string',
                'customer_id' => 'required|integer|exists:customers,id',
            ]);

            // Create new message log
            $messageLog = MessageLog::create([
                'customer_messages' => $request->customer_messages,
                'ai_messages' => $request->ai_messages,
                'customer_id' => $request->customer_id,
            ]);

            // Load customer relationship
            $messageLog->load('customer');

            // Return success response
            return response()->json([
                'success' => true,
                'message' => 'Message log berjaya disimpan!',
                'data' => [
                    'message_log' => $messageLog,
                    'customer' => $messageLog->customer
                ]
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Return validation error response
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors(),
                'data' => null
            ], 422);
        } catch (\Exception $e) {
            // Return error response
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan message log',
                'data' => null
            ], 500);
        }
    }

    /**
     * Get message logs data for dashboard table.
     * Dapatkan data message logs untuk dashboard table
     */
    public function getMessageLogs(Request $request)
    {
        try {
            // Get message logs with customer relationship
            $messageLogs = MessageLog::with('customer')
                ->orderBy('created_at', 'desc')
                ->limit(1) // Limit to 10 recent messages
                ->get();

            // Format data for table display
            $formattedData = $messageLogs->map(function ($log) {
                return [
                    'id' => $log->id,
                    'customer_name' => $log->customer->name ?? 'Unknown Customer',
                    'last_customer_message' => $log->customer_messages,
                    'created_at' => $log->created_at->format('d-m-Y'),
                    'created_at_raw' => $log->created_at
                ];
            });

            // Return success response
            return response()->json([
                'success' => true,
                'message' => 'Message logs berjaya diambil',
                'data' => $formattedData
            ], 200);
        } catch (\Exception $e) {
            // Return error response
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil message logs',
                'data' => []
            ], 500);
        }
    }
}
