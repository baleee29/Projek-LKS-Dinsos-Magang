{{-- Halaman error: memakai kerangka dashboard bila sudah masuk, dan kartu login bila belum. --}}
@include(auth()->user()?->peran ? 'errors.page-dashboard' : 'errors.page-guest')
