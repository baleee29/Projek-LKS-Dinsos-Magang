<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PendaftaranLks extends ModelDasar
{
    use HasUuids;

    public const MENUNGGU = 'Menunggu Verifikasi';

    public const DISETUJUI = 'Disetujui';

    public const DITOLAK = 'Ditolak';

    protected $table = 'pendaftaran_lks';

    protected function casts(): array
    {
        return [
            'prioritas_pelayanan' => 'array', 'jenis_pelayanan' => 'array', 'sasaran_pelayanan' => 'array',
            'jenis_kegiatan' => 'array', 'sifat_pelayanan' => 'array', 'ditinjau_pada' => 'datetime', 'versi' => 'integer',
        ];
    }

    public function berkas(): HasMany
    {
        return $this->hasMany(BerkasPendaftaran::class, 'pendaftaran_id');
    }

    public function akun(): HasOne
    {
        return $this->hasOne(Pengguna::class, 'pendaftaran_id');
    }
}
