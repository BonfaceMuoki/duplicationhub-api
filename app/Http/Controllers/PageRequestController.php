<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PageRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class PageRequestController extends Controller
{
    /**
     * Submit a page request
     */
    public function submit(Request $request): JsonResponse
    {
        // Debug: Log the incoming request data
        \Log::info('Page Request Data:', $request->all());
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone_number' => 'required|string|max:20',
            'message' => 'required|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $data = [
                'name' => $request->name,
                'email' => $request->email,
                'phone_number' => $request->phone_number,
                'message' => $request->message,
                'status' => 'pending'
            ];
            
            // Debug: Log the data being saved
            \Log::info('Page Request Data to Save:', $data);
            
            $pageRequest = PageRequest::create($data);
            
            // Debug: Log the created record
            \Log::info('Page Request Created:', $pageRequest->toArray());

            return response()->json([
                'success' => true,
                'message' => 'Page request submitted successfully',
                'data' => $pageRequest
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to submit page request',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Fetch all page requests (for admin)
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = PageRequest::query();

            // Filter by status if provided
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            // Sort by created_at desc by default
            $query->orderBy('created_at', 'desc');

            $pageRequests = $query->get();

            return response()->json([
                'success' => true,
                'data' => $pageRequests
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch page requests',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update page request status
     */
    public function updateStatus(Request $request, $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,approved,rejected'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $pageRequest = PageRequest::findOrFail($id);
            $pageRequest->update(['status' => $request->status]);

            return response()->json([
                'success' => true,
                'message' => 'Page request status updated successfully',
                'data' => $pageRequest
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update page request status',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
