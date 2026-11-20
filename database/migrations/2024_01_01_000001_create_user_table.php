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
        Schema::create('user', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('email')->unique();
            $table->string('password');
            $table->tinyInteger('role')->default(0)->comment('1 = Admin, 0 = Member');
            $table->tinyInteger('status')->default(1)->comment('1 = Aktif, 0 = Nonaktif');
            $table->string('hp', 20)->nullable();
            $table->string('foto')->nullable();
            // FK ke member ditambahkan pada migrasi terakhir (relasi melingkar)
            $table->unsignedBigInteger('member_id')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user');
    }
};
