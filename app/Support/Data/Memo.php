<?php

namespace App\Support\Data;

use Closure;

/** Cache sepanjang satu request, supaya satu halaman tidak mengulang query yang sama. */
class Memo
{
    public static function ingat(string $kunci, Closure $ambil): mixed
    {
        $tempat = request()->attributes;
        if (! $tempat->has('memo.'.$kunci)) {
            $tempat->set('memo.'.$kunci, $ambil());
        }

        return $tempat->get('memo.'.$kunci);
    }
}
