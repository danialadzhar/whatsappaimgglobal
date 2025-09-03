<?php

namespace App\Http\Controllers;

use App\Models\FAQ;
use Exception;
use Illuminate\Http\Request;
use Inertia\Inertia;

class FAQController extends Controller
{
    /**
     * Display FAQ page
     * Display FAQ page with dummy data
     */
    public function index()
    {
        return Inertia::render('FAQ');
    }

    /**
     * Get FAQ data in JSON format
     * Get FAQ data in JSON format for API
     */
    public function getFAQData()
    {
        // Dummy FAQ data for WhatsApp AI Chatbot
        $faqData = [
            [
                'id' => 1,
                'question' => 'What is WhatsApp AI Chatbot?',
                'answer' => 'WhatsApp AI Chatbot is an intelligent chatbot system that uses AI technology to provide automatic responses through WhatsApp. This system can understand user questions and provide relevant answers in real-time.',
                'category' => 'General',
                'created_at' => '2025-01-27 10:00:00'
            ],
            [
                'id' => 2,
                'question' => 'How to use this chatbot?',
                'answer' => 'To use the chatbot, simply send a message to the registered WhatsApp number. The chatbot will respond automatically and help answer your questions. You can ask about services, products, or other general information.',
                'category' => 'Usage',
                'created_at' => '2025-01-27 10:15:00'
            ],
            [
                'id' => 3,
                'question' => 'Is this chatbot available 24/7?',
                'answer' => 'Yes, WhatsApp AI Chatbot operates 24 hours a day, 7 days a week. You can send messages anytime and will receive automatic responses from the AI system.',
                'category' => 'Availability',
                'created_at' => '2025-01-27 10:30:00'
            ],
            [
                'id' => 4,
                'question' => 'What languages are supported?',
                'answer' => 'Currently the chatbot supports Malay and English languages. We are developing support for other languages such as Mandarin and Tamil.',
                'category' => 'Language',
                'created_at' => '2025-01-27 10:45:00'
            ],
            [
                'id' => 5,
                'question' => 'What if the chatbot doesn\'t understand my question?',
                'answer' => 'If the chatbot cannot understand your question, the system will provide an option to connect with a human customer service agent. You can also try repeating the question with different words.',
                'category' => 'Troubleshooting',
                'created_at' => '2025-01-27 11:00:00'
            ],
            [
                'id' => 6,
                'question' => 'Is my conversation data secure?',
                'answer' => 'Yes, all your conversation data is protected with strong encryption. We follow international security standards and will not share your personal information with third parties without permission.',
                'category' => 'Security',
                'created_at' => '2025-01-27 11:15:00'
            ],
            [
                'id' => 7,
                'question' => 'Can I provide feedback about the chatbot?',
                'answer' => 'Of course! We greatly appreciate feedback from users. You can provide feedback through the "Feedback" menu in the dashboard or directly through chat by typing "feedback".',
                'category' => 'Feedback',
                'created_at' => '2025-01-27 11:30:00'
            ],
            [
                'id' => 8,
                'question' => 'How to set up notifications?',
                'answer' => 'You can set up notifications through your WhatsApp settings or through the "Settings" menu in the dashboard. Choose the type of notifications you want to receive and their frequency.',
                'category' => 'Settings',
                'created_at' => '2025-01-27 11:45:00'
            ],
            [
                'id' => 9,
                'question' => 'Is there a cost to use the chatbot?',
                'answer' => 'Basic chatbot usage is free. However, for premium features such as in-depth analysis or integration with external systems, there may be additional costs. Please contact our sales team for more information.',
                'category' => 'Pricing',
                'created_at' => '2025-01-27 12:00:00'
            ],
            [
                'id' => 10,
                'question' => 'How to contact customer service?',
                'answer' => 'You can contact customer service through WhatsApp by typing "customer service" or via email support@whatsappai.com. Our team is ready to help you Monday to Friday, 9:00 - 18:00 WIB.',
                'category' => 'Contact',
                'created_at' => '2025-01-27 12:15:00'
            ]
        ];

        return response()->json([
            'success' => true,
            'data' => $faqData,
            'total' => count($faqData),
            'categories' => array_unique(array_column($faqData, 'category'))
        ]);
    }

    /**
     * Store a newly created FAQ
     * Save new FAQ to database
     */
    public function store(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'question' => 'required|string|max:255',
            'answer' => 'required|string',
            'category' => 'required|string|max:100',
        ]);

        try {
            // Create new FAQ
            $faq = FAQ::create([
                'question' => $validated['question'],
                'answer' => $validated['answer'],
                'category' => $validated['category'],
                'is_active' => true,
                'sort_order' => 0,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'FAQ created successfully',
                'data' => $faq
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create FAQ',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get FAQ data from database
     * Get FAQ data in JSON format for API from database
     */
    public function getFAQDataFromDB(Request $request)
    {
        try {
            $query = FAQ::active()->ordered();

            // Apply search filter
            if ($request->has('search') && $request->search) {
                $query->search($request->search);
            }

            // Apply category filter
            if ($request->has('category') && $request->category) {
                $query->byCategory($request->category);
            }

            // Pagination parameters
            $perPage = $request->get('per_page', 10);
            $page = $request->get('page', 1);

            // Get paginated results
            $faqs = $query->paginate($perPage, ['*'], 'page', $page);

            // Get unique categories
            $categories = FAQ::active()
                ->distinct()
                ->pluck('category')
                ->filter()
                ->values()
                ->toArray();

            return response()->json([
                'success' => true,
                'data' => $faqs->items(),
                'pagination' => [
                    'current_page' => $faqs->currentPage(),
                    'last_page' => $faqs->lastPage(),
                    'per_page' => $faqs->perPage(),
                    'total' => $faqs->total(),
                    'from' => $faqs->firstItem(),
                    'to' => $faqs->lastItem(),
                ],
                'categories' => $categories
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch FAQ data',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
