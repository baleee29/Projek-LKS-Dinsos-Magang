<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;

class BerkasTersimpan extends ModelDasar
{
    use HasUuids;

    protected $table = 'berkas_tersimpan';

    protected function casts(): array
    {
        return ['ukuran' => 'integer'];
    }
}
