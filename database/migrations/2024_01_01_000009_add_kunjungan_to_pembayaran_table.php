<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Pembayaran bisa berasal dari dua sumber:
     * - langganan : bayar paket berdurasi
     * - latihan   : bayar per kedatangan (kunjungan_id terisi)
     *
     * Salah satu kolom sumber terisi, tidak pernah keduanya.
     */
    public function up(): void
    {
        Schema::table('pembayaran', function (Blueprint $table) {
            $table->dropForeign(['langganan_id']);
        });

        Schema::table('pembayaran', function (Blueprint $table) {
            $table->unsignedBigInteger('langganan_id')->nullable()->change();
        });

        Schema::table('pembayaran', function (Blueprint $table) {
            $table->foreign('langganan_id')->references('id')->on('langganan')->cascadeOnDelete();
            $table->foreignId('kunjungan_id')->nullable()->after('langganan_id')
                ->constrained('kunjungan')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('pembayaran', function (Blueprint $table) {
            $table->dropForeign(['kunjungan_id']);
            $table->dropColumn('kunjungan_id');
        });
    }
};
