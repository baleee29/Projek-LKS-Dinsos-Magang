<?php

namespace App\Support\Demo;

use App\Support\Format;

/**
 * Identitas topbar/sidebar dan lencana menu. Lencana dihitung dari data dummy yang sama dengan
 * isi tabelnya; polling 15 detik dari Dashboard.vue tidak dibawa.
 *
 * TODO(Tahap 2): baca dari auth()->user() dan window.SidayaWorkflowSummary versi server.
 */
class DemoShell
{
    /** @return array{name: string, organization: string, initials: string, account: string} */
    public static function identity(string $role): array
    {
        if ($role === 'admin') {
            $account = DemoAdminAccount::get();

            return [
                'name' => $account['name'],
                'organization' => $account['position'],
                'initials' => Format::initials($account['name']),
                'account' => $account['name'],
            ];
        }

        $org = DemoOrganizations::own();

        return [
            'name' => $org['chair'],
            'organization' => $org['title'],
            'initials' => 'L',
            'account' => $org['title'],
        ];
    }

    /** @return array<string, int|null> */
    public static function counts(string $role): array
    {
        if ($role === 'admin') {
            return [
                'unread' => DemoNotifications::forRole('admin')->where('read', false)->count(),
                'registrationsLKS' => self::orNull(DemoRegistrations::pending('lks')),
                'registrationsKT' => self::orNull(DemoRegistrations::pending('karang-taruna')),
                'incomingLetters' => self::orNull(DemoActivities::pending()->count()),
                'replyDrafts' => self::orNull(DemoReplies::all()->where('status', 'Draf')->count()),
                'unreadLettersLKS' => self::orNull(DemoLetters::unreadForAdmin()),
            ];
        }

        return [
            'unread' => DemoNotifications::forRole('lks')->where('read', false)->count(),
            'unreadLetters' => self::orNull(DemoLetters::unreadForLks()),
        ];
    }

    /** Sama seperti sumber: lencana 0 tidak ditampilkan. */
    private static function orNull(int $value): ?int
    {
        return $value ?: null;
    }
}
