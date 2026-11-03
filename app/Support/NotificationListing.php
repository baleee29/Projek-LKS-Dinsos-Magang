<?php

namespace App\Support;

use App\Support\Data\DataNotifikasi;
use App\Support\Data\DataLembaga;
use App\Support\Data\Halaman;
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
        $all = DataNotifikasi::milikSaya();
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
            'eyebrow' => $role === 'admin' ? 'Dinas Sosial Kota Banjarbaru' : DataLembaga::own()['title'],
            'all' => $all,
            'unreadCount' => $unread,
            'filters' => $filters,
            'counts' => ['all' => $all->count(), 'unread' => $unread, 'read' => $all->count() - $unread],
            'notifications' => Halaman::make($items, self::PER_PAGE),
        ];
    }

    /** @return array<string, mixed> */
    public static function detail(string $id, string $role): array
    {
        $item = DataNotifikasi::find($id);
        abort_unless($item, 404);
        auth()->user()->notifications()->whereKey($id)->whereNull('read_at')->update(['read_at' => now()]);

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
