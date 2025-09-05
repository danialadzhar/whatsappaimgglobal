<?php

namespace App\Http\Controllers;

use App\Models\FAQ;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Exception;

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
            'branch' => 'required|string|max:100',
        ]);

        try {
            // Create new FAQ
            $faq = FAQ::create([
                'question' => $validated['question'],
                'answer' => $validated['answer'],
                'branch' => $validated['branch'],
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
     * Update FAQ
     * Update existing FAQ in database
     */
    public function update(Request $request, $id)
    {
        // Validate the request
        $validated = $request->validate([
            'question' => 'required|string|max:255',
            'answer' => 'required|string',
            'branch' => 'required|string|max:100',
        ]);

        try {
            // Find FAQ by ID
            $faq = FAQ::findOrFail($id);

            // Update FAQ
            $faq->update([
                'question' => $validated['question'],
                'answer' => $validated['answer'],
                'branch' => $validated['branch'],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'FAQ successfully updated!',
                'data' => $faq
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update FAQ',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete FAQ
     * Delete FAQ from database
     */
    public function destroy($id)
    {
        try {
            // Find FAQ by ID
            $faq = FAQ::findOrFail($id);

            // Store FAQ data untuk response
            $deletedFaq = [
                'id' => $faq->id,
                'question' => $faq->question,
                'branch' => $faq->branch
            ];

            // Delete FAQ
            $faq->delete();

            return response()->json([
                'success' => true,
                'message' => 'FAQ successfully deleted!',
                'data' => $deletedFaq
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete FAQ',
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

            // Apply branch filter
            if ($request->has('branch') && $request->branch) {
                $query->byBranch($request->branch);
            }

            // Pagination parameters
            $perPage = $request->get('per_page', 10);
            $page = $request->get('page', 1);

            // Get paginated results
            $faqs = $query->paginate($perPage, ['*'], 'page', $page);

            // Get unique branches
            $branches = FAQ::active()
                ->distinct()
                ->pluck('branch')
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
                'branches' => $branches
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch FAQ data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all FAQ data without pagination
     * Return semua FAQ data untuk API integration
     */
    public function getAllFAQData(Request $request)
    {
        try {
            $query = FAQ::active()->ordered();

            // Apply search filter
            if ($request->has('search') && $request->search) {
                $query->search($request->search);
            }

            // Apply branch filter
            if ($request->has('branch') && $request->branch) {
                $query->byBranch($request->branch);
            }

            // Get all results without pagination
            $faqs = $query->get();

            // Get unique branches
            $branches = FAQ::active()
                ->distinct()
                ->pluck('branch')
                ->filter()
                ->values()
                ->toArray();

            return response()->json([
                'success' => true,
                'data' => $faqs,
                'total' => $faqs->count(),
                'branches' => $branches
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch all FAQ data',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
