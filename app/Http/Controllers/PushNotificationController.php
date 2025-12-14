<?php

namespace App\Http\Controllers;

use App\Services\PushNotificationService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PushNotificationController extends Controller
{
    public function __construct(
        protected PushNotificationService $pushService
    ) {}

    /**
     * دریافت VAPID Public Key
     */
    public function getPublicKey(): JsonResponse
    {
        return response()->json([
            'publicKey' => config('services.vapid.public_key'),
        ]);
    }

    /**
     * ثبت subscription جدید
     */
    public function subscribe(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'endpoint' => 'required|string|max:500',
            'keys.p256dh' => 'required|string|max:255',
            'keys.auth' => 'required|string|max:255',
            'contentEncoding' => 'nullable|string|max:16',
        ]);

        try {
            $subscription = $this->pushService->subscribe(
                $request->user(),
                $validated
            );

            return response()->json([
                'success' => true,
                'message' => 'اشتراک با موفقیت ثبت شد',
                'subscription_id' => $subscription->id,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطا در ثبت اشتراک',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * حذف subscription
     */
    public function unsubscribe(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'endpoint' => 'required|string|max:500',
        ]);

        try {
            $deleted = $this->pushService->unsubscribe($validated['endpoint']);

            return response()->json([
                'success' => $deleted,
                'message' => $deleted ? 'اشتراک با موفقیت حذف شد' : 'اشتراک یافت نشد',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطا در حذف اشتراک',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * ارسال notification تستی
     */
    public function sendTest(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'کاربر احراز هویت نشده است',
            ], 401);
        }

        $payload = [
            'title' => 'اعلان تستی',
            'body' => 'این یک اعلان تستی از آزمون کده است! 🎉',
            'icon' => '/icons/icon-192x192.png',
            'badge' => '/icons/icon-96x96.png',
            'data' => [
                'url' => url('/'),
                'timestamp' => now()->toIso8601String(),
            ],
        ];

        $success = $this->pushService->sendToUser($user, $payload);

        return response()->json([
            'success' => $success,
            'message' => $success ? 'اعلان با موفقیت ارسال شد' : 'خطا در ارسال اعلان',
        ]);
    }
}
