<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BerkasPendaftaran extends ModelDasar
{
    use HasUuids;

    protected $table = 'berkas_pendaftaran';

    public function berkas(): BelongsTo
    {
        return $this->belongsTo(BerkasTersimpan::class, 'berkas_id');
    }
}
