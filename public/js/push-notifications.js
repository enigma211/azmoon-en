/**
 * Push Notifications Manager
 * مدیریت اعلان‌های فشاری برای PWA
 */

class PushNotificationManager {
    constructor() {
        this.publicKey = null;
        this.registration = null;
        this.subscription = null;
    }

    /**
     * بررسی پشتیبانی مرورگر
     */
    isSupported() {
        return 'serviceWorker' in navigator && 
               'PushManager' in window && 
               'Notification' in window;
    }

    /**
     * دریافت VAPID Public Key
     */
    async getPublicKey() {
        if (this.publicKey) {
            return this.publicKey;
        }

        try {
            const response = await fetch('/push/vapid-public-key');
            const data = await response.json();
            this.publicKey = data.publicKey;
            return this.publicKey;
        } catch (error) {
            console.error('خطا در دریافت VAPID key:', error);
            throw error;
        }
    }

    /**
     * درخواست اجازه از کاربر
     */
    async requestPermission() {
        if (!this.isSupported()) {
            throw new Error('مرورگر از Push Notifications پشتیبانی نمی‌کند');
        }

        const permission = await Notification.requestPermission();
        return permission === 'granted';
    }

    /**
     * ثبت subscription
     */
    async subscribe() {
        try {
            // بررسی اجازه
            if (Notification.permission !== 'granted') {
                const granted = await this.requestPermission();
                if (!granted) {
                    throw new Error('کاربر اجازه نداد');
                }
            }

            // دریافت Service Worker registration
            this.registration = await navigator.serviceWorker.ready;

            // دریافت VAPID key
            const publicKey = await this.getPublicKey();

            // ثبت subscription
            this.subscription = await this.registration.pushManager.subscribe({
                userVisibleOnly: true,
                applicationServerKey: this.urlBase64ToUint8Array(publicKey)
            });

            // ارسال به سرور
            const response = await fetch('/push/subscribe', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                },
                body: JSON.stringify(this.subscription.toJSON())
            });

            const result = await response.json();
            
            if (result.success) {
                console.log('✅ اشتراک با موفقیت ثبت شد');
                return true;
            } else {
                throw new Error(result.message);
            }
        } catch (error) {
            console.error('❌ خطا در ثبت اشتراک:', error);
            throw error;
        }
    }

    /**
     * لغو subscription
     */
    async unsubscribe() {
        try {
            if (!this.subscription) {
                this.registration = await navigator.serviceWorker.ready;
                this.subscription = await this.registration.pushManager.getSubscription();
            }

            if (!this.subscription) {
                console.log('اشتراکی وجود ندارد');
                return true;
            }

            // لغو در مرورگر
            await this.subscription.unsubscribe();

            // حذف از سرور
            const response = await fetch('/push/unsubscribe', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                },
                body: JSON.stringify({
                    endpoint: this.subscription.endpoint
                })
            });

            const result = await response.json();
            
            if (result.success) {
                console.log('✅ اشتراک با موفقیت لغو شد');
                this.subscription = null;
                return true;
            } else {
                throw new Error(result.message);
            }
        } catch (error) {
            console.error('❌ خطا در لغو اشتراک:', error);
            throw error;
        }
    }

    /**
     * بررسی وضعیت subscription
     */
    async getSubscriptionStatus() {
        try {
            if (!this.isSupported()) {
                return { subscribed: false, permission: 'unsupported' };
            }

            const permission = Notification.permission;
            
            if (permission !== 'granted') {
                return { subscribed: false, permission };
            }

            this.registration = await navigator.serviceWorker.ready;
            this.subscription = await this.registration.pushManager.getSubscription();

            return {
                subscribed: !!this.subscription,
                permission,
                subscription: this.subscription
            };
        } catch (error) {
            console.error('خطا در بررسی وضعیت:', error);
            return { subscribed: false, permission: 'error', error };
        }
    }

    /**
     * ارسال notification تستی
     */
    async sendTestNotification() {
        try {
            const response = await fetch('/push/send-test', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                }
            });

            const result = await response.json();
            
            if (result.success) {
                console.log('✅ اعلان تستی ارسال شد');
                return true;
            } else {
                throw new Error(result.message);
            }
        } catch (error) {
            console.error('❌ خطا در ارسال اعلان تستی:', error);
            throw error;
        }
    }

    /**
     * تبدیل Base64 به Uint8Array
     */
    urlBase64ToUint8Array(base64String) {
        const padding = '='.repeat((4 - base64String.length % 4) % 4);
        const base64 = (base64String + padding)
            .replace(/\-/g, '+')
            .replace(/_/g, '/');

        const rawData = window.atob(base64);
        const outputArray = new Uint8Array(rawData.length);

        for (let i = 0; i < rawData.length; ++i) {
            outputArray[i] = rawData.charCodeAt(i);
        }
        return outputArray;
    }
}

// ایجاد instance سراسری
window.pushManager = new PushNotificationManager();

// Auto-subscribe برای کاربران لاگین (اختیاری)
if (document.querySelector('meta[name="user-authenticated"]')?.content === 'true') {
    window.addEventListener('load', async () => {
        const status = await window.pushManager.getSubscriptionStatus();
        
        // اگر اجازه داده شده اما subscribe نشده، خودکار subscribe کن
        if (status.permission === 'granted' && !status.subscribed) {
            try {
                await window.pushManager.subscribe();
                console.log('✅ Auto-subscribed to push notifications');
            } catch (error) {
                console.log('Auto-subscribe failed:', error.message);
            }
        }
    });
}
