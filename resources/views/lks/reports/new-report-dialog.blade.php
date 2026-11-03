{{-- Dialog pilih periode, dari shared/workspace-reports.js. --}}
<x-dashboard.dialog id="new-report-dialog" title="Buat Laporan Semester">
    <p class="muted">Pilih periode untuk {{ $organization['title'] }}. Satu laporan tersedia untuk setiap semester.</p>
    <div class="psm-form-grid">
        <label class="filter-field">Tahun
            <select id="new-report-year">
                @foreach ($years as $year)
                    <option value="{{ $year }}">{{ $year }}</option>
                @endforeach
            </select>
        </label>
        <label class="filter-field">Semester
            <select id="new-report-quarter">
                <option value="1">Semester 1 (Jan – Jun)</option>
                <option value="2" selected>Semester 2 (Jul – Des)</option>
            </select>
        </label>
    </div>
    <x-slot:actions>
        <button class="text-button" type="button" data-close-dialog>Batal</button><button class="export-button" type="button" id="create-quarterly" data-action-url="/lks/reports" data-action-fields="new-report-year:year,new-report-quarter:semester">Buat / buka laporan</button>
    </x-slot:actions>
</x-dashboard.dialog>
