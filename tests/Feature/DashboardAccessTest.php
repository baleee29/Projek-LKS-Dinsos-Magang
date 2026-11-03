<?php

namespace Tests\Feature;

use App\Models\KlienLks;
use App\Models\LaporanSemester;
use App\Models\Notifikasi;
use App\Models\PendaftaranLks;
use App\Models\PengurusLks;
use App\Support\Data\DataPendaftaran;
use Tests\TestCase;

class DashboardAccessTest extends TestCase
{
    /** @return array<int, string> */
    private function adminUrls(): array
    {
        $lembagaId = $this->lks()->lembaga_id;

        return [
            '/admin/overview', '/admin/notifications', '/admin/notification-detail/'.$this->admin()->notifications()->value('id'),
            '/admin/registrations/lks', '/admin/registration/'.PendaftaranLks::value('id'), '/admin/partner-accounts',
            '/admin/organizations/lks', '/admin/organization/'.$lembagaId, '/admin/member/'.PengurusLks::value('id'),
            '/admin/occupancy', '/admin/resident/'.KlienLks::value('id'), '/admin/reports/lks',
            '/admin/report/'.LaporanSemester::where('status', 'Terkirim')->value('id'),
            '/admin/letters/lks', '/admin/guide', '/admin/landing-content', '/admin/account',
        ];
    }

    /** @return array<int, string> */
    private function lksUrls(): array
    {
        $id = $this->lks()->lembaga_id;
        $pengurus = PengurusLks::where('lembaga_id', $id)->value('id');
        $klien = KlienLks::where('lembaga_id', $id)->value('id');
        $laporan = LaporanSemester::where('lembaga_id', $id)->value('id');

        return [
            '/lks/overview', '/lks/notifications', '/lks/notification-detail/'.$this->lks()->notifications()->value('id'), '/lks/profile',
            '/lks/members', '/lks/new-member', '/lks/edit-member/'.$pengurus, '/lks/member/'.$pengurus,
            '/lks/letters', '/lks/new-resident', '/lks/edit-resident/'.$klien, '/lks/occupancy',
            '/lks/resident/'.$klien, '/lks/reports', '/lks/drafts', '/lks/report/'.$laporan, '/lks/edit/'.$laporan, '/lks/guide',
        ];
    }

    public function test_admin_pages_open_for_admin_and_refuse_lks_and_guest(): void
    {
        $urls = $this->adminUrls();
        foreach ($urls as $url) {
            $this->actingAs($this->admin())->get($url)->assertOk();
        }
        foreach ($urls as $url) {
            $this->actingAs($this->lks())->get($url)->assertForbidden();
        }
        auth()->logout();
        foreach ($urls as $url) {
            $this->get($url)->assertRedirect('/login');
        }
    }

    public function test_lks_pages_open_for_lks_and_refuse_admin_and_guest(): void
    {
        $urls = $this->lksUrls();
        foreach ($urls as $url) {
            $this->actingAs($this->lks())->get($url)->assertOk();
        }
        foreach ($urls as $url) {
            $this->actingAs($this->admin())->get($url)->assertForbidden();
        }
        auth()->logout();
        foreach ($urls as $url) {
            $this->get($url)->assertRedirect('/login');
        }
    }

    public function test_lks_cannot_open_another_institutions_records(): void
    {
        $lain = $this->lembagaLain()->id;
        $this->actingAs($this->lks());

        $this->get('/lks/member/'.PengurusLks::where('lembaga_id', $lain)->value('id'))->assertNotFound();
        $this->get('/lks/resident/'.KlienLks::where('lembaga_id', $lain)->value('id'))->assertNotFound();
        LaporanSemester::where('lembaga_id', '!=', $this->lks()->lembaga_id)->take(1)->get()
            ->each(fn ($laporan) => $this->get('/lks/report/'.$laporan->id)->assertNotFound());
    }

    public function test_admin_cannot_open_an_unsubmitted_draft(): void
    {
        $this->actingAs($this->admin())->get('/admin/report/'.LaporanSemester::where('status', 'Draf')->value('id'))->assertNotFound();
    }

    public function test_notification_of_another_user_is_not_visible(): void
    {
        $milikLain = Notifikasi::where('notifiable_id', $this->lks()->id)->value('id');
        $this->actingAs($this->admin())->get('/admin/notification-detail/'.$milikLain)->assertNotFound();
    }

    public function test_unknown_page_uses_the_dashboard_shell_for_a_signed_in_user(): void
    {
        $this->actingAs($this->lks())->get('/lks/tidak-ada')->assertNotFound()->assertSee('id="dashboard-shell"', false);
    }

    public function test_retired_features_have_no_route(): void
    {
        $this->actingAs($this->admin());
        foreach (['/admin/member-applications', '/admin/resident-verifications', '/admin/revisions'] as $url) {
            $this->get($url)->assertNotFound();
        }
    }

    public function test_admin_only_manages_lks(): void
    {
        $this->actingAs($this->admin());
        foreach ([
            '/admin/registrations/karang-taruna', '/admin/organizations/karang-taruna', '/admin/members/psm', '/admin/complaints/psm',
            '/admin/incoming-letters/karang-taruna', '/admin/reply-letters/karang-taruna', '/admin/new-reply-letter',
        ] as $url) {
            $this->get($url)->assertNotFound();
        }
        foreach (['/admin/overview', '/admin/guide', '/admin/partner-accounts', '/admin/notifications', '/admin/landing-content'] as $url) {
            $text = strip_tags($this->get($url)->assertOk()->getContent());
            $this->assertStringNotContainsString('Karang Taruna', $text, $url);
            $this->assertStringNotContainsString('PSM', $text, $url);
        }
    }

    public function test_sidebar_badge_matches_the_table_it_counts(): void
    {
        $response = $this->actingAs($this->admin())->get('/admin/registrations/lks?status=Menunggu%20Verifikasi');
        $menunggu = DataPendaftaran::pending();

        $response->assertOk()->assertSee('<span class="psm-nav-count">'.$menunggu.'</span>', false);
        $this->assertSame($menunggu, substr_count($response->getContent(), 'data-status="Menunggu Verifikasi"'));
    }

    public function test_project_has_no_vue_or_inertia(): void
    {
        $composer = (string) file_get_contents(base_path('composer.json'));
        $package = (string) file_get_contents(base_path('package.json'));

        foreach (['vue', 'inertia', 'react', 'livewire', 'tailwind', 'bootstrap'] as $name) {
            $this->assertStringNotContainsStringIgnoringCase($name, $composer);
            $this->assertStringNotContainsStringIgnoringCase($name, $package);
        }
    }
}
