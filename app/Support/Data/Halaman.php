<?php

namespace App\Support\Data;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

/** Paginasi koleksi. Halaman di luar jangkauan dijepit ke halaman terakhir. */
class Halaman
{
    /** @param  Collection<int, mixed>  $items */
    public static function make(Collection $items, int $perPage): LengthAwarePaginator
    {
        $lastPage = max(1, (int) ceil($items->count() / $perPage));
        $page = min($lastPage, max(1, (int) request()->query('page', 1)));

        return (new LengthAwarePaginator(
            $items->forPage($page, $perPage)->values(),
            $items->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'pageName' => 'page'],
        ))->withQueryString();
    }
}
