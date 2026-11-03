<?php

namespace App\Support\Demo;

/**
 * Tabel sumber: users (role admin), lewat GET /account/profile dan adminAccount() di shared/data.js.
 */
class DemoAdminAccount
{
    /** @return array<string, mixed> */
    public static function get(): array
    {
        return [
            'name' => 'Admin Dinas Sosial',
            'position' => 'Dinas Sosial Kota Banjarbaru',
            'email' => 'admin@sidaya.example',
            'phone' => '080000000001',
            'notificationPreferences' => [
                'newReports' => true,
                'resubmittedReports' => true,
            ],
            'version' => 1,
        ];
    }
}
