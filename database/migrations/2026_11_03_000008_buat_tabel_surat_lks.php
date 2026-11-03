<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('surat_lks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('lembaga_id');
            $table->foreignId('pengguna_id')->nullable()->constrained('pengguna')->nullOnDelete();
            $table->string('arah', 20);
            $table->string('nomor_surat', 120)->nullable()->index();
            $table->string('pengirim')->nullable();
            $table->string('tujuan')->nullable();
            $table->string('perihal');
            $table->date('tanggal_surat');
            $table->date('tanggal_diterima')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamp('dibaca_pada')->nullable();
            $table->uuid('berkas_id')->nullable();
            $table->timestamp('dibuat_pada')->nullable();
            $table->timestamp('diperbarui_pada')->nullable();
            $table->foreign('lembaga_id')->references('id')->on('lembaga');
            $table->foreign('berkas_id')->references('id')->on('berkas_tersimpan')->nullOnDelete();
            $table->index(['lembaga_id', 'arah', 'tanggal_surat']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('surat_lks');
    }
};
