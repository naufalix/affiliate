<?php

namespace App\Events;

use App\Models\Order;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * OrderShipped — dispatched saat admin input resi & order transition ke 'shipped'.
 *
 * Listener target (downstream task t_e5d877f3): write entry ke `wa_notifications`
 * untuk WA stub. Listener belum diimplement di task ini — event di-fire saja
 * supaya wiring siap pas WA task picked up.
 */
class OrderShipped
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(public Order $order) {}
}
