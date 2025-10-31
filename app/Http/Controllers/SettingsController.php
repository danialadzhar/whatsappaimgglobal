<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\AIActivation;
use App\Models\EcommerceSetting;

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
        // Dapatkan settings dari database atau guna default jika tiada
        $record = EcommerceSetting::query()->latest('id')->first();
        $settings = [
            'countdown_enabled' => $record->countdown_enabled ?? true,
            'countdown_days' => $record->countdown_days ?? 3,
            'countdown_hours' => $record->countdown_hours ?? 11,
            'countdown_minutes' => $record->countdown_minutes ?? 31,
            'urgency_text' => $record->urgency_text ?? '🔥 TAWARAN TERHAD! Promosi Ansuran Berakhir Dalam:',
            'background_color' => $record->background_color ?? '#1f2937',
        ];

        return Inertia::render('Settings/Index', [
            'settings' => $settings
        ]);
    }

    /**
     * Simpan e-commerce settings melalui API
     */
    public function saveEcommerceSettings(Request $request)
    {
        try {
            // Validasi input
            $data = $request->validate([
                'countdown_enabled' => 'required|boolean',
                'countdown_days' => 'required|integer|min:0|max:365',
                'countdown_hours' => 'required|integer|min:0|max:23',
                'countdown_minutes' => 'required|integer|min:0|max:59',
                'urgency_text' => 'required|string|max:1000',
                'background_color' => 'required|string|max:20',
            ]);

            // Cipta atau kemas kini rekod terkini (single settings)
            $record = EcommerceSetting::query()->latest('id')->first();
            if ($record) {
                $record->update($data);
            } else {
                $record = EcommerceSetting::create($data);
            }

            return response()->json([
                'success' => true,
                'message' => 'E-commerce settings berjaya disimpan',
                'settings' => $record->only([
                    'countdown_enabled','countdown_days','countdown_hours','countdown_minutes','urgency_text','background_color'
                ]),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan e-commerce settings',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Dapatkan e-commerce settings melalui API
     */
    public function getEcommerceSettings()
    {
        $record = EcommerceSetting::query()->latest('id')->first();
        $settings = [
            'countdown_enabled' => $record->countdown_enabled ?? true,
            'countdown_days' => $record->countdown_days ?? 3,
            'countdown_hours' => $record->countdown_hours ?? 11,
            'countdown_minutes' => $record->countdown_minutes ?? 31,
            'urgency_text' => $record->urgency_text ?? '🔥 TAWARAN TERHAD! Promosi Ansuran Berakhir Dalam:',
            'background_color' => $record->background_color ?? '#1f2937',
        ];

        return response()->json([
            'success' => true,
            'settings' => $settings,
        ]);
    }
}
