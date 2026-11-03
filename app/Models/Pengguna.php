<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Pengguna extends Authenticatable
{
    use Notifiable;

    public const CREATED_AT = 'dibuat_pada';

    public const UPDATED_AT = 'diperbarui_pada';

    protected $table = 'pengguna';

    protected $guarded = [];

    protected $hidden = ['kata_sandi', 'remember_token'];

    protected function casts(): array
    {
        return [
            'kata_sandi' => 'hashed',
            'aktif' => 'boolean',
            'wajib_ganti_sandi' => 'boolean',
            'preferensi_notifikasi' => 'array',
            'versi' => 'integer',
        ];
    }

    public function getAuthPasswordName(): string
    {
        return 'kata_sandi';
    }

    public function lembaga(): BelongsTo
    {
        return $this->belongsTo(Lembaga::class, 'lembaga_id');
    }

    /** Menimpa relasi bawaan agar notifikasi tersimpan di tabel notifikasi. */
    public function notifications(): MorphMany
    {
        return $this->morphMany(Notifikasi::class, 'notifiable')->latest();
    }
}
