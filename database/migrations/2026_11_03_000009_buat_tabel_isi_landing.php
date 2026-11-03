<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('isi_landing', function (Blueprint $table) {
            $table->id();
            $table->string('kunci', 120)->unique();
            $table->text('nilai_teks')->nullable();
            $table->uuid('berkas_id')->nullable();
            $table->foreignId('diperbarui_oleh')->nullable()->constrained('pengguna')->nullOnDelete();
            $table->timestamp('dibuat_pada')->nullable();
            $table->timestamp('diperbarui_pada')->nullable();
            $table->foreign('berkas_id')->references('id')->on('berkas_tersimpan')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('isi_landing');
    }
};
