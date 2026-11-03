{{-- reportTable() dari shared/workspace-reports.js. $reports: koleksi atau paginator; $offset: nomor awal. --}}
@php
    use App\Support\Data\DataLaporan;
    use App\Support\Format;
@endphp
@if (count($reports))
    <div class="table-scroll" tabindex="0" role="region" aria-label="Daftar laporan">
        <table class="reports-table q-table q-table-numbered">
            <caption class="sr-only">Laporan</caption>
            <thead><tr><th scope="col" class="q-col-number">No.</th><th scope="col">Lembaga</th><th scope="col">Wilayah</th><th scope="col">Periode &amp; Judul</th><th scope="col">Berkas Terlampir</th><th scope="col">Pengajuan</th><th scope="col">Status</th><th scope="col" class="q-col-action">Tindakan</th></tr></thead>
            <tbody>
                @foreach ($reports as $index => $item)
                    @php
                        $org = \App\Support\Data\DataLembaga::find($item['organizationId']);
                        $filesCount = count($item['attachments']);
                    @endphp
                    <tr>
                        <td class="q-col-number" data-label="No.">{{ ($offset ?? 0) + $index + 1 }}</td>
                        <td data-label="Lembaga"><a class="record-link" href="/admin/report/{{ $item['id'] }}">{{ $org['title'] }}</a><span class="cell-meta">{{ $item['id'] }}</span></td>
                        <td data-label="Wilayah">{{ $org['village'] }}<span class="cell-meta">{{ $org['district'] }}</span></td>
                        <td data-label="Periode &amp; Judul"><strong>{{ DataLaporan::periodLabel($item) }}</strong>@if ($item['title'])<span class="cell-meta" style="color:var(--text-color, #1e293b);font-weight:500;">{{ $item['title'] }}</span>@endif</td>
                        <td data-label="Berkas Terlampir">@if ($filesCount)<span class="cell-badge">{{ $filesCount }} berkas</span>@else<span class="cell-meta">Belum ada berkas</span>@endif</td>
                        <td data-label="Pengajuan">{{ Format::date($item['submittedAt'], 'Belum diajukan') }}</td>
                        <td data-label="Status"><x-dashboard.status-badge :status="$item['status']" /></td>
                        <td class="q-col-action" data-label="Tindakan">
                            <div class="q-actions q-table-actions">
                                <a class="text-button" href="/admin/report/{{ $item['id'] }}">Lihat laporan</a>
                                <button class="text-button" type="button" data-export-url="/ekspor/laporan/{{ $item['id'] }}" data-export-format="pdf" aria-label="Unduh PDF {{ $item['title'] }}">Unduh PDF</button>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@else
    <x-dashboard.empty-state title="Belum ada laporan pada pilihan ini" description="Pilih periode lain atau ubah filter." />
@endif
