<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\MessageLog;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;

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

        // Calculate response rate percentage
        $totalMessages = MessageLog::count();
        $responseRate = 0;

        if ($totalMessages > 0) {
            // Count messages with AI response (ai_messages is not null and not empty)
            $messagesWithResponse = MessageLog::whereNotNull('ai_messages')
                ->where('ai_messages', '!=', '')
                ->get();

            $goodResponses = 0;
            foreach ($messagesWithResponse as $message) {
                // Calculate response time in minutes
                $responseTimeMinutes = $message->updated_at->diffInMinutes($message->created_at);

                // If response time is 1 minute or less, it's a good response
                if ($responseTimeMinutes <= 1) {
                    $goodResponses++;
                }
            }

            // Calculate response rate percentage
            $responseRate = ($goodResponses / $totalMessages) * 100;
        }

        // Prepare dashboard statistics
        $dashboardStats = [
            'totalCustomers' => $totalCustomers,
            'totalMessages' => $totalMessages,
            'responseRate' => $responseRate
        ];

        // Conversation analytics: today, last 7 days series, and weekly total
        $today = Carbon::today();
        $startDate = (clone $today)->subDays(6);

        // Get counts grouped by date within the last 7 days
        $counts = MessageLog::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->whereBetween('created_at', [$startDate->startOfDay(), $today->endOfDay()])
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        $series = [];
        $labels = [];
        $cursor = $startDate->copy();
        while ($cursor->lte($today)) {
            $dateKey = $cursor->toDateString();
            $series[] = (int) ($counts[$dateKey]->count ?? 0);
            $labels[] = $cursor->format('D'); // Mon, Tue, ...
            $cursor->addDay();
        }

        $todayCount = MessageLog::whereDate('created_at', $today)->count();
        $weeklyTotal = array_sum($series);

        return Inertia::render('Dashboard', [
            'stats' => $dashboardStats,
            'conversation' => [
                'today' => $todayCount,
                'weeklyTotal' => $weeklyTotal,
                'series' => $series,
                'labels' => $labels,
            ],
        ]);
    }
}
