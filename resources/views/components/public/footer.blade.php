<footer class="site-footer">
            <div class="container">
                <div class="footer-main">
                    <div class="footer-brand">
                        <a class="brand" href="/index.html">{{ $publicBrand }}</a>
                        <p>
                            {!! $landing->text('footer.utama.01', 'Informasi pemberdayaan sosial untuk masyarakat
Kota Banjarbaru.') !!}
                        </p>

                        <div class="social-icons" aria-label="Media sosial Dinas Sosial">
                            <a
                                href="https://www.instagram.com/dinsoskotabanjarbaru?exln=MXV6MWtiM2lhZ3FxeA%3D%3D"
                                target="_blank"
                                rel="noopener noreferrer"
                                aria-label="Instagram Dinas Sosial Kota Banjarbaru (tab baru)"
                            >
                                <svg aria-hidden="true" width="21" height="21" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                                    <rect x="3" y="3" width="18" height="18" rx="5" />
                                    <circle cx="12" cy="12" r="4" />
                                    <circle cx="17.5" cy="6.5" r=".7" fill="currentColor" stroke="none" />
                                </svg>
                            </a>
                        </div>
                    </div>

                    <div class="footer-column">
                        <h2>{{ $publicBrand }}</h2>
                        <a href="/index.html#profil">Kenali {{ $publicBrand }}</a>
                        <a href="/pilar-sosial.html">Pilar sosial</a>
                        <a href="/index.html#layanan">Hubungi Dinsos</a>
                        <a href="/index.html#galeri">Galeri kegiatan</a>
                    </div>
                    <div class="footer-column">
                        <h2>{!! $landing->text('footer.utama.02', 'Pilar Sosial') !!}</h2>
                        <a href="/pilar/lks.html">LKS</a>
                        <a href="/pilar/lks.html#pendaftaran">Panduan pendaftaran</a>
                        <a href="/pilar-sosial.html">Semua pilar sosial</a>
                    </div>
                    <div class="footer-column">
                        <h2>{!! $landing->text('footer.utama.03', 'Tautan') !!}</h2>
                        <a href="https://dinsos.banjarbarukota.go.id/pelayanan-dinas-sosial/">Layanan Dinsos</a>
                        <a href="https://dinsos.banjarbarukota.go.id/jam-pelayanan/">Jam pelayanan</a>
                        <a href="https://banjarbarukota.go.id/perangkat-daerah">Pemkot Banjarbaru</a>
                        <a href="/kontak.html">Halaman kontak</a>
                    </div>
                </div>

                <div class="footer-bottom">
                    <p>© {{ $publicBrand }} · Dinas Sosial Kota Banjarbaru</p>
                    <div>
                        <button type="button" data-content-title="Kebijakan Privasi" data-content-message="Kebijakan privasi {{ $publicBrand }} belum tersedia. Peta pada halaman publik {{ $publicBrand }} dimuat dari Google Maps. Kebijakan layanan pihak tersebut berlaku saat Anda menggunakannya.">
                            Kebijakan Privasi
                        </button>
                        <button type="button" data-content-title="Ketentuan Penggunaan" data-content-message="Ketentuan penggunaan {{ $publicBrand }} belum tersedia. Informasi beranda membantu Anda mengenali layanan; persyaratan dan jadwal terbaru dapat ditanyakan langsung kepada Dinas Sosial Kota Banjarbaru.">
                            Ketentuan Penggunaan
                        </button>
                    </div>
                </div>
            </div>
        </footer>
