<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\MessageLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Illuminate\Support\Str;

class ChatController extends Controller
{
    /**
     * Display chat interface
     * Paparan interface chat
     */
    public function index()
    {
        // Ambil semua customers dengan message logs terkini
        $conversations = Customer::with(['messageLogs' => function ($query) {
            $query->latest()->take(1);
        }])
            ->whereHas('messageLogs')
            ->withCount('messageLogs')
            ->orderByDesc('updated_at')
            ->get()
            ->map(function ($customer) {
                $lastMessage = $customer->messageLogs->first();
                // Tentukan mesej terakhir yang bukan kosong
                $lastMessageText = 'No messages yet';
                if ($lastMessage) {
                    if (!empty($lastMessage->ai_messages)) {
                        $lastMessageText = $lastMessage->ai_messages;
                    } elseif (!empty($lastMessage->customer_messages)) {
                        $lastMessageText = $lastMessage->customer_messages;
                    }
                }

                return [
                    'id' => $customer->id,
                    'name' => $customer->name,
                    'phone_number' => $customer->phone_number,
                    'avatar' => $this->generateAvatar($customer->name),
                    // Guna mesej terakhir yang wujud (AI dahulu, kemudian customer)
                    'last_message' => $lastMessageText,
                    'last_message_time' => $lastMessage ? $lastMessage->created_at->format('H:i') : '',
                    'unread_count' => 0, // Will implement later
                    'is_typing' => false,
                    'message_count' => $customer->message_logs_count,
                    'status' => 'online' // Will implement later
                ];
            });

        return Inertia::render('Chat/Index', [
            'conversations' => $conversations
        ]);
    }

    /**
     * Get messages for specific conversation
     * Dapatkan mesej untuk conversation tertentu
     */
    public function getMessages(Request $request, $customerId)
    {
        $customer = Customer::findOrFail($customerId);

        $messages = MessageLog::where('customer_id', $customerId)
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($log) use ($customer) {
                return [
                    'id' => $log->id,
                    'customer_message' => [
                        'id' => $log->id . '_customer',
                        'text' => $log->customer_messages,
                        'time' => $log->created_at->format('H:i'),
                        'sender' => 'customer',
                        'sender_name' => $customer->name,
                        'avatar' => $this->generateAvatar($customer->name)
                    ],
                    'ai_message' => [
                        'id' => $log->id . '_ai',
                        'text' => $log->ai_messages,
                        'time' => $log->created_at->addSeconds(5)->format('H:i'),
                        'sender' => 'ai',
                        'sender_name' => 'AI Assistant',
                        'avatar' => '🤖'
                    ],
                    'created_at' => $log->created_at
                ];
            });

        return response()->json([
            'success' => true,
            'customer' => [
                'id' => $customer->id,
                'name' => $customer->name,
                'phone_number' => $customer->phone_number,
                'avatar' => $this->generateAvatar($customer->name)
            ],
            'messages' => $messages
        ]);
    }

    /**
     * Send new message
     * Hantar mesej baru
     */
    public function sendMessage(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'message' => 'required|string|max:1000',
            'sender' => 'required|in:customer,ai'
        ]);

        $customer = Customer::findOrFail($request->customer_id);

        // Only store exactly what is sent, tanpa auto-generate AI reply
        // Jika mesej dihantar oleh sistem/AI, simpan di ai_messages dan customer_messages kosong
        // Jika mesej dihantar oleh customer, simpan di customer_messages dan ai_messages kosong
        $messageLog = MessageLog::create([
            'customer_id' => $request->customer_id,
            'customer_messages' => $request->sender === 'customer' ? $request->message : '',
            'ai_messages' => $request->sender === 'ai' ? $request->message : '',
        ]);

        // Update customer timestamp
        $customer->touch();

        if (!empty($messageLog->ai_messages)) {
            try {
                Http::post('https://automation.mgglobalhq.com/webhook-test/0a02dc45-b798-419e-b7f7-4090c26cb830', [
                    'ai_messages' => $messageLog->ai_messages,
                    'phone_number' => $customer->phone_number,
                ]);
            } catch (\Throwable $exception) {
                Log::warning('Failed to send AI message webhook', [
                    'message_log_id' => $messageLog->id,
                    'customer_id' => $customer->id,
                    'error' => $exception->getMessage(),
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Message sent successfully',
            'data' => [
                'id' => $messageLog->id,
                // Hanya kembalikan mesej yang dihantar (single payload)
                'message' => $request->sender === 'ai' ? [
                    'id' => $messageLog->id . '_ai',
                    'text' => $messageLog->ai_messages,
                    'time' => $messageLog->created_at->format('H:i'),
                    'sender' => 'ai',
                    'sender_name' => 'AI Assistant',
                    'avatar' => '🤖'
                ] : [
                    'id' => $messageLog->id . '_customer',
                    'text' => $messageLog->customer_messages,
                    'time' => $messageLog->created_at->format('H:i'),
                    'sender' => 'customer',
                    'sender_name' => $customer->name,
                    'avatar' => $this->generateAvatar($customer->name)
                ]
            ]
        ]);
    }

    /**
     * Generate avatar initials
     * Hasilkan avatar initials
     */
    private function generateAvatar($name)
    {
        $words = Str::of($name)->explode(' ');
        $initials = '';

        foreach ($words as $word) {
            $initials .= Str::upper(Str::substr($word, 0, 1));
            if (Str::length($initials) >= 2) break;
        }

        return $initials ?: Str::upper(Str::substr($name, 0, 2));
    }
}
