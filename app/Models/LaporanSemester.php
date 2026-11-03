<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;

class LaporanSemester extends ModelDasar
{
    use HasUuids;

    public const DRAF = 'Draf';

    public const TERKIRIM = 'Terkirim';

    protected $table = 'laporan_semester';

    protected function casts(): array
    {
        return ['data' => 'array', 'draf_data' => 'array', 'lampiran' => 'array', 'riwayat' => 'array', 'dikirim_pada' => 'datetime', 'versi' => 'integer'];
    }
}
