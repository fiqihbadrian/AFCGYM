<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Pesanan paket yang dibuat member sendiri dari halaman member.
     *
     * Member memilih paket lalu membayar di tempat. Petugas mengonfirmasi
     * pesanan; saat dikonfirmasi barulah dibuat langganan + pembayaran,
     * supaya pemasukan tetap tercatat di satu tempat (tabel pembayaran).
     */
    public function up(): void
    {
        Schema::create('pesanan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained('member')->cascadeOnDelete();
            $table->foreignId('paket_id')->constrained('paket')->restrictOnDelete();
            $table->decimal('jumlah', 12, 2)->comment('Harga paket saat dipesan');
            $table->string('metode', 30)->default('cash')
                ->comment('cash / transfer / qris — dibayar di tempat');
            $table->string('status', 20)->default('menunggu')
                ->comment('menunggu = belum dibayar, diproses = sudah dikonfirmasi');
            $table->foreignId('diproses_oleh')->nullable()->constrained('user')->nullOnDelete();
            $table->dateTime('diproses_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pesanan');
    }
};
