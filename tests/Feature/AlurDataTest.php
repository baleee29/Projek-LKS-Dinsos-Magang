<?php

namespace Tests\Feature;

use App\Http\Controllers\Aksi\PendaftaranController;
use App\Models\BerkasTersimpan;
use App\Models\IsiLanding;
use App\Models\KlienLks;
use App\Models\LaporanSemester;
use App\Models\Lembaga;
use App\Models\PendaftaranLks;
use App\Models\Pengguna;
use App\Models\PengurusLks;
use App\Models\SuratLks;
use Database\Seeders\DataContohSeeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

/** Alur tulis di docs/rancangan-database.md, diuji sampai ke database. */
class AlurDataTest extends TestCase
{
    private function pdf(string $nama = 'berkas.pdf', int $kb = 120): UploadedFile
    {
        return UploadedFile::fake()->createWithContent($nama, '%PDF-1.4'."
".str_repeat('isi berkas uji ', $kb * 70));
    }

    /** @return array<string, mixed> */
    private function isianPendaftaran(): array
    {
        $isian = ['lks-nama' => 'LKS Uji Coba', 'lks-ketua' => 'Pengisi Uji', 'lks-whatsapp' => '081234567890', 'lks-izin' => 'IZIN-1', 'lks-rekening' => 'BNI 123', 'lks-fokus' => 'Pengasuhan anak', 'request_id' => (string) Str::uuid()];
        foreach (PendaftaranController::CHOICES as $grup => $pilihan) {
            $isian[$grup] = [$pilihan[0]];
        }
        foreach (array_keys(PendaftaranController::FILES) as $kunci) {
            $isian['files'][$kunci] = $this->pdf($kunci.'.pdf');
        }

        return $isian;
    }

    public function test_registration_flow_from_public_form_to_login(): void
    {
        // Pengunjung mengirim formulir tanpa akun.
        $isian = $this->isianPendaftaran();
        $this->post('/pendaftaran/mitra', $isian, ['Accept' => 'application/json'])->assertCreated();
        $pendaftaran = PendaftaranLks::where('nama_lembaga', 'LKS Uji Coba')->firstOrFail();
        $this->assertSame(PendaftaranLks::MENUNGGU, $pendaftaran->status);
        $this->assertCount(16, $pendaftaran->berkas);
        $this->assertGreaterThan(0, DB::table('potongan_berkas')->where('berkas_id', $pendaftaran->berkas->first()->berkas_id)->count());
        $this->assertNotNull($this->admin()->notifications()->where('data', 'like', '%LKS Uji Coba%')->first());

        // Kiriman ulang dengan request_id yang sama tidak membuat baris baru.
        $this->post('/pendaftaran/mitra', $isian, ['Accept' => 'application/json'])->assertOk()->assertJson(['duplicate' => true]);
        $this->assertSame(1, PendaftaranLks::where('nama_lembaga', 'LKS Uji Coba')->count());

        // Penolakan wajib beralasan; penerimaan membuat lembaga dan bersifat final.
        $this->actingAs($this->admin());
        $this->postJson("/admin/registration/{$pendaftaran->id}/review", ['status' => 'Ditolak', 'notes' => 'pendek'])->assertUnprocessable();
        $this->postJson("/admin/registration/{$pendaftaran->id}/review", ['status' => 'Disetujui'])->assertOk();
        $pendaftaran->refresh();
        $this->assertSame('LKS Uji Coba', Lembaga::findOrFail($pendaftaran->lembaga_id)->nama);
        $this->postJson("/admin/registration/{$pendaftaran->id}/review", ['status' => 'Ditolak', 'notes' => 'alasan yang cukup panjang'])->assertConflict();

        // Satu pendaftaran hanya untuk satu akun.
        $akun = ['name' => 'LKS Uji Coba', 'whatsapp' => '081234567890', 'username' => 'lks_uji', 'password' => 'LKS#12345678', 'registration_id' => $pendaftaran->id];
        $this->postJson('/admin/partner-accounts', $akun)->assertOk()->assertJsonPath('message', 'Akun LKS tersimpan.');
        $this->postJson('/admin/partner-accounts', ['username' => 'lks_uji2'] + $akun)->assertStatus(422);
        $baru = Pengguna::where('nama_pengguna', 'lks_uji')->firstOrFail();
        $this->assertSame($pendaftaran->lembaga_id, $baru->lembaga_id);

        // Akun baru bisa masuk dan melihat lembaganya.
        $this->post('/logout');
        $this->post('/login', ['email' => 'lks_uji', 'password' => 'LKS#12345678'])->assertRedirect('/lks/overview');
        $this->get('/lks/profile')->assertOk()->assertSee('LKS Uji Coba');
    }

    public function test_public_registration_rejects_bots_incomplete_files_and_scripts(): void
    {
        $isian = $this->isianPendaftaran();
        $this->post('/pendaftaran/mitra', ['website' => 'spam'] + $isian, ['Accept' => 'application/json'])->assertUnprocessable();

        $kurang = $this->isianPendaftaran();
        unset($kurang['files']['ad_art']);
        $this->post('/pendaftaran/mitra', $kurang, ['Accept' => 'application/json'])->assertUnprocessable();

        $skrip = $this->isianPendaftaran();
        $skrip['files']['ad_art'] = UploadedFile::fake()->create('kode.php', 5, 'application/x-php');
        $this->post('/pendaftaran/mitra', $skrip, ['Accept' => 'application/json'])->assertUnprocessable();
        $this->assertSame(0, PendaftaranLks::where('nama_lembaga', 'LKS Uji Coba')->count());
    }

    public function test_reset_and_revoke_end_sessions_and_change_access(): void
    {
        $akun = $this->lks();
        DB::table('sesi')->insert(['id' => 'sesi-uji', 'user_id' => $akun->id, 'payload' => '', 'last_activity' => time()]);
        $this->actingAs($this->admin());

        $this->postJson("/admin/partner-accounts/{$akun->id}/reset-password", ['password' => 'pendek'])->assertUnprocessable();
        $this->postJson("/admin/partner-accounts/{$akun->id}/reset-password", ['password' => 'LKS#87654321'])->assertOk();
        $this->assertSame(0, DB::table('sesi')->where('id', 'sesi-uji')->count());
        $this->assertNotNull($akun->notifications()->where('data', 'like', '%direset%')->first());

        $this->deleteJson("/admin/partner-accounts/{$akun->id}")->assertOk();
        $this->assertFalse($akun->fresh()->aktif);
        $this->assertNotNull(Lembaga::find($akun->lembaga_id));
        $this->post('/logout');
        $this->post('/login', ['email' => $akun->email, 'password' => 'LKS#87654321'])->assertSessionHasErrors('email');
    }

    public function test_only_admin_reaches_account_endpoints(): void
    {
        $this->actingAs($this->lks());
        $this->postJson('/admin/partner-accounts', [])->assertForbidden();
        $this->postJson('/admin/landing-content/home.hero.01', ['value' => 'x'])->assertForbidden();
    }

    public function test_profile_update_uses_session_organization_and_checks_version(): void
    {
        $akun = $this->lks();
        $lembaga = $akun->lembaga;
        $lain = $this->lembagaLain();
        $this->actingAs($akun);

        $isian = ['title' => 'Nama Baru', 'chair' => 'Ketua Baru', 'version' => $lembaga->versi, 'id' => $lain->id, 'lembaga_id' => $lain->id, 'logo' => UploadedFile::fake()->image('logo.png', 100, 100)];
        $this->post('/lks/profile', $isian, ['Accept' => 'application/json'])->assertOk();
        $this->assertSame('Nama Baru', $lembaga->fresh()->nama);
        $this->assertNotSame('Nama Baru', $lain->fresh()->nama);
        $this->assertNotNull($lembaga->fresh()->logo_berkas_id);

        // Versi lama ditolak sebagai konflik.
        $this->post('/lks/profile', ['title' => 'Bentrok', 'chair' => 'X', 'version' => $lembaga->versi], ['Accept' => 'application/json'])->assertConflict();
        $this->assertSame('Nama Baru', $lembaga->fresh()->nama);
    }

    /** @return array<string, mixed> */
    private function isianPengurus(): array
    {
        return ['title' => 'Personel Uji', 'nik' => '0000000012345678', 'birthPlace' => 'Banjarbaru', 'birthDate' => '1990-01-01', 'gender' => 'Perempuan', 'contact' => '0812', 'ktpAddress' => 'Jl. Uji', 'category' => 'Staf'];
    }

    public function test_member_create_update_delete_and_attachment_scope(): void
    {
        $akun = $this->lks();
        $this->actingAs($akun);
        $this->post('/lks/members', $this->isianPengurus() + ['attachments' => [$this->pdf('sk.pdf')]], ['Accept' => 'application/json'])->assertOk();
        $pengurus = PengurusLks::where('nama', 'Personel Uji')->firstOrFail();
        $this->assertSame($akun->lembaga_id, $pengurus->lembaga_id);
        $berkas = BerkasTersimpan::where('jenis_sumber', 'pengurus')->where('id_sumber', $pengurus->id)->firstOrFail();
        $unduh = $this->get('/berkas/'.$berkas->id)->assertOk()->assertHeader('X-Content-Type-Options', 'nosniff');
        $this->assertSame($berkas->sha256, hash('sha256', $unduh->streamedContent()));
        $this->assertGreaterThan(1, DB::table('potongan_berkas')->where('berkas_id', $berkas->id)->count());

        $this->post("/lks/members/{$pengurus->id}", ['title' => 'Personel Ubah', 'version' => $pengurus->versi] + $this->isianPengurus(), ['Accept' => 'application/json'])->assertOk();
        $this->assertSame('Personel Ubah', $pengurus->fresh()->nama);
        $this->post("/lks/members/{$pengurus->id}", ['version' => $pengurus->versi] + $this->isianPengurus(), ['Accept' => 'application/json'])->assertConflict();

        // Lembaga lain tidak dapat mengubah, menghapus, atau mengunduh berkasnya.
        $lain = Pengguna::where('peran', 'lks')->where('lembaga_id', '!=', $akun->lembaga_id)->firstOrFail();
        $this->actingAs($lain);
        $this->deleteJson('/lks/members/'.$pengurus->id)->assertNotFound();
        $this->get('/berkas/'.$berkas->id)->assertForbidden();

        $this->actingAs($this->admin())->get('/berkas/'.$berkas->id)->assertOk();
        $this->actingAs($akun)->deleteJson('/lks/members/'.$pengurus->id)->assertOk();
        $this->assertNull(BerkasTersimpan::find($berkas->id));
    }

    /** @return array<string, mixed> */
    private function isianKlien(array $tambahan = []): array
    {
        return $tambahan + [
            'code' => 'UJI-1', 'name' => 'Klien Uji', 'category' => 'Anak', 'gender' => 'Perempuan', 'birthDate' => '2015-01-01', 'educationStatus' => 'Belum sekolah', 'admissionDate' => '2026-01-05', 'serviceType' => 'Tinggal di panti',
            'residenceStatus' => 'Masih menerima layanan', 'referralSource' => 'Keluarga', 'intakeReason' => 'Butuh pengasuhan', 'guardianStatus' => 'Belum diketahui',
            'careNeeds' => 'Pendampingan', 'carePlan' => 'Evaluasi', 'caseWorker' => 'Pendamping', 'identityDocumentStatus' => 'Tersedia', 'placementDocumentStatus' => 'Tersedia',
        ];
    }

    public function test_client_is_saved_directly_with_unique_code_and_clears_stale_fields(): void
    {
        $akun = $this->lks();
        $this->actingAs($akun);
        $this->postJson('/lks/clients', $this->isianKlien(['nik' => '0000000099990001', 'exitDate' => '2026-02-01', 'guardianName' => 'Tidak Berlaku']))->assertOk();
        $klien = KlienLks::where('kode', 'UJI-1')->firstOrFail();
        $this->assertSame('Terdaftar', $klien->status);
        $this->assertNull($klien->tanggal_keluar);
        $this->assertNull($klien->nama_wali);
        $this->assertCount(1, $klien->riwayat);
        $this->assertNotNull($this->admin()->notifications()->where('data', 'like', '%Klien baru tercatat%')->where('data', 'like', '%'.$klien->id.'%')->first());

        $this->postJson('/lks/clients', $this->isianKlien())->assertUnprocessable()->assertJsonValidationErrors('code');
        $this->postJson('/lks/clients', $this->isianKlien(['code' => 'UJI-2', 'nik' => '0000000099990001']))->assertUnprocessable()->assertJsonValidationErrors('nik');
        $this->postJson('/lks/clients', $this->isianKlien(['code' => 'UJI-3', 'nik' => '123']))->assertJsonValidationErrors('nik');

        $this->postJson('/lks/clients/'.$klien->id, $this->isianKlien(['name' => 'Klien Ubah', 'version' => $klien->versi]))->assertOk();
        $this->assertSame('Klien Ubah', $klien->fresh()->nama);
        $this->assertCount(2, $klien->fresh()->riwayat);
        $this->postJson('/lks/clients/'.$klien->id, $this->isianKlien(['version' => $klien->versi]))->assertConflict();

        // Admin hanya membaca.
        $this->actingAs($this->admin())->postJson('/lks/clients/'.$klien->id, $this->isianKlien(['version' => 2]))->assertForbidden();
        $this->actingAs($akun)->deleteJson('/lks/clients/'.$klien->id)->assertOk();
        $this->assertNull(KlienLks::find($klien->id));
    }

    public function test_client_conditional_rules_are_enforced_on_the_server(): void
    {
        $this->actingAs($this->lks());
        $this->postJson('/lks/clients', $this->isianKlien(['educationStatus' => '']))->assertJsonValidationErrors('educationStatus');
        $this->postJson('/lks/clients', $this->isianKlien(['birthDate' => '']))->assertJsonValidationErrors('birthDate');
        $this->postJson('/lks/clients', $this->isianKlien(['birthDate' => '', 'birthDateUnknown' => '1']))->assertJsonValidationErrors('approximateAge');
        $this->postJson('/lks/clients', $this->isianKlien(['residenceStatus' => 'Sudah keluar']))->assertJsonValidationErrors(['exitDate', 'exitReason']);
        $this->postJson('/lks/clients', $this->isianKlien(['residenceStatus' => 'Sudah keluar', 'exitDate' => '2025-01-01', 'exitReason' => 'Selesai']))->assertJsonValidationErrors('exitDate');
        $this->postJson('/lks/clients', $this->isianKlien(['guardianStatus' => 'Keluarga/wali diketahui']))->assertJsonValidationErrors(['guardianName', 'guardianRelationship']);
        $this->postJson('/lks/clients', $this->isianKlien(['educationStatus' => 'Sedang sekolah']))->assertJsonValidationErrors(['schoolName', 'educationLevel']);
    }

    public function test_report_draft_send_and_private_changes(): void
    {
        $akun = $this->lks();
        $this->actingAs($akun);
        LaporanSemester::where('lembaga_id', $akun->lembaga_id)->where('tahun', 2024)->delete();
        $id = $this->postJson('/lks/reports', ['year' => 2024, 'semester' => 1])->assertOk()->json('redirect');
        $laporan = LaporanSemester::where('lembaga_id', $akun->lembaga_id)->where('tahun', 2024)->where('semester', 1)->firstOrFail();
        $this->assertSame('/lks/edit/'.$laporan->id, $id);

        // Satu laporan per periode.
        $this->postJson('/lks/reports', ['year' => 2024, 'semester' => 1])->assertOk();
        $this->assertSame(1, LaporanSemester::where('lembaga_id', $akun->lembaga_id)->where('tahun', 2024)->where('semester', 1)->count());

        // Draf tidak memberi tahu Admin dan tidak terbaca Admin.
        $sebelum = $this->admin()->notifications()->count();
        $this->post("/lks/reports/{$laporan->id}", ['action' => 'draft', 'version' => 1, 'title' => 'Judul Draf', 'files' => [$this->pdf('a.pdf')]], ['Accept' => 'application/json'])->assertOk();
        $laporan->refresh();
        $this->assertSame('Judul Draf', $laporan->judul);
        $this->assertSame($sebelum, $this->admin()->notifications()->count());
        $this->actingAs($this->admin())->get('/admin/report/'.$laporan->id)->assertNotFound();

        // Kirim butuh judul dan berkas.
        $this->actingAs($akun)->post("/lks/reports/{$laporan->id}", ['action' => 'send', 'version' => $laporan->versi, 'title' => ''], ['Accept' => 'application/json'])->assertUnprocessable();
        $this->post("/lks/reports/{$laporan->id}", ['action' => 'send', 'version' => $laporan->versi, 'title' => 'Judul Kirim', 'kept' => $laporan->lampiran], ['Accept' => 'application/json'])->assertOk();
        $laporan->refresh();
        $this->assertSame('Terkirim', $laporan->status);
        $this->assertSame($sebelum + 1, $this->admin()->notifications()->count());

        // Perubahan setelah terkirim tetap privat sampai dikirim ulang.
        $this->post("/lks/reports/{$laporan->id}", ['action' => 'draft', 'version' => $laporan->versi, 'title' => 'Judul Privat', 'kept' => $laporan->lampiran], ['Accept' => 'application/json'])->assertOk();
        $this->get('/lks/report/'.$laporan->id)->assertSee('Judul Privat');
        $this->actingAs($this->admin())->get('/admin/report/'.$laporan->id)->assertSee('Judul Kirim')->assertDontSee('Judul Privat');
        $this->actingAs($akun)->deleteJson('/lks/reports/'.$laporan->id)->assertConflict();
    }

    public function test_report_for_a_running_period_cannot_be_sent(): void
    {
        $akun = $this->lks();
        $this->actingAs($akun);
        $tahun = now('Asia/Makassar')->year + 1;
        $this->postJson('/lks/reports', ['year' => $tahun, 'semester' => 2])->assertOk();
        $laporan = LaporanSemester::where('lembaga_id', $akun->lembaga_id)->where('tahun', $tahun)->firstOrFail();
        $this->post("/lks/reports/{$laporan->id}", ['action' => 'send', 'version' => 1, 'title' => 'Judul', 'files' => [$this->pdf()]], ['Accept' => 'application/json'])->assertUnprocessable();
        $this->assertSame('Draf', $laporan->fresh()->status);
    }

    public function test_letters_flow_both_ways_with_read_state_and_scope(): void
    {
        $akun = $this->lks();
        $this->actingAs($akun);
        $this->post('/lks/letters', ['letter_date' => '2026-10-01', 'subject' => 'Surat Dari LKS', 'organization_id' => $this->lembagaLain()->id, 'file' => $this->pdf('s.pdf')], ['Accept' => 'application/json'])->assertOk();
        $surat = SuratLks::where('perihal', 'Surat Dari LKS')->firstOrFail();
        $this->assertSame($akun->lembaga_id, $surat->lembaga_id);
        $this->assertSame(SuratLks::LKS_KE_DINAS, $surat->arah);
        $this->assertNotNull($surat->berkas_id);

        // LKS tidak menandai surat keluarnya sendiri; Admin sebagai penerima bisa.
        $this->postJson("/lks/letters/{$surat->id}/read")->assertNotFound();
        $this->actingAs($this->admin());
        $this->postJson("/admin/letters/{$surat->id}/read")->assertOk();
        $this->assertNotNull($surat->fresh()->dibaca_pada);

        $this->postJson('/admin/letters', ['letter_date' => '2026-10-02', 'subject' => 'Tanpa Tujuan'])->assertUnprocessable();
        $this->postJson('/admin/letters', ['letter_date' => '2026-10-02', 'subject' => 'Surat Dinas', 'organization_id' => $akun->lembaga_id])->assertOk();
        $dinas = SuratLks::where('perihal', 'Surat Dinas')->firstOrFail();
        $this->assertSame(SuratLks::DINAS_KE_LKS, $dinas->arah);
        $this->assertNotNull($akun->notifications()->where('data', 'like', '%Surat masuk dari Dinas%')->first());

        // LKS hanya bisa menghapus surat keluarnya.
        $this->actingAs($akun);
        $this->postJson("/lks/letters/{$dinas->id}/read")->assertOk();
        $this->deleteJson("/lks/letters/{$dinas->id}")->assertNotFound();
        $this->deleteJson("/lks/letters/{$surat->id}")->assertOk();
    }

    public function test_notification_read_state_belongs_to_the_owner(): void
    {
        $akun = $this->lks();
        $id = $akun->notifications()->whereNull('read_at')->value('id');
        $this->actingAs($this->admin())->postJson("/notifications/{$id}/unread")->assertNotFound();
        $this->actingAs($akun)->get('/lks/notification-detail/'.$id)->assertOk();
        $this->assertNotNull($akun->notifications()->find($id)->read_at);
        $this->postJson("/notifications/{$id}/unread")->assertOk();
        $this->assertNull($akun->notifications()->find($id)->read_at);
        $this->postJson('/notifications/read-all')->assertOk();
        $this->assertSame(0, $akun->notifications()->whereNull('read_at')->count());
    }

    public function test_landing_content_changes_public_page_and_can_be_restored(): void
    {
        $this->actingAs($this->admin());
        $this->postJson('/admin/landing-content/pillars.page.02', ['value' => 'Judul *Baru* Pilar'])->assertOk();
        $this->get('/admin/landing-content?filter=pillars')->assertSee('Diubah');
        $this->postJson('/admin/landing-content/tidak.ada', ['value' => 'x'])->assertNotFound();
        $this->get('/pilar-sosial.html')->assertSee('Judul <em>Baru</em> Pilar', false);

        // Isi berbahaya tetap ditampilkan sebagai teks.
        $this->postJson('/admin/landing-content/pillars.page.03', ['value' => '<script>alert(1)</script>'])->assertOk();
        $this->get('/pilar-sosial.html')->assertDontSee('<script>alert(1)</script>', false);

        $this->post('/admin/landing-content/pillars.daftar-pilar.foto-1/image', ['image' => UploadedFile::fake()->image('foto.jpg', 800, 600)], ['Accept' => 'application/json'])->assertOk();
        $foto = IsiLanding::where('kunci', 'pillars.daftar-pilar.foto-1')->value('berkas_id');
        $this->get('/pilar-sosial.html')->assertSee('/media/'.$foto, false);
        auth()->logout();
        $this->get('/media/'.$foto)->assertOk();

        $this->actingAs($this->admin());
        $this->post('/admin/landing-content/pillars.daftar-pilar.foto-1/image', ['image' => UploadedFile::fake()->image('kecil.jpg', 100, 100)], ['Accept' => 'application/json'])->assertUnprocessable();
        $this->deleteJson('/admin/landing-content/pillars.daftar-pilar.foto-1')->assertOk();
        $this->assertNull(BerkasTersimpan::find($foto));
        $this->get('/media/'.$foto)->assertNotFound();
        $this->deleteJson('/admin/landing-content/pillars.page.02')->assertOk();
        $this->get('/pilar-sosial.html')->assertSee('Pilar <em>pemberdayaan</em> sosial', false);
    }

    public function test_private_registration_files_stay_private_and_media_rejects_them(): void
    {
        $berkas = BerkasTersimpan::where('jenis_sumber', 'pendaftaran')->firstOrFail();
        $this->get('/media/'.$berkas->id)->assertNotFound();
        $this->get('/berkas/'.$berkas->id)->assertRedirect('/login');
        $this->actingAs($this->lks())->get('/berkas/'.$berkas->id)->assertForbidden();
        $this->actingAs($this->admin())->get('/berkas/'.$berkas->id)->assertOk();
    }

    public function test_admin_settings_are_saved(): void
    {
        $admin = $this->admin();
        $versi = $admin->versi;
        $this->actingAs($admin);
        $this->putJson('/account/profile', ['version' => $versi, 'name' => 'Admin Ubah', 'email' => 'admin@sidaya.example', 'position' => 'Kepala'])->assertOk();
        $this->assertSame('Admin Ubah', $admin->fresh()->nama);
        $this->assertFalse($admin->fresh()->preferensi_notifikasi['showUnreadBadge']);
        $this->putJson('/account/profile', ['version' => $versi, 'name' => 'B', 'email' => 'b@sidaya.example', 'position' => 'K'])->assertConflict();
    }

    public function test_exports_follow_scope_and_filters(): void
    {
        $akun = $this->lks();
        $lain = $this->lembagaLain()->id;
        $this->actingAs($akun);
        $klien = $this->getJson('/ekspor/klien?residence=')->assertOk()->assertJsonPath('kind', 'klien');
        $this->assertCount(KlienLks::where('lembaga_id', $akun->lembaga_id)->count(), $klien->json('items'));
        $this->assertSame([$akun->lembaga_id], collect($klien->json('organizations'))->pluck('id')->all());
        $this->assertArrayHasKey('birthDate', $klien->json('items.0.data'));

        $this->getJson('/ekspor/lembaga')->assertForbidden();
        $pengurus = $this->getJson('/ekspor/pengurus?org='.$lain)->assertOk();
        $this->assertSame($akun->lembaga_id, $pengurus->json('org.id'));
        $this->assertCount(PengurusLks::where('lembaga_id', $akun->lembaga_id)->count(), $pengurus->json('members'));
        $this->assertCount(PengurusLks::where('lembaga_id', $akun->lembaga_id)->where('kategori', 'Staf')->count(), $this->getJson('/ekspor/pengurus?category=Staf')->json('members'));

        $laporan = $this->getJson('/ekspor/laporan')->assertOk()->assertJsonPath('kind', 'laporanLks');
        $this->assertCount(LaporanSemester::where('lembaga_id', $akun->lembaga_id)->count(), $laporan->json('items'));
        $this->assertContains($laporan->json('items.0.quarter'), [1, 2]);
        $this->assertCount(LaporanSemester::where('lembaga_id', $akun->lembaga_id)->where('status', 'Draf')->count(), $this->getJson('/ekspor/laporan?status=Draf')->json('items'));
        $this->getJson('/ekspor/laporan/'.$laporan->json('items.0.id'))->assertForbidden();

        $surat = $this->getJson('/ekspor/surat')->assertOk()->assertJsonPath('adminMode', false)->assertJsonPath('prefix', 'Surat_Masuk_LKS');
        $this->assertSame([$akun->lembaga_id], collect($surat->json('items'))->pluck('organization_id')->unique()->values()->all());

        $this->actingAs($this->admin());
        $this->assertCount(KlienLks::count(), $this->getJson('/ekspor/klien?residence=')->json('items'));
        $this->assertCount(Lembaga::count(), $this->getJson('/ekspor/lembaga')->json('items'));
        $this->getJson('/ekspor/pengurus?org='.$lain)->assertOk()->assertJsonPath('org.id', $lain);
        $this->getJson('/ekspor/pengurus')->assertNotFound();
        $admin = $this->getJson('/ekspor/laporan')->assertJsonPath('kind', 'laporanAdmin');
        $admin->assertJsonPath('context.year', '')->assertJsonPath('context.quarter', '');
        $this->getJson('/ekspor/laporan?year=2026&quarter=all')->assertJsonPath('context.year', '2026')->assertJsonPath('context.quarter', 'all');
        $this->assertCount(LaporanSemester::where('status', 'Terkirim')->count(), $admin->json('reports'));
        $this->assertNotContains('Draf', collect($admin->json('reports'))->pluck('status')->all());
        $this->getJson('/ekspor/laporan/'.LaporanSemester::where('status', 'Draf')->value('id'))->assertNotFound();
        $this->getJson('/ekspor/laporan/'.LaporanSemester::where('status', 'Terkirim')->value('id'))->assertOk()->assertJsonPath('report.pillar', 'lks');
        $this->getJson('/ekspor/surat?tab=outgoing&year=2026&month=3')->assertJsonPath('periodText', 'Maret 2026')->assertJsonPath('prefix', 'Surat_Keluar_LKS_Admin');
    }

    public function test_sample_passwords_are_hashed_and_seed_is_lks_only(): void
    {
        $this->assertNotSame(DataContohSeeder::SANDI_LKS, $this->lks()->kata_sandi);
        $this->assertSame(['admin', 'lks'], Pengguna::distinct()->orderBy('peran')->pluck('peran')->all());
    }
}
