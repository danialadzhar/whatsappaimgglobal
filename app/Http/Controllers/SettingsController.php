<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\AIActivation;

class SettingsController extends Controller
{
    /**
     * Display settings page
     * Papar halaman settings
     */
    public function index()
    {
        // Get current chatbot status from database
        // Ambil status chatbot semasa dari database
        $chatbotActive = AIActivation::getChatbotStatus();

        return Inertia::render('Settings/Index', [
            'chatbotActive' => $chatbotActive
        ]);
    }

    /**
     * Toggle chatbot AI status
     * Toggle status chatbot AI
     */
    public function toggleChatbot(Request $request)
    {
        try {
            // Validate input
            // Validasi input
            $request->validate([
                'active' => 'required|boolean'
            ]);

            // Toggle chatbot status in database
            // Toggle status chatbot dalam database
            $activation = AIActivation::toggleChatbotStatus($request->active);

            // Return success response
            // Return response berjaya
            return response()->json([
                'success' => true,
                'message' => $request->active ? 'Chatbot AI diaktifkan!' : 'Chatbot AI dinyahaktifkan!',
                'chatbot_active' => $activation->is_active,
                'last_updated' => $activation->last_updated_at->format('Y-m-d H:i:s')
            ], 200);
        } catch (\Exception $e) {
            // Return error response
            // Return response error
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengemas kini status chatbot',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get current chatbot status
     * Dapatkan status chatbot semasa
     */
    public function getChatbotStatus()
    {
        try {
            // Get current chatbot status from database
            // Ambil status chatbot semasa dari database
            $chatbotActive = AIActivation::getChatbotStatus();

            // Get full activation record for additional info
            // Ambil record lengkap untuk maklumat tambahan
            $activation = AIActivation::where('name', 'chatbot')->first();

            return response()->json([
                'success' => true,
                'chatbot_active' => $chatbotActive,
                'last_updated' => $activation ? $activation->last_updated_at?->format('Y-m-d H:i:s') : null
            ], 200);
        } catch (\Exception $e) {
            // Return error response
            // Return response error
            return response()->json([
                'success' => false,
                'message' => 'Gagal mendapatkan status chatbot',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display e-commerce settings page
     * Papar halaman settings e-commerce
     */
    public function ecommerceSettings()
    {
        // Placeholder for e-commerce settings
        // Will be implemented with database storage later
        $settings = [
            'countdown_enabled' => true,
            'countdown_days' => 3,
            'countdown_hours' => 11,
            'countdown_minutes' => 31,
            'urgency_text' => '🔥 TAWARAN TERHAD! Promosi Ansuran Berakhir Dalam:',
            'background_color' => '#1f2937',
        ];

        return Inertia::render('Settings/Index', [
            'settings' => $settings
        ]);
    }
}
