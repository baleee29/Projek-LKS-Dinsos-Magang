<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('klien_lks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('lembaga_id');
            $table->foreignId('pengguna_id')->nullable()->constrained('pengguna')->nullOnDelete();
            $table->string('kode', 50);
            $table->string('nama', 150);
            $table->uuid('foto_berkas_id')->nullable();
            $table->string('kategori', 50);
            $table->string('jenis_kelamin', 20);
            $table->string('tempat_lahir', 100)->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->boolean('tanggal_lahir_tidak_diketahui')->default(false);
            $table->unsignedTinyInteger('perkiraan_usia')->nullable();
            $table->string('nik', 16)->nullable();
            $table->string('nomor_kk', 16)->nullable();
            $table->string('agama', 50)->nullable();
            $table->text('alamat_asal')->nullable();
            $table->date('tanggal_masuk');
            $table->string('bentuk_layanan', 100);
            $table->string('status_penghuni', 100)->default('Masih menerima layanan');
            $table->date('tanggal_keluar')->nullable();
            $table->text('alasan_keluar')->nullable();
            $table->string('asal_rujukan', 200);
            $table->text('alasan_penerimaan');
            $table->string('status_wali', 100);
            $table->string('nama_wali', 150)->nullable();
            $table->string('hubungan_wali', 100)->nullable();
            $table->string('kontak_wali', 40)->nullable();
            $table->text('alamat_wali')->nullable();
            $table->string('status_pendidikan', 100)->nullable();
            $table->string('nama_sekolah', 150)->nullable();
            $table->string('jenjang_pendidikan', 100)->nullable();
            $table->text('kebutuhan_layanan');
            $table->text('rencana_layanan');
            $table->string('petugas_pendamping', 150);
            $table->string('status_dokumen_identitas', 50);
            $table->string('status_dokumen_penempatan', 50);
            $table->text('catatan_dokumen')->nullable();
            $table->text('catatan')->nullable();
            $table->boolean('yatim')->default(false);
            $table->boolean('piatu')->default(false);
            $table->boolean('rentan_ekonomi')->default(false);
            $table->string('status_jaminan_kesehatan', 50)->nullable();
            $table->string('nomor_jaminan_kesehatan', 13)->nullable();
            $table->string('nama_pimpinan', 150)->nullable();
            $table->string('status', 30)->default('Terdaftar');
            $table->json('riwayat')->nullable();
            $table->unsignedInteger('versi')->default(1);
            $table->timestamp('dibuat_pada')->nullable();
            $table->timestamp('diperbarui_pada')->nullable();
            $table->foreign('lembaga_id')->references('id')->on('lembaga');
            $table->foreign('foto_berkas_id')->references('id')->on('berkas_tersimpan')->nullOnDelete();
            $table->unique(['lembaga_id', 'kode']);
            $table->unique(['lembaga_id', 'nik']);
            $table->index(['lembaga_id', 'diperbarui_pada']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('klien_lks');
    }
};
