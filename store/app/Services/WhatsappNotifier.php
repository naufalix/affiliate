<?php

namespace App\Services;

use App\Models\Order;
use App\Models\WaNotification;

/**
 * WhatsappNotifier — M2 stub (no actual gateway integration).
 *
 * INSERT row ke `wa_notifications` dengan status='queued'. Gateway sender
 * (Fonnte / Wablas) di-wire di M3+ via worker yang baca queued rows + flip
 * status ke 'sent' atau 'failed' setelah API call.
 *
 * Pattern usage:
 *
 *     app(WhatsappNotifier::class)->send(
 *         template: 'admin_payment_review_alert',
 *         recipient: '+628111222333',
 *         payload: ['order_number' => 'MFP-...', 'amount' => 1500000],
 *         order: $order, // optional, untuk FK lookup di list view
 *     );
 *
 * Recipient format: E.164 (+62...) atau plain digit (sanitization gateway side).
 * Template: snake_case identifier yang nanti di-map ke WhatsApp Business
 * template approved Meta. Untuk M2 stub, template name cuma annotation.
 *
 * Listeners di app/Listeners/* call ini, listener registration di
 * AppServiceProvider::boot.
 */
class WhatsappNotifier
{
    /**
     * Queue WhatsApp notification — write row ke wa_notifications, return WaNotification.
     *
     * @param  string  $template  template identifier (mis. 'admin_payment_review_alert')
     * @param  string  $recipient  phone E.164 (mis. '+62811222333')
     * @param  array<string, mixed>  $payload  variabel template (di-serialize JSON di kolom payload_json)
     * @param  Order|null  $order  optional FK ke orders.id (nullOnDelete)
     */
    public function send(
        string $template,
        string $recipient,
        array $payload = [],
        ?Order $order = null,
    ): WaNotification {
        return WaNotification::create([
            'order_id' => $order?->id,
            'recipient' => $recipient,
            'template' => $template,
            'payload_json' => $payload,
            'status' => 'queued',
        ]);
    }
}
