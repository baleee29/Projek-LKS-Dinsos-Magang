<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pendaftaran_lks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('permintaan_id')->nullable()->unique();
            $table->string('nama_lembaga', 180);
            $table->string('nama_ketua', 150);
            $table->string('whatsapp', 30);
            $table->string('nomor_izin', 150)->nullable();
            $table->string('rekening_bank', 150)->nullable();
            $table->text('bidang_fokus');
            $table->json('prioritas_pelayanan');
            $table->json('jenis_pelayanan');
            $table->json('sasaran_pelayanan');
            $table->json('jenis_kegiatan');
            $table->json('sifat_pelayanan');
            $table->string('status', 30)->default('Menunggu Verifikasi')->index();
            $table->text('catatan')->nullable();
            $table->foreignId('ditinjau_oleh')->nullable()->constrained('pengguna')->nullOnDelete();
            $table->timestamp('ditinjau_pada')->nullable();
            $table->uuid('lembaga_id')->nullable()->index();
            $table->unsignedInteger('versi')->default(1);
            $table->timestamp('dibuat_pada')->nullable();
            $table->timestamp('diperbarui_pada')->nullable();
            $table->foreign('lembaga_id')->references('id')->on('lembaga');
        });

        Schema::create('berkas_pendaftaran', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('pendaftaran_id');
            $table->string('kunci_berkas', 50);
            $table->string('label_berkas')->nullable();
            $table->uuid('berkas_id');
            $table->timestamp('dibuat_pada')->nullable();
            $table->timestamp('diperbarui_pada')->nullable();
            $table->unique(['pendaftaran_id', 'kunci_berkas']);
            $table->foreign('pendaftaran_id')->references('id')->on('pendaftaran_lks')->cascadeOnDelete();
            $table->foreign('berkas_id')->references('id')->on('berkas_tersimpan');
        });

        Schema::table('pengguna', function (Blueprint $table) {
            $table->foreign('pendaftaran_id')->references('id')->on('pendaftaran_lks')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('pengguna', fn (Blueprint $table) => $table->dropForeign(['pendaftaran_id']));
        Schema::dropIfExists('berkas_pendaftaran');
        Schema::dropIfExists('pendaftaran_lks');
    }
};
