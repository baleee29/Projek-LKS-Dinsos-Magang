<?php

namespace App\Support\Demo;

/**
 * Tabel sumber: users dengan role lks, karang-taruna, dan psm (lewat GET /admin/partner-accounts/data).
 * Kata sandi tidak pernah menjadi bagian data dummy.
 */
class DemoAccounts extends DemoRepository
{
    public const ROLE_BADGES = ['lks' => 'LKS', 'karang-taruna' => 'Karang Taruna', 'psm' => 'Ketua PSM'];

    protected static function seed(): array
    {
        $partners = DemoOrganizations::raw()->where('pillar', 'lks')->take(4)
            ->merge(DemoOrganizations::raw()->where('pillar', 'karang-taruna'))
            ->merge(DemoOrganizations::raw()->where('pillar', 'psm'));

        $rows = [];
        foreach ($partners->values() as $index => $org) {
            $n = $index + 1;
            $rows[] = [
                'id' => 'acc-'.$org['id'],
                'name' => $org['title'],
                'role' => $org['pillar'],
                'username' => str_replace('-', '_', $org['id']).'_demo',
                'email' => $org['id'].'@sidaya.example',
                'phone' => $org['id'] === 'kt-003' ? '' : $org['contact'],
                'created_at' => sprintf('2026-%02d-%02d', ($n % 9) + 1, ($n % 27) + 1),
            ];
        }

        return $rows;
    }
}
