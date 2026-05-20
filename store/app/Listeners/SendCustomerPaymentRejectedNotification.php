<?php

namespace App\Listeners;

use App\Events\PaymentRejected;
use App\Models\Order;
use App\Models\OrderPayment;
use App\Services\WhatsappNotifier;
use Illuminate\Support\Facades\URL;

/**
 * SendCustomerPaymentRejectedNotification — listener untuk PaymentRejected.
 *
 * Trigger: admin reject bukti bayar dengan alasan → OrderController::rejectPayment.
 * Action: queue WA notif ke customer (template: customer_payment_rejected)
 * dengan rejection_reason + signed re-upload URL.
 *
 * M2 stub: cuma INSERT row ke wa_notifications status='queued'.
 */
class SendCustomerPaymentRejectedNotification
{
    public function __construct(protected WhatsappNotifier $notifier) {}

    public function handle(PaymentRejected $event): void
    {
        $recipient = (string) ($event->order->phone ?? '');
        if ($recipient === '') {
            return;
        }

        // Derive sequence dari payment position di order siblings (order-by-id).
        $sequence = $this->derivePaymentSequence($event->order, $event->payment);

        $ttlDays = (int) config('checkout.upload_url_ttl_days', 7);
        $uploadUrl = URL::temporarySignedRoute(
            'upload.show',
            now()->addDays($ttlDays),
            [
                'order_number' => $event->order->order_number,
                'seq' => $sequence,
            ],
        );

        $this->notifier->send(
            template: 'customer_payment_rejected',
            recipient: $recipient,
            payload: [
                'order_number' => $event->order->order_number,
                'customer_name' => $event->order->customer_name,
                'payment_id' => $event->payment->id,
                'sequence' => $sequence,
                'amount' => (int) $event->payment->amount,
                'rejection_reason' => $event->reason,
                'reupload_url' => $uploadUrl,
            ],
            order: $event->order,
        );
    }

    /**
     * Compute 0-indexed payment sequence by id position in order's payments.
     */
    protected function derivePaymentSequence(Order $order, OrderPayment $payment): int
    {
        $ids = $order->payments()->orderBy('id')->pluck('id')->all();
        $position = array_search($payment->id, $ids, true);

        return $position === false ? 0 : (int) $position;
    }
}
