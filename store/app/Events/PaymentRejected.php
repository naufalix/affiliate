<?php

namespace App\Events;

use App\Models\Order;
use App\Models\OrderPayment;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * PaymentRejected — dispatched saat admin reject bukti bayar dengan alasan.
 *
 * Listener: SendCustomerPaymentRejectedNotification (WA stub di task t_e5d877f3)
 * → write entry ke `wa_notifications` template 'customer_payment_rejected',
 * payload include rejection_reason.
 */
class PaymentRejected
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public Order $order,
        public OrderPayment $payment,
        public string $reason,
    ) {}
}
