<?php

namespace App\Services;

use App\Models\PushSubscription;
use App\Models\User;
use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;
use Illuminate\Support\Facades\Log;

class PushNotificationService
{
    protected WebPush $webPush;

    public function __construct()
    {
        $this->webPush = new WebPush([
            'VAPID' => [
                'subject' => config('app.url'),
                'publicKey' => config('services.vapid.public_key'),
                'privateKey' => config('services.vapid.private_key'),
            ],
        ]);
    }

    /**
     * ارسال notification به یک کاربر
     */
    public function sendToUser(User $user, array $payload): bool
    {
        $subscriptions = $user->pushSubscriptions;

        if ($subscriptions->isEmpty()) {
            return false;
        }

        $success = false;
        foreach ($subscriptions as $subscription) {
            if ($this->sendToSubscription($subscription, $payload)) {
                $success = true;
            }
        }

        return $success;
    }

    /**
     * ارسال notification به یک subscription
     */
    public function sendToSubscription(PushSubscription $pushSubscription, array $payload): bool
    {
        try {
            $subscription = Subscription::create([
                'endpoint' => $pushSubscription->endpoint,
                'keys' => [
                    'p256dh' => $pushSubscription->public_key,
                    'auth' => $pushSubscription->auth_token,
                ],
                'contentEncoding' => $pushSubscription->content_encoding,
            ]);

            $result = $this->webPush->sendOneNotification(
                $subscription,
                json_encode($payload)
            );

            // اگر subscription منقضی شده، حذف کن
            if (!$result->isSuccess() && $result->isSubscriptionExpired()) {
                $pushSubscription->delete();
                Log::info('Push subscription expired and deleted', [
                    'subscription_id' => $pushSubscription->id,
                ]);
                return false;
            }

            return $result->isSuccess();
        } catch (\Exception $e) {
            Log::error('Failed to send push notification', [
                'subscription_id' => $pushSubscription->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * ارسال notification به همه کاربران
     */
    public function sendToAll(array $payload): int
    {
        $subscriptions = PushSubscription::all();
        $successCount = 0;

        foreach ($subscriptions as $subscription) {
            if ($this->sendToSubscription($subscription, $payload)) {
                $successCount++;
            }
        }

        return $successCount;
    }

    /**
     * ارسال notification به لیستی از کاربران
     */
    public function sendToUsers(array $userIds, array $payload): int
    {
        $subscriptions = PushSubscription::whereIn('user_id', $userIds)->get();
        $successCount = 0;

        foreach ($subscriptions as $subscription) {
            if ($this->sendToSubscription($subscription, $payload)) {
                $successCount++;
            }
        }

        return $successCount;
    }

    /**
     * ذخیره subscription جدید
     */
    public function subscribe(?User $user, array $subscriptionData): PushSubscription
    {
        return PushSubscription::updateOrCreate(
            ['endpoint' => $subscriptionData['endpoint']],
            [
                'user_id' => $user?->id,
                'public_key' => $subscriptionData['keys']['p256dh'] ?? null,
                'auth_token' => $subscriptionData['keys']['auth'] ?? null,
                'content_encoding' => $subscriptionData['contentEncoding'] ?? 'aesgcm',
            ]
        );
    }

    /**
     * حذف subscription
     */
    public function unsubscribe(string $endpoint): bool
    {
        return PushSubscription::where('endpoint', $endpoint)->delete() > 0;
    }
}
