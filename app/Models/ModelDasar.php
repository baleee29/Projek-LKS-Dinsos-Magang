<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** Dasar model SiDaya: kolom waktu berbahasa Indonesia. Isian dipetakan satu per satu di controller. */
abstract class ModelDasar extends Model
{
    public const CREATED_AT = 'dibuat_pada';

    public const UPDATED_AT = 'diperbarui_pada';

    protected $guarded = [];
}
