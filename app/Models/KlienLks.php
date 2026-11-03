<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;

class KlienLks extends ModelDasar
{
    use HasUuids;

    protected $table = 'klien_lks';

    protected function casts(): array
    {
        return [
            'tanggal_lahir' => 'date:Y-m-d', 'tanggal_masuk' => 'date:Y-m-d', 'tanggal_keluar' => 'date:Y-m-d',
            'tanggal_lahir_tidak_diketahui' => 'boolean', 'yatim' => 'boolean', 'piatu' => 'boolean', 'rentan_ekonomi' => 'boolean',
            'riwayat' => 'array', 'versi' => 'integer',
        ];
    }
}
