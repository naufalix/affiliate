# PR Templates — M2 Implementation Chain (5 PRs)

Generated: 2026-05-20
Branch base: `main`
Repo: naufalix/affiliate

PR order (dependency-respecting): merge dari atas ke bawah.

---

## PR 1 — t_34ed789d (independent)

**Title:** `feat(admin): input resi + transition ke shipped (t_34ed789d)`

**Branch:** `feat/t_34ed789d-input-resi` → `main`

**Body:**

```
## Task
[t_34ed789d](kanban://masfirmanpratama/t_34ed789d) — Pesanan · Input resi + status transition ke shipped

## Changes
- Form Input Resi di order detail page (status `verified`/`paid`/`packed` → `shipped`)
- Validate `shipping_courier` (whitelist: JNE, POS, SiCepat, JNT, Tiki, Anteraja) + `shipping_resi` (4-64 chars)
- Migration: add `shipping_courier`, `shipping_resi`, `shipped_at` ke orders
- Fire `OrderShipped` event (stub buat WA notif task downstream)
- Track page hydrate dari DB kalau order_number cocok (override dummy heuristic lama)

## Tests
18 feature test (Admin\OrderShipTest) — auth, validation, transition, multi-courier, event dispatch, track page DB hydrate

## Acceptance
- [x] Form muncul di order detail saat status paid/verified
- [x] Resi tersimpan + transition ke shipped
- [x] OrderShipped event di-fire (listener di task t_e5d877f3)
- [x] Track page render real resi dari DB
```

---

## PR 2 — t_a3f2fe94 (parent: t_410e2c29 + t_6be9a4e4 + t_8446fbd4 — udah merged)

**Title:** `feat(store): wire FE→BE checkout flow (t_a3f2fe94)`

**Branch:** `feat/t_a3f2fe94-checkout-wire` → `main`

**Body:**

```
## Task
[t_a3f2fe94](kanban://masfirmanpratama/t_a3f2fe94) — Wire FE→BE · POST /checkout

## Changes
- Replace closure stub di `routes/web.php` dengan `CheckoutController@store`
- Persist `Order` + `OrderItems` + `OrderPayments` dalam transaction
- Generate `order_number` unique format `MFP-YYYYMMDD-XXXXXX`
- Support lunas + cicilan (via `installment_scheme`) — generate payment schedule sesuai DP%
- Redirect ke `/upload/{order_number}` dengan signed URL via session
- M1 stub fallback masih jalan (backward compat untuk prototype)

## Tests
Feature test multi-skenario: lunas, cicilan dengan N=1 (treated as lunas), multi-item, validation error, transaction rollback on failure, order_number format.

## Acceptance
- [x] POST /checkout save order ke DB
- [x] Cicilan generate N rows di order_payments
- [x] Redirect ke /upload pakai signed URL
- [x] Validation error preserve form input
```

---

## PR 3 — t_c0616c67 (parent: t_a3f2fe94)

**Title:** `feat(store): wire FE→BE upload bukti bayar (t_c0616c67)`

**Branch:** `feat/t_c0616c67-upload-wire` → `main` (after PR 2 merged)

**Body:**

```
## Task
[t_c0616c67](kanban://masfirmanpratama/t_c0616c67) — Wire FE→BE · POST /upload/{order}

## Changes
- `UploadController@show` — fetch real Order, list pending payments, render view dengan DB data
- `UploadController@store` — validate file (image, max 2MB), match payment by sequence, save ke `storage/app/public/payment-proofs/`, update `proof_path` + `paid_at`
- Fire `PaymentSubmitted` event (stub buat WA notif task downstream)
- Order.status TIDAK transition saat upload — status flip ke `paid`/`partial_paid` di OrderController::approvePayment setelah admin verify (schema source-of-truth: enum ngga punya 'payment_review')
- M1 stub fallback (kalau order_number ngga match DB)

## Tests
13 feature test (UploadStoreDbTest) — show real order, validation (file required/size/mime), match payment by sequence, double-upload reject, randomized filename, full checkout-to-upload flow E2E

## Acceptance
- [x] Upload page render real pending payments
- [x] File save + payment update
- [x] PaymentSubmitted event di-fire
- [x] Multi-cicilan: upload ke seq=N update payment ke-N
```

---

## PR 4 — t_8a063559 (parent: t_c0616c67)

**Title:** `feat(store): token-protect /upload + /track via signed URL (t_8a063559)`

**Branch:** `feat/t_8a063559-signed-url` → `main` (after PR 3 merged)

**Body:**

```
## Task
[t_8a063559](kanban://masfirmanpratama/t_8a063559) — Wire FE→BE · Token-protect /upload + /track

## Changes
- Middleware `signed` di route `/upload/{order_number}` (GET + POST) + `/track/{order_number}` (GET)
- TTL config-driven: `CHECKOUT_UPLOAD_URL_TTL_DAYS=7`, `CHECKOUT_TRACK_URL_TTL_DAYS=30`
- Custom `InvalidSignatureException` handler dengan friendly Indonesian view (`signed-url-error.blade.php`)
- Signed URL generation di `CheckoutController::store` (session-stash track URL untuk success page)
- Form action di upload view juga signed (re-submit dengan signature)
- Test helper `signedTrack`/`signedStore` reusable di test base

## Tests
11 test baru (SignedUrlGuardTest) — tanpa/valid/expired/tampered signature × upload+track × GET+POST. 47 test existing di-update pakai signed URL.

## Live verify
- `/upload/MFP-NOT-EXIST` (no signature) → HTTP 403 dengan friendly view ✅

## Decision (logged in commit body)
- Status 403 untuk both `invalid` + `expired` — Laravel ngga distinguish di middleware level. Custom handler bisa cek `$exception->isSignatureExpired()` tapi response status sama. Acceptable untuk M2.

## Acceptance
- [x] Akses tanpa signature → 403
- [x] Expired signature → 403
- [x] Valid signature → 200
- [x] Tampered signature → 403
```

---

## PR 5 — t_e5d877f3 (parent: t_8a063559 + t_34ed789d merge base)

**Title:** `feat(store): WA notification stub via event listeners (t_e5d877f3)`

**Branch:** `feat/t_e5d877f3-wa-notif-stub` → `main` (after PR 4 + PR 1 merged)

**Body:**

```
## Task
[t_e5d877f3](kanban://masfirmanpratama/t_e5d877f3) — WA Notification stub

## Changes

### Events (2 baru)
- `PaymentVerified` — dispatched dari `OrderController::approvePayment`
- `PaymentRejected` — dispatched dari `OrderController::rejectPayment`
- `PaymentSubmitted` (existing dari t_c0616c67) — extend dengan `$sequence` 3rd constructor arg
- `OrderShipped` (existing dari t_34ed789d) — no change

### Service
- `App\Services\WhatsappNotifier::send(template, recipient, payload, ?order)` — insert row ke `wa_notifications` status='queued'

### Listeners (4) di `app/Listeners/`
- `SendAdminPaymentReviewAlert` → admin_payment_review_alert ke admin WA
- `SendCustomerPaymentVerifiedNotification` → customer_payment_verified + signed track URL
- `SendCustomerPaymentRejectedNotification` → customer_payment_rejected + rejection_reason + signed re-upload URL
- `SendCustomerOrderShippedNotification` → customer_order_shipped + resi + signed track URL
- Registered di `AppServiceProvider::boot` (Laravel 11 — no EventServiceProvider)

### Admin UI
- Sidebar nav 'WA Notifikasi' (`admin.wa-notifications.index`)
- `WaNotificationController@index` — read-only list, filter status/template, search recipient/order_number, pagination 20/page
- View `admin/wa-notifications/index.blade.php` — stat strip queued/sent/failed/total, filter form, table dengan order link, empty state

### Sequence handling
Schema `order_payments` tidak punya 'sequence' column. Solusi:
- `PaymentSubmitted` event terima sequence sebagai 3rd constructor arg (dari UploadController yang derive dari array position)
- Listener `PaymentVerified`/`Rejected` derive sequence sendiri via `derivePaymentSequence()` helper (`array_search` position di order siblings order-by-id)

### Defensive
- Listener skip kalau recipient empty (test_listener_skips_when_recipient_empty)
- `Settings::getWaAdmin()` default fallback ke `config('store.wa_admin')`

## Tests
15 test baru di `WaNotificationStubTest` (50 assertions):
- Service unit: insert queued row, payload JSON valid, works without order
- Listener integration via `event()`: admin alert, customer verified, customer rejected w/ reason, customer shipped w/ resi, skip empty recipient
- Controller wire HTTP: approve, reject, ship, customer upload — all end-to-end fire event → row created
- Admin index page: render, filter by status, empty state, auth required

**Total suite: 270/270 pass (1019 assertions, 6.36s).**

## Branch chain
Base = merge dari `feat/t_8a063559-signed-url` + `feat/t_34ed789d-input-resi` (biar listener bisa subscribe ke 4 event sekaligus tanpa missing parent file).

## Pint
3 fully_qualified_strict_types fix (extracted `\App\Models\Order` `\App\Events\PaymentVerified` etc ke use statements).

## Acceptance
- [x] Trigger flow → row baru di wa_notifications dengan payload JSON valid, status='queued'
- [x] Admin bisa lihat log queued di /admin/wa-notifications
- [x] Gateway integration M3+ (sender daemon yang flip status queued→sent/failed)

## Out of scope (M3+)
- Actual WhatsApp gateway integration (Fonnte/Wablas)
- Sender worker daemon
- Retry mechanism
- Failed notification alerting
```

---

## Merge Order Strategy

**Sequential merge** — base branch update tiap PR di-merge:

1. Merge PR 1 (t_34ed789d) → main
2. Merge PR 2 (t_a3f2fe94) → main
3. Rebase PR 3 onto new main (atau merge as-is — kemungkinan no conflict)
4. Merge PR 3 (t_c0616c67) → main
5. Rebase PR 4 onto new main → merge
6. Rebase PR 5 onto new main → merge

**Alternative: GitHub merge-queue** kalau enabled di repo settings (auto sequential rebase + merge).

**Conflict expectation: LOW** — branch chain udah linear, merge antar branch udah resolved sebelumnya. Tapi tetap perlu cek post-PR-1-merge apakah PR 2 fast-forward atau need rebase.

## Test verification post-merge

Setelah semua 5 PR merged ke main, di main branch:
```bash
cd /root/malang-creative/_active/masfirmanpratama && git checkout main && git pull
cd store && php artisan test
# Expected: 270/270 pass
```
