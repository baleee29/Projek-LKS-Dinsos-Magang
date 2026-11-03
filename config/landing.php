<?php

// Halaman publik yang isinya tampil di Kelola Landing Page. Slot dibaca dari panggilan $landing di tiap
// berkas Blade (App\Support\LandingRegistry), jadi isi bawaan hanya ditulis di Blade.
return [
    'pages' => [
        'home' => ['label' => 'Beranda', 'url' => '/', 'view' => 'public/home.blade.php'],
        'profile' => ['label' => 'Profil', 'url' => '/profil.html', 'view' => 'public/profile.blade.php'],
        'contact' => ['label' => 'Kontak', 'url' => '/kontak.html', 'view' => 'public/contact.blade.php'],
        'pillars' => ['label' => 'Pilar Sosial', 'url' => '/pilar-sosial.html', 'view' => 'public/pillars.blade.php'],
        'lks' => ['label' => 'LKS', 'url' => '/pilar/lks.html', 'view' => 'public/lks.blade.php'],
        'footer' => ['label' => 'Footer', 'url' => '/', 'view' => 'components/public/footer.blade.php'],
    ],
    // Kontak yang dipakai beberapa halaman; diisi satu kali.
    'shared' => ['dinsos' => 'Kontak Dinas Sosial'],
];
