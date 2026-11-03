<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;

class PengurusLks extends ModelDasar
{
    use HasUuids;

    protected $table = 'pengurus_lks';

    protected function casts(): array
    {
        return ['tanggal_lahir' => 'date:Y-m-d', 'tanggal_bergabung' => 'date:Y-m-d', 'ketua' => 'boolean', 'versi' => 'integer'];
    }
}
