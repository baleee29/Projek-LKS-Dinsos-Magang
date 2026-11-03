<?php

namespace App\Support\Demo;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

/**
 * Paginasi server untuk koleksi dummy. Halaman di luar jangkauan dijepit ke halaman terakhir,
 * sama seperti Math.min(pages, Math.max(1, page)) di modul sumber.
 *
 * TODO(Tahap 2): ganti dengan $query->paginate($perPage)->withQueryString().
 */
class DemoPaginator
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
