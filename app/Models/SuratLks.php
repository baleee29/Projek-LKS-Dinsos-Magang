<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SuratLks extends ModelDasar
{
    use HasUuids;

    public const DINAS_KE_LKS = 'dinas_ke_lks';

    public const LKS_KE_DINAS = 'lks_ke_dinas';

    protected $table = 'surat_lks';

    protected function casts(): array
    {
        return ['tanggal_surat' => 'date:Y-m-d', 'tanggal_diterima' => 'date:Y-m-d', 'dibaca_pada' => 'datetime'];
    }

    public function berkas(): BelongsTo
    {
        return $this->belongsTo(BerkasTersimpan::class, 'berkas_id');
    }
}
