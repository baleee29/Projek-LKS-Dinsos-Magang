<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('berkas_tersimpan', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('lembaga_id')->nullable();
            $table->string('jenis_sumber', 40);
            $table->string('id_sumber', 100);
            $table->string('nama');
            $table->string('jenis_mime', 100);
            $table->unsignedBigInteger('ukuran');
            $table->string('sha256', 64);
            $table->timestamp('dibuat_pada')->nullable();
            $table->timestamp('diperbarui_pada')->nullable();
            $table->foreign('lembaga_id')->references('id')->on('lembaga');
            $table->index(['jenis_sumber', 'id_sumber']);
        });

        Schema::create('potongan_berkas', function (Blueprint $table) {
            $table->uuid('berkas_id');
            $table->unsignedInteger('urutan');
            $table->binary('isi');
            $table->primary(['berkas_id', 'urutan']);
            $table->foreign('berkas_id')->references('id')->on('berkas_tersimpan')->cascadeOnDelete();
        });

        Schema::table('lembaga', function (Blueprint $table) {
            $table->foreign('logo_berkas_id')->references('id')->on('berkas_tersimpan')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('lembaga', fn (Blueprint $table) => $table->dropForeign(['logo_berkas_id']));
        Schema::dropIfExists('potongan_berkas');
        Schema::dropIfExists('berkas_tersimpan');
    }
};
