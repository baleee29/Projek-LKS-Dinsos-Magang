<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

/** Notifikasi database. Ditulis dalam transaksi yang sama dengan perubahan datanya. */
class NotifikasiDashboard extends Notification
{
    /** @param  array<string, mixed>  $isi  title, message, organizationTitle, periodLabel, url, note, fields */
    public function __construct(private array $isi) {}

    /** @return array<int, string> */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /** @return array<string, mixed> */
    public function toArray(object $notifiable): array
    {
        return $this->isi;
    }
}
