<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Boleh kosong agar pendaftaran yang sudah masuk tetap sah.
        Schema::table('pendaftaran_lks', function (Blueprint $table) {
            $table->string('kecamatan', 100)->nullable()->after('whatsapp')->index();
            $table->string('kelurahan', 100)->nullable()->after('kecamatan');
        });
    }

    public function down(): void
    {
        Schema::table('pendaftaran_lks', function (Blueprint $table) {
            $table->dropIndex(['kecamatan']);
            $table->dropColumn(['kecamatan', 'kelurahan']);
        });
    }
};
