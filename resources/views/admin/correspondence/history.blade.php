<section class="admin-correspondence-card">
    <h2>Riwayat</h2>
    @if (count($history))
        <ol class="admin-correspondence-history">
            @foreach ($history as $entry)
                <li><strong>{{ $entry['status'] }}</strong><time datetime="{{ $entry['at'] }}">{{ \App\Support\Format::dateTime($entry['at']) }}</time><span>{{ $entry['actor'] ?: 'Belum dicatat' }}</span><p>{{ $entry['note'] }}</p></li>
            @endforeach
        </ol>
    @else
        <p>Riwayat belum tersedia.</p>
    @endif
</section>
