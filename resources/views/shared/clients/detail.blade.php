@extends('layouts.dashboard')

@php
    use App\Support\Demo\DemoClientSchema;
    use App\Support\Format;
    $canEdit = ! $admin;
    $data = $item;
    $filled = fn ($value) => $value !== '' && $value !== null;
@endphp

@section('title', $data['name'])

@section('content')
    <div class="lks-workspace lks-client-detail-view">
        <header class="lks-page-heading lcd-page-heading">
            <div>
                <nav class="lks-breadcrumb" aria-label="Navigasi halaman"><a href="{{ $admin ? '/admin/occupancy' : '/lks/overview' }}">{{ $admin ? 'Rekap Penghuni' : 'LKS SiDaya' }}</a><span aria-hidden="true">/</span><span>{{ $organization['title'] }}</span><span aria-hidden="true">/</span><span aria-current="page">{{ $data['code'] ?: 'Biodata klien' }}</span></nav>
                <div class="lcd-title-row">
                    <span class="lcd-title-avatar lcd-title-initials" aria-hidden="true">{{ Format::initials($data['name']) }}</span>
                    <div><h1 id="view-title" tabindex="-1">{{ $data['name'] ?: 'Biodata klien' }}</h1><span class="lcd-identity-tag">{{ $data['code'] ?: 'Kode belum dicatat' }} · {{ $organization['title'] }}</span></div>
                </div>
            </div>
            <div class="lks-actions">
                <a class="lks-button" href="{{ $backUrl }}"><x-dashboard.lks-icon name="arrow-left" />Kembali ke daftar</a>
                @if ($canEdit)
                    <a class="lks-button lks-button-primary" href="/lks/edit-resident/{{ $data['id'] }}"><x-dashboard.lks-icon name="edit" />Edit Data</a>
                @endif
            </div>
        </header>
        <section class="lks-card lcd-summary" aria-label="Status biodata klien">
            <div class="lcd-summary-top">
                <div class="lcd-badges"><span class="lcd-badge"><x-dashboard.lks-icon name="home" />{{ $data['serviceType'] ?: 'Bentuk layanan belum dicatat' }}</span><span class="lcd-badge"><x-dashboard.lks-icon name="file" />{{ $data['residenceStatus'] ?: 'Status layanan belum dicatat' }}</span></div>
                <span class="lcd-demo">Data tersimpan di sistem</span>
            </div>
            <div class="lcd-summary-bottom"><p>Biodata tersimpan dan langsung tercatat dalam daftar klien serta rekap layanan.</p><span>Terakhir diperbarui: {{ Format::date($data['updated']) }}</span></div>
        </section>
        <div class="lcd-layout">
            <div class="lcd-sections">
                @foreach ($sections as $index => $section)
                    <section class="lks-card lcd-section" aria-labelledby="lcd-current-{{ $section['key'] }}">
                        <header class="lcd-section-heading"><x-dashboard.lks-icon :name="$section['icon']" /><h2 id="lcd-current-{{ $section['key'] }}">Bagian {{ chr(65 + $index) }}. {{ $section['title'] }}</h2><span class="lcd-section-tag">{{ $section['tag'] }}</span></header>
                        <dl class="lcd-fields">
                            @if ($section['key'] === 'identity')
                                <div class="lcd-field lcd-field-wide" data-client-field="photo"><dt>Foto profil</dt><dd><span class="lcd-value-empty">Belum ada foto profil</span></dd></div>
                            @endif
                            @foreach ($section['fields'] as $key)
                                @continue(! DemoClientSchema::applicable($key, $data))
                                @php
                                    $field = $fields[$key];
                                    $type = $field['type'] ?? 'text';
                                    $value = $data[$key];
                                    $label = $field['label'];
                                    $display = $filled($value) ? $value : 'Belum dicatat';
                                    if ($type === 'checkbox') {
                                        $display = $value ? 'Ya' : 'Tidak';
                                    }
                                    if ($type === 'date') {
                                        $display = Format::date($value);
                                    }
                                    if ($key === 'birthDate') {
                                        $label = 'Tanggal lahir / usia';
                                        $display = $data['birthDateUnknown']
                                            ? 'Tanggal lahir belum diketahui'
                                            : Format::date($value).($filled($value) ? ' ('.Format::age($data, $today).')' : '');
                                    }
                                    $empty = ! $filled($value) && $type !== 'checkbox' && ! ($key === 'birthDate' && $data['birthDateUnknown']);
                                @endphp
                                <div @class(['lcd-field', 'lcd-field-wide' => $type === 'textarea', 'lcd-field-tinted' => in_array($key, ['careNeeds', 'carePlan', 'notes'], true)]) data-client-field="{{ $key }}"><dt>{{ $label }}</dt><dd @class(['lcd-value-empty' => $empty])>{{ $display }}</dd></div>
                            @endforeach
                        </dl>
                    </section>
                @endforeach
            </div>
            <aside class="lks-card lcd-history" aria-labelledby="lcd-history-title">
                <header class="lcd-history-header"><x-dashboard.lks-icon name="history" /><div><h2 id="lcd-history-title">Riwayat biodata</h2><p>Pencatatan dan perubahan biodata</p></div></header>
                <ol class="lcd-timeline">
                    @foreach ($data['history'] as $entry)
                        <li @class(['lcd-timeline-item', 'is-recorded' => $entry['status'] === 'Terdaftar'])>
                            <span class="lcd-timeline-marker"><x-dashboard.lks-icon :name="$entry['status'] === 'Terdaftar' ? 'check' : 'history'" /></span>
                            <div class="lcd-timeline-entry">
                                <div class="lcd-timeline-heading"><h3>{{ $entry['status'] }}</h3><time datetime="{{ $entry['at'] }}">{{ Format::dateTime($entry['at']) }}</time></div>
                                <p class="lcd-timeline-actor">{{ $entry['actor'] }}</p>
                                @if ($entry['note'])
                                    <p class="lcd-timeline-note">{{ $entry['note'] }}</p>
                                @endif
                            </div>
                        </li>
                    @endforeach
                </ol>
                <p class="lcd-history-footnote"><x-dashboard.lks-icon name="shield" /><span>Riwayat untuk klien ini tersimpan di sistem.</span></p>
            </aside>
        </div>
    </div>
@endsection
