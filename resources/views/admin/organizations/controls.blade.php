<form class="admin-partner-controls" aria-label="Pencarian dan filter mitra" method="get" data-filter-form>
    <label class="admin-partner-search"><x-dashboard.icon name="search" /><span class="sr-only">{{ $searchLabel }}</span><input type="search" name="q" value="{{ $filters['q'] }}" placeholder="{{ $placeholder }}" @if ($filters['q']) data-autofocus @endif></label>
    <div class="admin-partner-filters">
        <label class="admin-partner-filter"><span>Kecamatan</span>
            <select name="district">
                <option value="">Semua kecamatan</option>
                @foreach ($districts as $district)
                    <option @selected($filters['district'] === $district)>{{ $district }}</option>
                @endforeach
            </select>
        </label>
        <label class="admin-partner-filter"><span>Kelurahan</span>
            <select name="village">
                <option value="">Semua kelurahan</option>
                @foreach ($villages as $village)
                    <option @selected($filters['village'] === $village)>{{ $village }}</option>
                @endforeach
            </select>
        </label>
        <button type="button" class="admin-partner-button" data-href="{{ request()->url() }}">Hapus filter</button>
    </div>
</form>
