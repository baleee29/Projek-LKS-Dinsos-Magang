{{-- Pratinjau ?demo_state=error, memakai teks dan markup error dari shared/workspace.js sumber. --}}
<div class="section-heading">
    <div>
        <p class="eyebrow">{{ $role === 'admin' ? 'Dinas Sosial Kota Banjarbaru' : $identity['organization'] }}</p>
        <h1 id="view-title" tabindex="-1">Sumber data belum dapat dibaca</h1>
        <p class="view-description">Data belum dapat dimuat dari server. Coba muat ulang halaman.</p>
    </div>
    <div class="q-actions">
        <a class="text-button" href="{{ request()->url() }}">Coba kembali</a>
    </div>
</div>
<p class="notice" role="alert">Data belum dapat dimuat/disimpan. Muat ulang atau masuk kembali.</p>
