<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\MessageLog;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    /**
     * Display the dashboard with real data from database.
     * Papar dashboard dengan data sebenar dari database
     */
    public function index()
    {
        // Get total customers count from database
        $totalCustomers = Customer::count();
        
        // Get total message logs count
        $totalMessages = MessageLog::count();
        
        // Get unreplied messages count (assuming there's a status field)
        $unrepliedMessages = MessageLog::where('status', 'pending')->count();

        // Prepare dashboard statistics
        $dashboardStats = [
            'totalCustomers' => $totalCustomers,
            'totalMessages' => $totalMessages,
            'unrepliedMessages' => $unrepliedMessages
        ];

        return Inertia::render('Dashboard', [
            'stats' => $dashboardStats
        ]);
    }
}
