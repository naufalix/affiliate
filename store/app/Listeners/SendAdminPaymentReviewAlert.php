<?php

namespace App\Listeners;

use App\Events\PaymentSubmitted;
use App\Services\Settings;
use App\Services\WhatsappNotifier;

/**
 * SendAdminPaymentReviewAlert — listener untuk PaymentSubmitted.
 *
 * Trigger: customer upload bukti bayar → POST /upload/{order_number} sukses.
 * Action: queue WA notif ke admin (template: admin_payment_review_alert)
 * supaya admin tau ada bukti bayar baru yang nunggu verifikasi.
 *
 * M2 stub: cuma INSERT row ke wa_notifications status='queued'. Gateway
 * sender M3+.
 */
class SendAdminPaymentReviewAlert
{
    public function __construct(protected WhatsappNotifier $notifier) {}

    public function handle(PaymentSubmitted $event): void
    {
        $admin = Settings::getWaAdmin();
        $recipient = (string) ($admin['number'] ?? '');

        // Skip kalau admin contact ngga ke-set — defensive untuk fresh install.
        if ($recipient === '') {
            return;
        }

        $this->notifier->send(
            template: 'admin_payment_review_alert',
            recipient: $recipient,
            payload: [
                'order_number' => $event->order->order_number,
                'customer_name' => $event->order->customer_name,
                'payment_id' => $event->payment->id,
                'amount' => (int) $event->payment->amount,
                'sequence' => $event->sequence,
                'review_url' => route('admin.orders.show', $event->order),
            ],
            order: $event->order,
        );
    }
}
