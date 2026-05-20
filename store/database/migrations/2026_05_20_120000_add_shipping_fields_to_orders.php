<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
|--------------------------------------------------------------------------
| Add shipping fields to orders (task t_34ed789d)
|--------------------------------------------------------------------------
|
| Kolom buat capture data resi saat admin transition order ke `shipped`:
|   - shipping_courier  : enum kurir (JNE/JNT/SiCepat/Pos/Other)
|   - shipping_resi     : nomor resi/AWB
|   - shipped_at        : timestamp transition ke shipped
|
| Reversible: down() drop ketiga kolom. Tidak menyentuh enum status (udah
| punya 'shipped' sejak migration awal).
|
*/

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('shipping_courier', 32)->nullable()->after('ref_code');
            $table->string('shipping_resi', 64)->nullable()->after('shipping_courier');
            $table->timestamp('shipped_at')->nullable()->after('shipping_resi');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['shipping_courier', 'shipping_resi', 'shipped_at']);
        });
    }
};
