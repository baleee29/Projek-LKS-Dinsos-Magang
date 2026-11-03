<?php

// Menu sidebar Admin dan LKS, disalin dari DashboardSidebar.vue sumber.
// count: kunci lencana dari App\Support\Demo\DemoShell::counts(). unread: lencana hanya tampil bila > 0.
return [
    'role_names' => ['admin' => 'ADMIN', 'lks' => 'LKS'],

    'admin' => [
        [
            'type' => 'section', 'key' => 'work', 'label' => 'Ruang Kerja',
            'items' => [
                ['path' => 'overview', 'label' => 'Ringkasan', 'icon' => 'dashboard'],
                ['path' => 'notifications', 'label' => 'Notifikasi', 'icon' => 'bell', 'count' => 'unread', 'unread' => true],
            ],
        ],
        [
            'type' => 'section', 'key' => 'review', 'label' => 'Penerimaan & Verifikasi',
            'items' => [
                [
                    'type' => 'group', 'key' => 'registrations', 'id' => 'nav-admin-registrations', 'label' => 'Pendaftaran Mitra', 'icon' => 'file',
                    'items' => [
                        ['path' => 'registrations/lks', 'label' => 'LKS', 'count' => 'registrationsLKS'],
                        ['path' => 'registrations/karang-taruna', 'label' => 'Karang Taruna', 'count' => 'registrationsKT'],
                    ],
                ],
                ['path' => 'partner-accounts', 'label' => 'Buat Akun Mitra', 'icon' => 'users'],
            ],
        ],
        [
            'type' => 'pillar', 'key' => 'lks', 'title' => 'LKS', 'description' => 'Lembaga Kesejahteraan Sosial', 'icon' => 'building',
            'items' => [
                ['path' => 'organizations/lks', 'label' => 'Data LKS', 'icon' => 'building'],
                ['path' => 'occupancy', 'label' => 'Rekap Penghuni', 'icon' => 'users'],
                ['path' => 'reports/lks', 'label' => 'Laporan Semester', 'icon' => 'file'],
                ['path' => 'letters/lks', 'label' => 'Surat Masuk & Keluar', 'icon' => 'envelope', 'count' => 'unreadLettersLKS'],
            ],
        ],
        [
            'type' => 'pillar', 'key' => 'psm', 'title' => 'PSM', 'description' => 'Pekerja Sosial Masyarakat', 'logo' => '/assets/images/branding/logo-psm-96.webp',
            'items' => [
                ['path' => 'members/psm', 'label' => 'Data Anggota', 'icon' => 'users'],
                ['path' => 'complaints/psm', 'label' => 'Pengaduan Anggota', 'icon' => 'envelope'],
            ],
        ],
        [
            'type' => 'pillar', 'key' => 'kt', 'title' => 'Karang Taruna', 'description' => 'Kelompok & surat pengajuan', 'logo' => '/assets/images/branding/logo-karang-taruna-96.webp',
            'items' => [
                ['path' => 'organizations/karang-taruna', 'label' => 'Data Kelompok', 'icon' => 'building'],
                [
                    'type' => 'group', 'key' => 'letters', 'id' => 'nav-admin-kt-letters', 'label' => 'Surat Pengajuan', 'icon' => 'file',
                    'items' => [
                        ['path' => 'incoming-letters/karang-taruna', 'label' => 'Surat Masuk', 'count' => 'incomingLetters'],
                        ['path' => 'reply-letters/karang-taruna', 'label' => 'Surat Balasan', 'count' => 'replyDrafts'],
                    ],
                ],
            ],
        ],
        [
            'type' => 'section', 'key' => 'help', 'label' => 'Bantuan',
            'items' => [
                ['path' => 'guide', 'label' => 'Panduan Admin', 'icon' => 'info'],
                ['path' => 'landing-content', 'label' => 'Kelola Landing Page', 'icon' => 'settings'],
            ],
        ],
    ],

    'lks' => [
        [
            'heading' => 'Ruang kerja',
            'items' => [
                ['path' => 'overview', 'label' => 'Ringkasan', 'icon' => 'dashboard'],
                ['path' => 'notifications', 'label' => 'Notifikasi', 'icon' => 'bell', 'count' => 'unread', 'unread' => true],
            ],
        ],
        [
            'heading' => 'Organisasi',
            'items' => [
                ['path' => 'profile', 'label' => 'Profil & Legalitas', 'icon' => 'building'],
                ['path' => 'members', 'label' => 'Pengurus & SDM', 'icon' => 'users'],
                ['path' => 'letters', 'label' => 'Surat Masuk & Keluar', 'icon' => 'envelope', 'count' => 'unreadLetters'],
            ],
        ],
        [
            'heading' => 'Pendataan',
            'items' => [
                ['path' => 'new-resident', 'label' => 'Tambah Klien', 'icon' => 'heart'],
                ['path' => 'occupancy', 'label' => 'Rekap Klien', 'icon' => 'users'],
            ],
        ],
        [
            'heading' => 'Pelaporan',
            'items' => [
                ['path' => 'reports', 'label' => 'Laporan Semester', 'icon' => 'file'],
            ],
        ],
        [
            'heading' => 'Bantuan',
            'items' => [
                ['path' => 'guide', 'label' => 'Panduan Pelaporan', 'icon' => 'info'],
            ],
        ],
    ],
];
