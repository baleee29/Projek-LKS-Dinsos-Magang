<?php

namespace App\Support;

use App\Support\Data\SkemaKlien;
use App\Support\Data\DataKlien;
use App\Support\Data\Kalender;
use App\Support\Data\DataLembaga;
use App\Support\Data\Halaman;
use App\Support\Data\Wilayah;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

/**
 * Filter dan ringkasan Rekap Klien (LKS) dan Rekap Penghuni (Admin), dari update() di lks/client-lists.js.
 * Dipakai kedua role agar logikanya hanya ada di satu tempat.
 */
class ClientRecap
{
    private const PER_PAGE = 8;

    /**
     * @param  Collection<int, array<string, mixed>>  $clients
     * @param  Collection<int, array<string, mixed>>  $organizations
     * @return array<string, mixed>
     */
    public static function build(Request $request, Collection $clients, Collection $organizations, bool $admin, string $title): array
    {
        $filters = [
            'q' => (string) $request->query('q', ''),
            'category' => (string) $request->query('category', ''),
            'gender' => (string) $request->query('gender', ''),
            'org' => $admin ? (string) $request->query('org', '') : '',
            'district' => $admin ? (string) $request->query('district', '') : '',
            'village' => $admin ? (string) $request->query('village', '') : '',
            // Bawaan sumber: hanya penghuni panti. Nilai kosong berarti semua keberadaan.
            'residence' => $request->query->has('residence') ? (string) $request->query('residence') : 'living',
        ];

        $inScope = $clients->filter(function (array $client) use ($filters) {
            $owner = DataLembaga::find($client['organizationId']);

            return (! $filters['org'] || $owner['id'] === $filters['org'])
                && (! $filters['district'] || $owner['district'] === $filters['district'])
                && (! $filters['village'] || $owner['village'] === $filters['village']);
        })->values();

        $query = mb_strtolower(trim($filters['q']));
        $items = $inScope->filter(function (array $client) use ($filters, $query, $admin) {
            $matchesResidence = match ($filters['residence']) {
                'living' => Format::living($client),
                'exited' => $client['residenceStatus'] === 'Sudah keluar',
                'nonresidential' => $client['serviceType'] === 'Layanan nonresidensial' && $client['residenceStatus'] === 'Masih menerima layanan',
                default => true,
            };
            $haystack = [$client['name'], $client['code'], $admin ? DataLembaga::find($client['organizationId'])['title'] : ''];

            return (! $filters['category'] || $client['category'] === $filters['category'])
                && (! $filters['gender'] || $client['gender'] === $filters['gender'])
                && $matchesResidence
                && (! $query || collect($haystack)->contains(fn ($value) => str_contains(mb_strtolower((string) $value), $query)));
        })->sortBy('name')->values();

        return [
            'admin' => $admin,
            'filters' => $filters,
            'counts' => DataKlien::summary($inScope),
            'total' => $inScope->count(),
            'allItems' => $items,
            'clients' => Halaman::make($items, self::PER_PAGE),
            'organizations' => $organizations,
            'districts' => Wilayah::districts(),
            'villages' => Wilayah::villages($filters['district'] ?: null),
            'options' => DataKlien::formOptions(),
            'caption' => $admin ? (DataLembaga::find($filters['org'])['title'] ?? 'Seluruh LKS') : $title,
            'today' => Kalender::today(),
            'organizationName' => fn (string $id) => DataLembaga::find($id),
        ];
    }

    /**
     * @param  array<string, mixed>  $client
     * @return array<string, mixed>
     */
    public static function detail(Request $request, array $client, bool $admin): array
    {
        $back = $request->only(['q', 'category', 'gender', 'org', 'district', 'village', 'residence', 'page']);

        return [
            'admin' => $admin,
            'item' => $client,
            'organization' => DataLembaga::find($client['organizationId']),
            'sections' => SkemaKlien::detailSections(),
            'fields' => SkemaKlien::fields(),
            'today' => Kalender::today(),
            'backUrl' => ($admin ? '/admin/occupancy' : '/lks/occupancy').($back ? '?'.http_build_query($back) : ''),
        ];
    }
}
