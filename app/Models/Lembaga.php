<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Lembaga extends ModelDasar
{
    use HasUuids;

    protected $table = 'lembaga';

    protected function casts(): array
    {
        return ['terdaftar_sejak' => 'date:Y-m-d', 'lintang' => 'float', 'bujur' => 'float', 'versi' => 'integer'];
    }
}
