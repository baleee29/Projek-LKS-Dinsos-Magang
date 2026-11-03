<?php

namespace App\Support;

use App\Support\Data\Kalender;
use App\Support\Data\DataLembaga;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

/**
 * Filter persuratan kedua role, dari lks/letters.js dan lks/letters-view.js.
 * Bagi Admin, tab "incoming" adalah surat dari LKS (type outgoing di tabel), dan sebaliknya.
 */
class LetterListing
{
    private const MONTHS = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

    /**
     * @param  Collection<int, array<string, mixed>>  $letters
     * @return array<string, mixed>
     */
    public static function build(Request $request, Collection $letters, bool $admin): array
    {
        $tab = $request->query('tab') === 'outgoing' ? 'outgoing' : 'incoming';
        $filters = [
            'q' => (string) $request->query('q', ''),
            'org' => $admin ? (string) $request->query('org', 'all') : 'all',
            'month' => (string) $request->query('month', 'all'),
            'year' => (string) $request->query('year', 'all'),
        ];

        $typeOfTab = fn (string $name) => $admin ? ($name === 'incoming' ? 'outgoing' : 'incoming') : $name;
        $query = mb_strtolower(trim($filters['q']));

        $shown = $letters
            ->where('type', $typeOfTab($tab))
            ->filter(fn (array $letter) => ($filters['org'] === 'all' || $letter['organization_id'] === $filters['org'])
                && ($filters['month'] === 'all' || (int) substr($letter['letter_date'], 5, 2) === (int) $filters['month'])
                && ($filters['year'] === 'all' || substr($letter['letter_date'], 0, 4) === $filters['year'])
                && (! $query || collect([$letter['letter_number'], $letter['subject'], $letter['description']])->contains(fn ($value) => str_contains(mb_strtolower((string) $value), $query))))
            ->sortByDesc('letter_date')
            ->values();

        return [
            'admin' => $admin,
            'tab' => $tab,
            'isOutgoing' => $tab === 'outgoing',
            'filters' => $filters,
            'letters' => $shown,
            'counts' => [
                'incoming' => $letters->where('type', $typeOfTab('incoming'))->count(),
                'outgoing' => $letters->where('type', $typeOfTab('outgoing'))->count(),
            ],
            'months' => self::MONTHS,
            'years' => [Kalender::year(), Kalender::year() - 1, Kalender::year() - 2],
            'organizations' => $admin ? DataLembaga::raw() : collect(),
            'today' => Kalender::today(),
            'hasActiveFilter' => ($admin && $filters['org'] !== 'all') || $filters['month'] !== 'all' || $filters['year'] !== 'all' || trim($filters['q']) !== '',
            'rolePrefix' => $admin ? '/admin/letters/lks' : '/lks/letters',
            // Balas surat: form terbuka dengan isian awal, dari handleReplyToLetter() di lks/letters-actions.js.
            'reply' => $admin && $tab === 'outgoing' ? $letters->where('type', 'outgoing')->firstWhere('id', $request->query('reply')) : null,
        ];
    }
}
