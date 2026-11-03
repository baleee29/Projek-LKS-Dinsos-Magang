<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengguna', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 150);
            $table->string('nama_pengguna', 60)->nullable()->unique();
            $table->string('email')->unique();
            $table->string('telepon', 30)->nullable();
            $table->string('kata_sandi');
            $table->rememberToken();
            $table->string('peran', 20)->index();
            $table->string('jabatan', 150)->nullable();
            $table->uuid('lembaga_id')->nullable()->index();
            $table->uuid('pendaftaran_id')->nullable()->unique();
            $table->boolean('aktif')->default(true);
            $table->boolean('wajib_ganti_sandi')->default(false);
            $table->json('preferensi_notifikasi')->nullable();
            $table->unsignedInteger('versi')->default(1);
            $table->timestamp('dibuat_pada')->nullable();
            $table->timestamp('diperbarui_pada')->nullable();
            $table->foreign('lembaga_id')->references('id')->on('lembaga');
        });

        // Kolom mengikuti bawaan Laravel karena handler sesi menulis ke nama kolom ini.
        Schema::create('sesi', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sesi');
        Schema::dropIfExists('pengguna');
    }
};
