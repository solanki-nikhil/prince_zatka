<?php

namespace App\Http\Controllers;

use App\Services\PushNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class PushSubscriptionController extends Controller
{
    protected $pushNotificationService;

    public function __construct(PushNotificationService $pushNotificationService)
    {
        $this->pushNotificationService = $pushNotificationService;
    }

    /**
     * Store push subscriptions
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'endpoint' => 'required|string',
            'keys.p256dh' => 'required|string',
            'keys.auth' => 'required|string',
            'device_type' => 'nullable|string|in:web,mobile,desktop'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = Auth::user();
            
            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            $subscription = $this->pushNotificationService->storeSubscription($user, $request->all());

            return response()->json([
                'status' => true,
                'message' => 'Push subscription stored successfully',
                'data' => $subscription
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to store push subscription: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove push subscription
     */
    public function destroy(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'endpoint' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = Auth::user();
            
            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            $removed = $this->pushNotificationService->removeSubscription($user, $request->endpoint);

            if ($removed) {
                return response()->json([
                    'status' => true,
                    'message' => 'Push subscription removed successfully'
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Push subscription not found'
                ], 404);
            }

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to remove push subscription: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get user's push subscriptions
     */
    public function index()
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            $subscriptions = $user->pushSubscriptions()->where('is_active', true)->get();

            return response()->json([
                'status' => true,
                'data' => $subscriptions
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to get push subscriptions: ' . $e->getMessage()
            ], 500);
        }
    }
}
