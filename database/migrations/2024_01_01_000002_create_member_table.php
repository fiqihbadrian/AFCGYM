<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('member', function (Blueprint $table) {
            $table->id();
            $table->string('kode_member', 20)->unique();
            $table->string('nama');
            $table->string('nik', 20)->nullable();
            $table->string('no_hp', 20);
            $table->string('alamat')->nullable();
            $table->string('foto')->nullable();
            $table->date('tanggal_daftar');
            $table->timestamps();
        });

        // Relasi user ke member (dibuat di sini karena urutan tabel)
        Schema::table('user', function (Blueprint $table) {
            $table->foreign('member_id')->references('id')->on('member')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user', function (Blueprint $table) {
            $table->dropForeign(['member_id']);
        });

        Schema::dropIfExists('member');
    }
};
