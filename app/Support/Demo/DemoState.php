<?php

namespace App\Support\Demo;

/**
 * Pratinjau ?demo_state=empty|error, hanya saat APP_ENV=local.
 *
 * TODO(Tahap 2): hapus class ini bersama seluruh App\Support\Demo.
 */
class DemoState
{
    public static function current(): ?string
    {
        if (! app()->environment('local')) {
            return null;
        }

        $state = request()->query('demo_state');

        return in_array($state, ['empty', 'error'], true) ? $state : null;
    }

    public static function isEmpty(): bool
    {
        return self::current() === 'empty';
    }

    public static function isError(): bool
    {
        return self::current() === 'error';
    }
}
