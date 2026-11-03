<?php

namespace Database\Seeders\Contoh;

/**
 * Tabel sumber: users dengan role lks (lewat GET /admin/partner-accounts/data).
 * Kata sandi tidak pernah menjadi bagian data dummy.
 */
class DemoAccounts extends DemoRepository
{
    protected static function seed(): array
    {
        $rows = [];
        foreach (DemoOrganizations::raw()->values() as $index => $org) {
            $n = $index + 1;
            $rows[] = [
                'id' => 'acc-'.$org['id'],
                'name' => $org['title'],
                'role' => 'lks',
                'username' => str_replace('-', '_', $org['id']).'_demo',
                'email' => $org['id'].'@sidaya.example',
                'phone' => $org['id'] === 'lks-006' ? '' : $org['contact'],
                'created_at' => sprintf('2026-%02d-%02d', ($n % 9) + 1, ($n % 27) + 1),
            ];
        }

        return $rows;
    }
}
