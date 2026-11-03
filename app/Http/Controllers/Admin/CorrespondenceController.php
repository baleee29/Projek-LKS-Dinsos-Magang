<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\Demo\DemoActivities;
use App\Support\Demo\DemoClock;
use App\Support\Demo\DemoOrganizations;
use App\Support\Demo\DemoPaginator;
use App\Support\Demo\DemoReplies;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class CorrespondenceController extends Controller
{
    private const MONTHS = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

    public function incoming(Request $request): View
    {
        return $this->listing($request, false, DemoActivities::all(), 'submittedAt', DemoActivities::STATUSES);
    }

    public function replies(Request $request): View
    {
        return $this->listing($request, true, DemoReplies::all(), 'date', ['Draf', 'Diterbitkan']);
    }

    public function incomingShow(string $id): View
    {
        $item = DemoActivities::find($id);
        abort_unless($item, 404);

        return view('admin.correspondence.incoming', [
            'item' => $item,
            'organization' => DemoOrganizations::find($item['organizationId']),
            'kinds' => DemoActivities::TYPES,
            'reviewable' => in_array($item['status'], ['Menunggu Pemeriksaan', 'Ditinjau'], true),
        ]);
    }

    public function replyCreate(Request $request): View
    {
        return $this->replyForm(null, $request);
    }

    public function replyEdit(Request $request, string $id): View
    {
        $item = DemoReplies::find($id);
        abort_unless($item && $item['status'] === 'Draf', 404);

        return $this->replyForm($item, $request);
    }

    public function replyShow(string $id): View
    {
        $item = DemoReplies::find($id);
        abort_unless($item, 404);

        return view('admin.correspondence.reply', [
            'item' => $item,
            'organization' => DemoOrganizations::find($item['organizationId']),
            'activity' => $item['activityId'] ? DemoActivities::find($item['activityId']) : null,
            'ownDraft' => $item['status'] === 'Draf',
        ]);
    }

    /** @param  array<string, mixed>|null  $item */
    private function replyForm(?array $item, Request $request): View
    {
        return view('admin.correspondence.reply-form', [
            'item' => $item,
            'values' => [
                'organizationId' => $item['organizationId'] ?? (string) $request->query('org', ''),
                'activityId' => $item['activityId'] ?? (string) $request->query('activity', ''),
                'subject' => $item['subject'] ?? '',
                'number' => $item['number'] ?? '',
                'date' => $item['date'] ?? DemoClock::TODAY,
            ],
            'organizations' => DemoOrganizations::raw()->where('pillar', 'karang-taruna')->values(),
            'activities' => DemoActivities::raw(),
        ]);
    }

    /**
     * @param  Collection<int, array<string, mixed>>  $rows
     * @param  array<int, string>  $statuses
     */
    private function listing(Request $request, bool $letters, Collection $rows, string $dateKey, array $statuses): View
    {
        $filters = [
            'q' => (string) $request->query('q', ''),
            'month' => (string) $request->query('month', ''),
            'year' => (string) $request->query('year', ''),
            'status' => (string) $request->query('status', ''),
            'org' => (string) $request->query('org', ''),
        ];
        $query = mb_strtolower(trim($filters['q']));
        $filtered = $rows->filter(function (array $row) use ($filters, $query, $dateKey, $letters) {
            $org = DemoOrganizations::find($row['organizationId']);
            $date = (string) $row[$dateKey];
            $haystack = $letters ? [$row['subject'], $row['number'], $org['title']] : [$row['title'], $row['letterNumber'], $org['title']];

            return (! $filters['status'] || $row['status'] === $filters['status'])
                && (! $filters['org'] || $row['organizationId'] === $filters['org'])
                && (! $filters['year'] || substr($date, 0, 4) === $filters['year'])
                && (! $filters['month'] || (int) substr($date, 5, 2) === (int) $filters['month'])
                && (! $query || collect($haystack)->contains(fn ($value) => str_contains(mb_strtolower((string) $value), $query)));
        })->sortByDesc($dateKey)->values();

        return view('admin.correspondence.index', [
            'letters' => $letters,
            'pageTitle' => $letters ? 'Surat Balasan' : 'Surat Masuk',
            'description' => $letters
                ? 'Draf milik Anda dan surat yang telah diterbitkan kepada organisasi penerima.'
                : 'Pengajuan kegiatan Karang Taruna yang telah dikirim. Draf mitra tidak masuk antrean Admin.',
            'filters' => $filters,
            'months' => self::MONTHS,
            'years' => [DemoClock::YEAR, DemoClock::YEAR - 1],
            'statuses' => $statuses,
            'organizations' => DemoOrganizations::raw()->where('pillar', 'karang-taruna')->values(),
            'hasRows' => $rows->isNotEmpty(),
            'rows' => DemoPaginator::make($filtered, 10),
            'dateKey' => $dateKey,
            'kinds' => DemoActivities::TYPES,
            'organizationOf' => fn (string $id) => DemoOrganizations::find($id),
            'activityOf' => fn (?string $id) => $id ? DemoActivities::find($id) : null,
        ]);
    }
}
