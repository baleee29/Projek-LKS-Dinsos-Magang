<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengurus_lks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('lembaga_id')->index();
            $table->string('nama', 150);
            $table->string('nik', 16)->nullable();
            $table->string('tempat_lahir', 150)->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->string('jenis_kelamin', 20)->nullable();
            $table->string('kontak', 40)->nullable();
            $table->text('alamat_ktp')->nullable();
            $table->string('npwp', 150)->nullable();
            $table->string('pekerjaan', 100)->nullable();
            $table->string('kategori', 50);
            $table->string('jabatan', 150)->nullable();
            $table->date('tanggal_bergabung')->nullable();
            $table->text('catatan')->nullable();
            $table->boolean('ketua')->default(false);
            $table->uuid('foto_berkas_id')->nullable();
            $table->unsignedInteger('versi')->default(1);
            $table->timestamp('dibuat_pada')->nullable();
            $table->timestamp('diperbarui_pada')->nullable();
            $table->foreign('lembaga_id')->references('id')->on('lembaga');
            $table->foreign('foto_berkas_id')->references('id')->on('berkas_tersimpan')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengurus_lks');
    }
};
