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
