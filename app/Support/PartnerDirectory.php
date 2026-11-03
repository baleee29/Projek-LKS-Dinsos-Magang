<?php

namespace App\Support;

use App\Support\Demo\DemoMembers;
use App\Support\Demo\DemoOrganizations;
use App\Support\Demo\DemoPaginator;
use App\Support\Demo\DemoPsmMembers;
use App\Support\Demo\DemoRegions;
use Illuminate\Http\Request;

/**
 * Direktori Admin dari admin/partner-workspace.js: Data LKS, Data Kelompok, dan Data Anggota PSM.
 */
class PartnerDirectory
{
    private const PER_PAGE = 10;

    /** @return array<string, mixed> */
    public static function organizations(Request $request, string $pillar): array
    {
        $filters = self::filters($request);
        $query = mb_strtolower(trim($filters['q']));
        $rows = DemoOrganizations::byPillar($pillar)
            ->filter(fn (array $org) => (! $filters['district'] || $org['district'] === $filters['district'])
                && (! $filters['village'] || $org['village'] === $filters['village'])
                && (! $query || collect([$org['title'], $org['chair'], $org['contact']])->contains(fn ($value) => str_contains(mb_strtolower((string) $value), $query))))
            ->values();

        return [
            'pillar' => $pillar,
            'title' => $pillar === 'lks' ? 'Data LKS' : 'Data Kelompok Karang Taruna',
            'description' => $pillar === 'lks'
                ? 'Direktori lembaga kesejahteraan sosial beserta SDM yang tercatat.'
                : 'Direktori kelompok Karang Taruna beserta keanggotaan operasionalnya.',
            'filters' => $filters,
            'districts' => DemoRegions::districts(),
            'villages' => DemoRegions::villages($filters['district'] ?: null),
            'organizations' => DemoPaginator::make($rows, self::PER_PAGE),
            'rosterOf' => fn (string $id) => DemoMembers::raw()->where('organizationId', $id)->values(),
        ];
    }

    /** @return array<string, mixed> */
    public static function psmMembers(Request $request): array
    {
        $filters = self::filters($request);
        $query = mb_strtolower(trim($filters['q']));
        $rows = DemoPsmMembers::all()
            ->filter(fn (array $member) => (! $filters['district'] || $member['district'] === $filters['district'])
                && (! $filters['village'] || $member['village'] === $filters['village'])
                && (! $query || collect([$member['title'], $member['contact'], $member['district'], $member['village']])->contains(fn ($value) => str_contains(mb_strtolower((string) $value), $query))))
            ->values();

        return [
            'filters' => $filters,
            'districts' => DemoRegions::districts(),
            'villages' => DemoRegions::villages($filters['district'] ?: null),
            'members' => DemoPaginator::make($rows, self::PER_PAGE),
        ];
    }

    /** @return array{q: string, district: string, village: string} */
    private static function filters(Request $request): array
    {
        return [
            'q' => (string) $request->query('q', ''),
            'district' => (string) $request->query('district', ''),
            'village' => (string) $request->query('village', ''),
        ];
    }
}
