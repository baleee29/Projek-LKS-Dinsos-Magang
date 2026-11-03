<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('laporan_semester', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('lembaga_id');
            $table->unsignedSmallInteger('tahun');
            $table->unsignedTinyInteger('semester');
            $table->string('judul')->nullable();
            $table->string('status', 20)->default('Draf')->index();
            $table->text('catatan')->nullable();
            $table->json('data')->nullable();
            $table->json('draf_data')->nullable();
            $table->json('lampiran')->nullable();
            $table->json('riwayat')->nullable();
            $table->foreignId('dikirim_oleh')->nullable()->constrained('pengguna')->nullOnDelete();
            $table->timestamp('dikirim_pada')->nullable();
            $table->unsignedInteger('versi')->default(1);
            $table->timestamp('dibuat_pada')->nullable();
            $table->timestamp('diperbarui_pada')->nullable();
            $table->foreign('lembaga_id')->references('id')->on('lembaga');
            $table->unique(['lembaga_id', 'tahun', 'semester']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laporan_semester');
    }
};
