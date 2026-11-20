<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tamu per latihan sering tidak mau memberi nomor HP,
     * jadi kolom ini tidak boleh wajib.
     */
    public function up(): void
    {
        Schema::table('member', function (Blueprint $table) {
            $table->string('no_hp', 20)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('member', function (Blueprint $table) {
            $table->string('no_hp', 20)->nullable(false)->change();
        });
    }
};
