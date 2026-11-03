@extends('layouts.dashboard')

@section('title', 'Ringkasan Admin')

@php
    use App\Support\Format;
    $pillarLabels = ['psm' => 'PSM', 'lks' => 'LKS', 'karang-taruna' => 'Karang Taruna'];
    $pillarUrls = ['psm' => '/admin/members/psm', 'lks' => '/admin/organizations/lks', 'karang-taruna' => '/admin/organizations/karang-taruna'];
    $arrow = '<svg class="icon" aria-hidden="true"><use href="#icon-arrow"/></svg>';
    $link = fn (string $href, string $title) => '<a class="q-admin-link" href="'.e($href).'">'.e($title).$arrow.'</a>';
    $facts = function (array $rows) {
        $html = '<dl class="q-admin-facts">';
        foreach ($rows as $row) {
            $value = isset($row[2]) ? '<a href="'.e($row[2]).'">'.e($row[1]).'</a>' : e($row[1]);
            $html .= '<div><dt>'.e($row[0]).'</dt><dd>'.$value.'</dd></div>';
        }

        return $html.'</dl>';
    };
    $status = function (string $value) {
        $tones = ['Terkirim' => 'approved', 'Diajukan' => 'pending', 'Ditinjau' => 'progress', 'Perlu Revisi' => 'revision', 'Disetujui' => 'approved', 'Diterima' => 'approved', 'Menunggu Persetujuan' => 'pending', 'Menunggu Verifikasi' => 'pending', 'Menunggu Pemeriksaan' => 'pending', 'Perlu Perbaikan' => 'revision'];

        return '<span class="q-admin-status q-admin-status-'.($tones[$value] ?? 'neutral').'">'.e($value).'</span>';
    };
    $icon = fn (string $name) => '<svg class="icon q-admin-card-icon" viewBox="0 0 24 24" aria-hidden="true"><use href="#icon-'.$name.'"/></svg>';
    $excerpt = fn (?string $value) => mb_strimwidth((string) $value, 0, 180, '…');
    $queueCards = [
        ['Formulir terbuka', 'Pendaftaran LKS', $queues['registrationsLKS'], 'Pendaftaran lembaga beserta berkas yang dikirim melalui formulir publik.', '/admin/registrations/lks', 'Periksa pendaftaran LKS'],
        ['Formulir terbuka', 'Pendaftaran Karang Taruna', $queues['registrationsKT'], 'Pendaftaran kelompok dan kelengkapan berkas yang menunggu pemeriksaan.', '/admin/registrations/karang-taruna', 'Periksa pendaftaran kelompok'],
        ['Agenda mitra', 'Surat masuk kegiatan', $queues['incomingLetters'], 'Pengajuan Karang Taruna yang menunggu pemeriksaan atau sedang ditinjau.', '/admin/incoming-letters/karang-taruna', 'Periksa surat masuk'],
        ['Administrasi', 'Draf surat balasan', $queues['replyDrafts'], 'Draf milik akun Admin ini belum terlihat oleh penerima.', '/admin/reply-letters/karang-taruna', 'Buka surat balasan'],
    ];
@endphp

@section('content')
    <div class="q-admin-overview">
        <div class="q-admin-overview-toolbar">
            <header class="q-admin-overview-heading"><h1 id="view-title" tabindex="-1">Ringkasan Admin</h1><p class="view-description">Pantau berkas masuk, tiga pilar sosial, dan progres laporan semester.</p></header>
            <form class="q-admin-period" method="get" aria-label="Periode laporan LKS">
                <label><span>Tahun anggaran</span><select name="year" aria-label="Tahun">@foreach ($years as $value)<option value="{{ $value }}" @selected($value === $year)>{{ $value }}</option>@endforeach</select></label>
                <label><span>Periode pelaporan</span><select name="quarter" aria-label="Semester"><option value="1" @selected($semester === 1)>Semester 1 · Januari–Juni</option><option value="2" @selected($semester === 2)>Semester 2 · Juli–Desember</option></select></label>
                <button class="q-admin-apply-period" type="submit"><svg class="icon q-admin-card-icon" viewBox="0 0 24 24" aria-hidden="true"><path d="M3 5h18l-7 8v6l-4 2v-8Z"/></svg>Terapkan</button>
            </form>
        </div>
        <section class="q-admin-statistics" data-admin-section="statistics" aria-label="Statistik utama">
            <article class="q-admin-stat q-admin-stat-hero">
                <div class="q-admin-stat-heading"><h2>Entitas mitra terdaftar</h2><span class="q-admin-category">3 pilar sosial</span></div>
                <strong>{{ $organizationCount }}</strong>
                <p class="q-admin-stat-caption">Organisasi mitra</p>
                <p class="q-admin-stat-description">Lembaga kesejahteraan sosial, organisasi PSM, dan kelompok Karang Taruna yang tercatat pada sumber data aktif.</p>
                {!! $link('/admin/organizations/lks', 'Lihat seluruh mitra') !!}
                <dl class="q-admin-distribution">
                    @foreach ($pillarLabels as $pillar => $title)
                        <div><dt>{{ $title }}</dt><dd><a href="{{ $pillarUrls[$pillar] }}">{{ $pillarCounts[$pillar] }}<span class="sr-only"> organisasi {{ $title }}</span></a></dd></div>
                    @endforeach
                </dl>
            </article>
            <div class="q-admin-stat-matrix">
                @foreach ([
                    ['Anggota terdaftar', $memberCount, 'Pengurus, SDM dan anggota aktif dari seluruh mitra.', '/admin/organizations/lks', 'users', 'Lihat detail keanggotaan'],
                    ['Klien terdaftar', $clientCount, 'Klien terdaftar pada LKS di Kota Banjarbaru.', '/admin/occupancy?residence=', 'heart', 'Buka klien LKS'],
                    ['Laporan LKS', $reportCount, 'Laporan semester terkirim; setiap periode dihitung satu laporan.', $reportUrl, 'file', 'Arsip laporan LKS'],
                ] as [$title, $value, $note, $href, $symbol, $action])
                    <a class="q-admin-stat" href="{{ $href }}"><div class="q-admin-stat-heading"><h2>{{ $title }}</h2>{!! $icon($symbol) !!}</div><strong>{{ $value }}</strong><p>{{ $note }}</p><span>{{ $action }} {!! $arrow !!}</span></a>
                @endforeach
            </div>
        </section>

        <section class="q-admin-section" data-admin-section="work">
            <header class="q-admin-section-heading"><div><p class="q-admin-eyebrow">Pekerjaan Admin</p><h2>Perlu ditindaklanjuti</h2><p>Pendaftaran, kegiatan, dan surat balasan dari seluruh periode.</p></div><span @class(['q-admin-total', 'is-pending' => $pendingWork > 0])>{{ $pendingWork }} pekerjaan membutuhkan aksi</span></header>
            <div class="q-admin-queues">
                @foreach ($queueCards as [$category, $title, $count, $note, $href, $action])
                    <article @class(['q-admin-queue', 'is-pending' => $count > 0])>
                        <div class="q-admin-queue-heading"><p class="q-admin-eyebrow">{{ $category }}@if ($count > 0)<span class="q-admin-active-label">Aktif</span>@endif</p><h3>{{ $title }}</h3></div>
                        <strong class="q-admin-queue-count">{{ $count }}</strong>
                        <p>{{ $note }}</p>
                        <div class="q-admin-queue-actions">{!! $link($href, $action) !!}</div>
                    </article>
                @endforeach
            </div>
        </section>

        <section class="q-admin-section" data-admin-section="partners">
            <header class="q-admin-section-heading"><div><p class="q-admin-eyebrow">Tiga mitra strategis</p><h2>Ringkasan setiap mitra</h2><p>Keanggotaan dan klien menggunakan data terkini. Rekap laporan hanya berlaku untuk LKS pada periode terpilih.</p></div></header>
            <div class="q-admin-pillars">
                <article class="q-admin-pillar" data-pillar="lks">
                    <header><div><h3>LKS</h3><p>Lembaga, klien dan laporan layanan</p></div>{!! $icon('building') !!}</header>
                    {!! $facts([['Lembaga', $lksOrgCount, '/admin/organizations/lks'], ['Pengurus & SDM', $lksMemberCount, '/admin/organizations/lks'], ['Klien terdaftar', $clientCount, '/admin/occupancy?residence='], ['Masih tinggal di panti', $inhabitants, '/admin/occupancy?residence=living']]) !!}
                    <p class="q-admin-note">Penghuni memakai biodata tersimpan terbaru; terpisah dari penerima manfaat dalam laporan.</p>
                    <div class="q-admin-pillar-statuses">
                        @foreach ($reportStatusCounts as $value => $count)
                            <a href="{{ $reportUrl }}&status={{ $value }}">{!! $status($value) !!}<strong>{{ $count }}</strong></a>
                        @endforeach
                    </div>
                    {!! $link($reportUrl, 'Lihat laporan periode ini') !!}{!! $link('/admin/registrations/lks', 'Periksa pendaftaran LKS') !!}{!! $link('/admin/organizations/lks', 'Data SDM LKS') !!}
                </article>
                <article class="q-admin-pillar" data-pillar="psm">
                    <header><div><h3>PSM</h3><p>Anggota dan organisasi PSM</p></div>{!! $icon('heart') !!}</header>
                    {!! $facts([['Organisasi / wilayah', $psmOrgCount, '/admin/members/psm'], ['Anggota terdaftar', $psmMemberCount, '/admin/members/psm']]) !!}
                    <p class="q-admin-note">Data anggota dan akun yang ditambahkan Ketua PSM tercatat dalam daftar yang sama.</p>
                    {!! $link('/admin/members/psm', 'Lihat data anggota') !!}{!! $link('/admin/complaints/psm', 'Pengaduan Anggota PSM') !!}
                </article>
                <article class="q-admin-pillar" data-pillar="karang-taruna">
                    <header><div><h3>Karang Taruna</h3><p>Kelompok dan keanggotaan operasional</p></div>{!! $icon('users') !!}</header>
                    {!! $facts([['Kelompok', $ktOrgCount, '/admin/organizations/karang-taruna'], ['Pengurus', $ktOfficers, '/admin/organizations/karang-taruna'], ['Anggota berkegiatan', $ktParticipating, '/admin/organizations/karang-taruna'], ['Total anggota', $ktMemberCount, '/admin/organizations/karang-taruna']]) !!}
                    <p class="q-admin-note">Jumlah anggota operasional tidak mewakili seluruh warga Karang Taruna.</p>
                    {!! $link('/admin/organizations/karang-taruna', 'Lihat data kelompok') !!}{!! $link('/admin/organizations/karang-taruna', 'Data anggota') !!}{!! $link('/admin/registrations/karang-taruna', 'Pendaftaran kelompok') !!}{!! $link('/admin/incoming-letters/karang-taruna', 'Surat masuk kegiatan') !!}{!! $link('/admin/reply-letters/karang-taruna', 'Surat balasan dinas') !!}
                </article>
            </div>

            <section class="q-admin-lks-recap">
                <header class="q-admin-section-heading"><div><h3>Rekap laporan LKS</h3><p>{{ $period['label'] }} · {{ $reports->count() }} laporan semester yang telah dikirim.</p></div>{!! $link($reportUrl, 'Lihat detail laporan') !!}</header>
                @if ($reports->isEmpty())
                    <div class="q-admin-empty q-admin-recap-empty">{!! $icon('file') !!}<p>Belum ada laporan LKS yang dikirim pada periode ini.</p><span>Pilih periode lain atau periksa kembali setelah lembaga mengirim laporan.</span></div>
                @else
                    {!! $facts([['Laporan semester', $reports->count().' laporan'], ['Berkas terlampir', $totalFiles.' berkas'], ['Terkirim ke Admin', $reports->where('status', 'Terkirim')->count().' laporan']]) !!}
                    <div class="q-admin-report-summaries">
                        @foreach ($previews as $report)
                            <article>
                                <header><h4>{{ $organizationOf($report['organizationId'])['title'] }}</h4>{!! $status($report['status']) !!}</header>
                                {!! $facts([['Perihal', $excerpt($report['title'])], ['Lampiran', count($report['attachments']).' berkas terlampir']]) !!}
                                {!! $link('/admin/report/'.$report['id'], 'Lihat laporan') !!}
                            </article>
                        @endforeach
                    </div>
                @endif
                <details class="q-admin-missing">
                    <summary>{{ $unsubmitted->count() }} LKS belum memiliki pengajuan pada periode ini</summary>
                    <p>Daftar ini mencakup LKS yang tercatat sebelum akhir periode. Draf belum dihitung sebagai pengajuan.</p>
                    @if ($unsubmitted->isNotEmpty())
                        <ul>
                            @foreach ($unsubmitted->take(5) as $org)
                                <li><a href="/admin/organization/{{ $org['id'] }}">{{ $org['title'] }}</a><span>{{ $org['village'] }}, {{ $org['district'] }}</span></li>
                            @endforeach
                        </ul>
                    @else
                        <p class="q-admin-note">Semua LKS dalam cakupan periode sudah memiliki pengajuan.</p>
                    @endif
                    @if ($unsubmitted->count() > 5)
                        <p class="q-admin-note">Menampilkan 5 dari {{ $unsubmitted->count() }} LKS.</p>
                    @endif
                    {!! $link('/admin/organizations/lks', 'Lihat daftar LKS') !!}
                </details>
            </section>
        </section>

        <section class="q-admin-section" data-admin-section="activity">
            <header class="q-admin-section-heading"><div><p class="q-admin-eyebrow">Riwayat tercatat</p><h2>Aktivitas terbaru</h2><p>Pendaftaran mitra, laporan LKS, kegiatan dan surat balasan.</p></div></header>
            <div class="q-admin-activity-panel">
                @if ($activities->isEmpty())
                    <div class="q-admin-empty q-admin-activity-empty">{!! $icon('file') !!}<p>Belum ada riwayat pengajuan atau pemeriksaan dari sumber yang terhubung.</p></div>
                @else
                    <ol class="q-admin-activity-list">
                        @foreach ($activities->take(8) as $event)
                            <li>
                                <div>
                                    <div class="q-admin-activity-heading"><h3><a href="{{ $event['url'] }}">{{ $event['title'] }}</a></h3>{!! $status($event['status']) !!}</div>
                                    <p>{{ $pillarLabels[$event['pillar']] }} · {{ $event['organizationTitle'] }}</p>
                                    @if ($event['note'])
                                        <p class="q-admin-activity-note">{{ $excerpt($event['note']) }}</p>
                                    @endif
                                    <time datetime="{{ $event['at'] }}">{{ Format::date($event['at']) }}@if ($event['actor']) · {{ $event['actor'] }}@endif</time>
                                </div>
                                {!! $link($event['url'], 'Lihat detail') !!}
                            </li>
                        @endforeach
                    </ol>
                    @if ($activities->count() > 8)
                        <p class="q-admin-note">Menampilkan delapan aktivitas terbaru. Riwayat lengkap tersedia pada detail pengajuan.</p>
                    @endif
                @endif
            </div>
        </section>
        <footer class="q-admin-overview-footer">
            <p class="q-admin-storage-note">Data yang tersimpan di browser hanya tersedia pada browser dan alamat situs yang sama. Data server mengikuti sumber pada masing-masing fitur; arsip laporan PSM/Karang Taruna tidak dihitung sebagai pekerjaan laporan aktif.</p>
            <span>SiDaya · Kota Banjarbaru</span>
        </footer>
    </div>
@endsection
