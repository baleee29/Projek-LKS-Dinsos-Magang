{{-- memberTable() dari shared/workspace-profile.js. --}}
@php
    use App\Support\Format;
@endphp
@if ($members->isEmpty())
    <x-dashboard.empty-state title="Belum ada anggota" description="Tambahkan pengurus atau anggota organisasi Anda." />
@else
    <div class="table-scroll" tabindex="0" role="region" aria-label="Daftar anggota">
        <table class="reports-table q-table q-table-numbered">
            <thead><tr><th scope="col" class="q-col-number">No.</th><th scope="col" class="q-col-person">Nama</th><th scope="col">Peran / kategori</th><th scope="col">Bergabung</th><th scope="col">Keterangan</th><th scope="col">Detail</th></tr></thead>
            <tbody>
                @foreach ($members as $member)
                    <tr>
                        <td class="q-col-number" data-label="No.">{{ $loop->iteration }}</td>
                        <td class="q-col-person" data-label="Nama"><div class="q-person-cell"><span class="q-initials" aria-hidden="true">{{ Format::initials($member['title']) }}</span><div><strong>{{ $member['title'] }}</strong>@if ($member['isChair'])<span class="cell-meta">Penanggung Jawab</span>@endif</div></div></td>
                        <td data-label="Peran / kategori">{{ $member['position'] ?: $member['category'] }}<span class="cell-meta">{{ $member['category'] }}</span></td>
                        <td data-label="Bergabung">{{ Format::date($member['joined']) }}</td>
                        <td data-label="Keterangan">{{ $member['notes'] ?: '—' }}</td>
                        <td data-label="Detail"><a class="row-action" href="/admin/member/{{ $member['id'] }}" aria-label="Lihat {{ $member['title'] }}"><x-dashboard.icon name="arrow" /></a></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif
