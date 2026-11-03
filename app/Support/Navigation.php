<?php

namespace App\Support;

use App\Support\Demo\DemoMembers;
use App\Support\Demo\DemoOrganizations;
use App\Support\Demo\DemoPsmMembers;
use App\Support\Demo\DemoRegistrations;

/**
 * Menentukan menu aktif. Dipindahkan dari adminDestination(), adminGroupActive(), dan active()
 * di DashboardSidebar.vue. Halaman detail membaca pilarnya dari data dummy.
 */
class Navigation
{
    /** @param  array<string, mixed>  $query */
    public function __construct(
        private string $role,
        private string $workspace,
        private array $query = [],
    ) {}

    private static function organizationPath(string $pillar): string
    {
        return $pillar === 'psm' ? 'members/psm' : 'organizations/'.$pillar;
    }

    public function adminDestination(): string
    {
        $parts = explode('/', $this->workspace);
        $key = $parts[0] ?? '';
        $id = $parts[1] ?? '';

        if ($key === 'organization') {
            $pillar = DemoOrganizations::find($id)['pillar'] ?? null;

            return $pillar ? self::organizationPath($pillar) : '';
        }
        if ($key === 'member') {
            $member = DemoMembers::find($id) ?? DemoPsmMembers::find($id);
            $pillar = $member ? (DemoOrganizations::find($member['organizationId'])['pillar'] ?? null) : null;

            return $pillar ? self::organizationPath($pillar) : '';
        }
        if ($key === 'registration') {
            $pillar = DemoRegistrations::find($id)['pillar'] ?? null;

            return $pillar ? 'registrations/'.$pillar : 'registrations';
        }
        if ($key === 'incoming-letter') {
            return 'incoming-letters/karang-taruna';
        }
        if (in_array($key, ['new-reply-letter', 'reply-letter', 'edit-reply-letter'], true)) {
            return 'reply-letters/karang-taruna';
        }
        if (in_array($key, ['resident', 'occupancy'], true)) {
            return 'occupancy';
        }
        if (in_array($key, ['reports', 'report', 'edit', 'drafts'], true)) {
            return 'reports/lks';
        }
        if ($key === 'letters') {
            return 'letters/lks';
        }
        if ($key === 'complaints' && (! $id || $id === 'psm')) {
            return 'complaints/psm';
        }

        return $key.($id ? '/'.$id : '');
    }

    public function groupActive(string $group): bool
    {
        $current = $this->adminDestination();

        return match ($group) {
            'work' => in_array($current, ['overview', 'notifications', 'notification-detail'], true)
                || str_starts_with($current, 'notification-detail/'),
            'registrations' => $current === 'registrations' || str_starts_with($current, 'registrations/'),
            'review' => $this->groupActive('registrations') || $current === 'partner-accounts',
            'lks' => in_array($current, ['organizations/lks', 'occupancy', 'reports/lks', 'letters/lks', 'letters'], true),
            'psm' => in_array($current, ['members/psm', 'complaints/psm'], true),
            'letters' => in_array($current, ['incoming-letters/karang-taruna', 'reply-letters/karang-taruna'], true),
            'kt' => $current === 'organizations/karang-taruna' || $this->groupActive('letters'),
            'help' => in_array($current, ['guide', 'landing-content'], true),
            default => false,
        };
    }

    public function active(string $path): bool
    {
        if ($this->role === 'admin') {
            $destination = $this->adminDestination();

            return $path === $destination
                || ($path === 'notifications' && str_starts_with($destination, 'notification-detail'));
        }

        $parts = explode('/', $this->workspace);
        $key = $parts[0] ?? '';
        $id = $parts[1] ?? '';
        $current = $key.($id && in_array($key, ['reports', 'organizations'], true) ? '/'.$id : '');

        if ($path === $current) {
            return true;
        }

        return match ($path) {
            'reports' => in_array($key, ['report', 'edit', 'drafts'], true),
            'members' => in_array($key, ['member', 'new-member', 'edit-member'], true),
            'occupancy' => in_array($key, ['resident', 'edit-resident'], true),
            'notifications' => $key === 'notification-detail',
            default => false,
        };
    }
}
