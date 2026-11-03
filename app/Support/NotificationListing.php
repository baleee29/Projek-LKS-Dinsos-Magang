<?php

namespace App\Support;

use App\Support\Demo\DemoNotifications;
use App\Support\Demo\DemoOrganizations;
use App\Support\Demo\DemoPaginator;
use Illuminate\Http\Request;

/**
 * Daftar notifikasi kedua role, dari admin/notifications.js dan shared/workspace-notifications.js.
 */
class NotificationListing
{
    private const PER_PAGE = 10;

    /** @return array<string, mixed> */
    public static function build(Request $request, string $role): array
    {
        $all = DemoNotifications::forRole($role);
        $filters = [
            'q' => (string) $request->query('q', ''),
            'filter' => in_array($request->query('filter'), ['unread', 'read'], true) ? $request->query('filter') : 'all',
        ];
        $query = mb_strtolower(trim($filters['q']));

        $items = $all->filter(fn (array $item) => ($filters['filter'] === 'all' || ($filters['filter'] === 'unread' ? ! $item['read'] : $item['read']))
            && (! $query || collect([$item['title'], $item['message'], $item['organizationTitle'], $item['periodLabel']])
                ->contains(fn ($value) => str_contains(mb_strtolower((string) $value), $query))))->values();

        $unread = $all->where('read', false)->count();

        return [
            'admin' => $role === 'admin',
            'eyebrow' => $role === 'admin' ? 'Dinas Sosial Kota Banjarbaru' : DemoOrganizations::own()['title'],
            'all' => $all,
            'unreadCount' => $unread,
            'filters' => $filters,
            'counts' => ['all' => $all->count(), 'unread' => $unread, 'read' => $all->count() - $unread],
            'notifications' => DemoPaginator::make($items, self::PER_PAGE),
        ];
    }

    /** @return array<string, mixed> */
    public static function detail(string $id, string $role): array
    {
        $item = DemoNotifications::find($id);
        abort_unless($item && $item['audience'] === $role, 404);

        return [
            'item' => $item,
            'backUrl' => "/{$role}/notifications",
            // Label dari renderDetail() di shared/notifications.js.
            'labels' => [
                'organization_name' => 'Nama organisasi', 'leader_name' => 'Ketua', 'title' => 'Judul', 'subject' => 'Perihal',
                'name' => 'Nama', 'status' => 'Status', 'notes' => 'Catatan', 'letter_number' => 'Nomor surat', 'year' => 'Tahun',
                'quarter' => 'Periode', 'district' => 'Kecamatan', 'village' => 'Kelurahan', 'address' => 'Alamat',
            ],
        ];
    }
}
