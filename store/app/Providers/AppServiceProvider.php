<?php

namespace App\Providers;

use App\Events\OrderShipped;
use App\Events\PaymentRejected;
use App\Events\PaymentSubmitted;
use App\Events\PaymentVerified;
use App\Listeners\SendAdminPaymentReviewAlert;
use App\Listeners\SendCustomerOrderShippedNotification;
use App\Listeners\SendCustomerPaymentRejectedNotification;
use App\Listeners\SendCustomerPaymentVerifiedNotification;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Event listener registration (Laravel 11 — no EventServiceProvider).
        // Listener classes write WA notif rows ke wa_notifications (M2 stub).
        // Detail: app/Listeners/* + app/Services/WhatsappNotifier.php (task t_e5d877f3).
        Event::listen(PaymentSubmitted::class, SendAdminPaymentReviewAlert::class);
        Event::listen(PaymentVerified::class, SendCustomerPaymentVerifiedNotification::class);
        Event::listen(PaymentRejected::class, SendCustomerPaymentRejectedNotification::class);
        Event::listen(OrderShipped::class, SendCustomerOrderShippedNotification::class);
    }
}
