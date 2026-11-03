<?php

namespace App\Support;

/**
 * Menentukan menu aktif. Dipindahkan dari adminDestination(), adminGroupActive(), dan active()
 * di DashboardSidebar.vue, lalu disempitkan: Admin hanya mengelola LKS.
 */
class Navigation
{
    /** @param  array<string, mixed>  $query */
    public function __construct(
        private string $role,
        private string $workspace,
        private array $query = [],
    ) {}

    public function adminDestination(): string
    {
        $parts = explode('/', $this->workspace);
        $key = $parts[0] ?? '';
        $id = $parts[1] ?? '';

        return match (true) {
            in_array($key, ['organization', 'member'], true) => 'organizations/lks',
            $key === 'registration' => 'registrations/lks',
            in_array($key, ['resident', 'occupancy'], true) => 'occupancy',
            in_array($key, ['reports', 'report'], true) => 'reports/lks',
            $key === 'letters' => 'letters/lks',
            $key === 'notification-detail' => 'notifications',
            default => $key.($id ? '/'.$id : ''),
        };
    }

    public function groupActive(string $group): bool
    {
        $current = $this->adminDestination();

        return match ($group) {
            'work' => in_array($current, ['overview', 'notifications'], true),
            'review' => in_array($current, ['registrations/lks', 'partner-accounts'], true),
            'lks' => in_array($current, ['organizations/lks', 'occupancy', 'reports/lks', 'letters/lks'], true),
            'help' => in_array($current, ['guide', 'landing-content'], true),
            default => false,
        };
    }

    public function active(string $path): bool
    {
        if ($this->role === 'admin') {
            return $path === $this->adminDestination();
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
