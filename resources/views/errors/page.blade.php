{{-- Halaman error: memakai kerangka dashboard bila sudah masuk, dan kartu login bila belum. --}}
@include(session('demo_role') ? 'errors.page-dashboard' : 'errors.page-guest')
