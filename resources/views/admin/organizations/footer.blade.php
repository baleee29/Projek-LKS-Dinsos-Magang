@if ($paginator->total())
    <div class="admin-partner-footer">
        <p>Menampilkan {{ $paginator->firstItem() }}–{{ $paginator->lastItem() }} dari {{ $paginator->total() }} {{ $noun }}</p>
        <nav aria-label="Halaman daftar mitra">
            <button type="button" class="admin-partner-page" @if ($paginator->currentPage() === 1) disabled @else data-href="{{ $paginator->previousPageUrl() }}" @endif>Sebelumnya</button>
            <span>Halaman {{ $paginator->currentPage() }} dari {{ $paginator->lastPage() }}</span>
            <button type="button" class="admin-partner-page" @if ($paginator->currentPage() === $paginator->lastPage()) disabled @else data-href="{{ $paginator->nextPageUrl() }}" @endif>Berikutnya</button>
        </nav>
    </div>
@endif
