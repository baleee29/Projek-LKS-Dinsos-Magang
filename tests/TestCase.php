<?php

namespace Tests;

use App\Models\Lembaga;
use App\Models\Pengguna;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use RuntimeException;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    /** Tes menghapus isi database, jadi hanya boleh berjalan di database pengujian. */
    protected function beforeRefreshingDatabase(): void
    {
        if (config('database.default') !== 'mysql' || config('database.connections.mysql.database') !== 'sidaya_uji') {
            throw new RuntimeException('Tes hanya boleh berjalan di database sidaya_uji.');
        }
    }

    protected function admin(): Pengguna
    {
        return Pengguna::where('peran', 'admin')->firstOrFail();
    }

    /** Akun LKS contoh pertama. */
    protected function lks(): Pengguna
    {
        return Pengguna::where('peran', 'lks')->orderBy('id')->firstOrFail();
    }

    protected function lembagaLain(): Lembaga
    {
        return Lembaga::whereKeyNot($this->lks()->lembaga_id)->orderBy('nama')->firstOrFail();
    }
}
