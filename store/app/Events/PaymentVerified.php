<?php

namespace App\Events;

use App\Models\Order;
use App\Models\OrderPayment;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * PaymentVerified — dispatched saat admin approve bukti bayar.
 *
 * Listener: SendCustomerPaymentVerifiedNotification (WA stub di task t_e5d877f3)
 * → write entry ke `wa_notifications` template 'customer_payment_verified'.
 */
class PaymentVerified
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(
        public Order $order,
        public OrderPayment $payment,
    ) {}
}
