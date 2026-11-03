<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class DashboardAccessTest extends TestCase
{
    /** @return array<string, array{string}> */
    public static function adminUrls(): array
    {
        return self::named([
            '/admin/overview', '/admin/notifications', '/admin/notification-detail/notif-001',
            '/admin/registrations/lks', '/admin/registration/reg-lks-001', '/admin/partner-accounts',
            '/admin/organizations/lks', '/admin/organization/lks-001', '/admin/member/member-101',
            '/admin/occupancy', '/admin/resident/client-002', '/admin/reports/lks', '/admin/report/RPT-LKS002-2026-S1',
            '/admin/letters/lks', '/admin/guide', '/admin/landing-content', '/admin/account',
        ]);
    }

    /** @return array<string, array{string}> */
    public static function lksUrls(): array
    {
        return self::named([
            '/lks/overview', '/lks/notifications', '/lks/notification-detail/notif-011', '/lks/profile',
            '/lks/members', '/lks/new-member', '/lks/edit-member/member-101', '/lks/member/member-102',
            '/lks/letters', '/lks/new-resident', '/lks/edit-resident/client-001', '/lks/occupancy',
            '/lks/resident/client-001', '/lks/reports', '/lks/drafts', '/lks/report/RPT-LKS001-2026-S1',
            '/lks/edit/RPT-LKS001-2026-S2', '/lks/guide',
        ]);
    }

    /**
     * @param  array<int, string>  $urls
     * @return array<string, array{string}>
     */
    private static function named(array $urls): array
    {
        return array_combine($urls, array_map(fn (string $url) => [$url], $urls));
    }

    #[DataProvider('adminUrls')]
    public function test_admin_pages_open_for_admin_and_refuse_lks(string $url): void
    {
        $this->withSession(['demo_role' => 'admin'])->get($url)->assertOk();
        $this->withSession(['demo_role' => 'lks'])->get($url)->assertForbidden();
    }

    #[DataProvider('lksUrls')]
    public function test_lks_pages_open_for_lks_and_refuse_admin(string $url): void
    {
        $this->withSession(['demo_role' => 'lks'])->get($url)->assertOk();
        $this->withSession(['demo_role' => 'admin'])->get($url)->assertForbidden();
    }

    #[DataProvider('adminUrls')]
    #[DataProvider('lksUrls')]
    public function test_guest_is_sent_to_login(string $url): void
    {
        $this->get($url)->assertRedirect('/login');
    }

    public function test_lks_cannot_open_another_institutions_records(): void
    {
        $session = $this->withSession(['demo_role' => 'lks']);

        $session->get('/lks/member/member-111')->assertNotFound();
        $session->get('/lks/report/RPT-LKS002-2026-S1')->assertNotFound();
        $session->get('/lks/resident/client-010')->assertNotFound();
    }

    public function test_admin_cannot_open_an_unsubmitted_draft(): void
    {
        $this->withSession(['demo_role' => 'admin'])->get('/admin/report/RPT-LKS001-2026-S2')->assertNotFound();
    }

    public function test_unknown_page_uses_the_dashboard_shell_for_a_signed_in_user(): void
    {
        $this->withSession(['demo_role' => 'lks'])->get('/lks/tidak-ada')
            ->assertNotFound()
            ->assertSee('id="dashboard-shell"', false);
    }

    public function test_retired_features_have_no_route(): void
    {
        $session = $this->withSession(['demo_role' => 'admin']);

        foreach (['/admin/member-applications', '/admin/resident-verifications', '/admin/revisions'] as $url) {
            $session->get($url)->assertNotFound();
        }
    }

    public function test_admin_only_manages_lks(): void
    {
        $session = $this->withSession(['demo_role' => 'admin']);

        foreach ([
            '/admin/registrations/karang-taruna', '/admin/organizations/karang-taruna', '/admin/members/psm', '/admin/complaints/psm',
            '/admin/incoming-letters/karang-taruna', '/admin/reply-letters/karang-taruna', '/admin/new-reply-letter',
        ] as $url) {
            $session->get($url)->assertNotFound();
        }

        foreach (['/admin/overview', '/admin/guide', '/admin/partner-accounts', '/admin/notifications', '/admin/landing-content'] as $url) {
            $response = $session->get($url)->assertOk();
            $text = strip_tags($response->getContent());
            $this->assertStringNotContainsString('Karang Taruna', $text, $url);
            $this->assertStringNotContainsString('PSM', $text, $url);
        }
    }

    public function test_sidebar_badge_matches_the_table_it_counts(): void
    {
        $response = $this->withSession(['demo_role' => 'admin'])->get('/admin/registrations/lks?status=Menunggu%20Verifikasi');

        $pending = \App\Support\Demo\DemoRegistrations::pending();
        $response->assertOk()->assertSee('<span class="psm-nav-count">'.$pending.'</span>', false);
        $this->assertSame($pending, substr_count($response->getContent(), 'data-status="Menunggu Verifikasi"'));
    }
}
