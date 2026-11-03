<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lembaga', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('nama', 180);
            $table->string('ketua', 150)->nullable();
            $table->text('alamat')->nullable();
            $table->string('kontak', 40)->nullable();
            $table->string('kecamatan', 100)->nullable();
            $table->string('kelurahan', 100)->nullable();
            $table->text('legalitas')->nullable();
            $table->string('masa_bakti', 150)->nullable();
            $table->date('terdaftar_sejak')->nullable();
            $table->text('deskripsi')->nullable();
            $table->uuid('logo_berkas_id')->nullable();
            $table->decimal('lintang', 10, 7)->nullable();
            $table->decimal('bujur', 10, 7)->nullable();
            $table->unsignedInteger('versi')->default(1);
            $table->timestamp('dibuat_pada')->nullable();
            $table->timestamp('diperbarui_pada')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lembaga');
    }
};
