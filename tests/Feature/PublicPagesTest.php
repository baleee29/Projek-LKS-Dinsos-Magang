<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class PublicPagesTest extends TestCase
{
    /** @return array<string, array{string}> */
    public static function pages(): array
    {
        return ['beranda' => ['/'], 'index' => ['/index.html'], 'profil' => ['/profil.html'], 'kontak' => ['/kontak.html'], 'pilar-sosial' => ['/pilar-sosial.html'], 'lks' => ['/pilar/lks.html'], 'pendaftaran' => ['/pilar/pendaftaran-lks.html']];
    }

    #[DataProvider('pages')]
    public function test_page_is_public_and_only_about_lks(string $url): void
    {
        $text = $this->get($url)->assertOk()->getContent();

        foreach (['PSM', 'Karang Taruna', 'PUB'] as $word) {
            $this->assertStringNotContainsString($word, strip_tags($text), "{$url}: {$word}");
            $this->assertStringNotContainsString('href="/pilar/psm', $text);
        }
    }

    public function test_pillar_hub_is_in_navigation_and_only_lists_lks(): void
    {
        $this->get('/')->assertSee('href="/pilar-sosial.html"', false)->assertSeeText('Pilar Sosial');

        foreach (['/', '/login'] as $url) {
            $this->get($url)->assertSeeText('Semua pilar sosial');
        }

        $text = $this->get('/pilar-sosial.html')->assertOk()->assertSee('href="/pilar/lks.html"', false)->getContent();
        $this->assertSame(1, substr_count($text, 'class="pillar-panel-item"'));
    }

    public function test_registration_form_has_no_backend(): void
    {
        $this->get('/pilar/pendaftaran-lks.html')->assertOk()->assertSee('id="form-lks"', false)->assertDontSee('name="_token"', false);
        $this->post('/pilar/pendaftaran-lks.html')->assertStatus(405);
        $this->post('/pendaftaran/mitra')->assertStatus(405);

        foreach (['lks.js', 'lks-documents.js', 'lks-rules.js'] as $file) {
            $source = (string) file_get_contents(resource_path('js/public/registrations/'.$file));
            foreach (['localStorage', 'sessionStorage', 'indexedDB', 'XMLHttpRequest', 'sendBeacon', 'method:'] as $api) {
                $this->assertStringNotContainsString($api, $source, "{$file}: {$api}");
            }
        }
    }

    public function test_other_pillar_pages_do_not_exist(): void
    {
        foreach (['/pilar/psm.html', '/pilar/karang-taruna.html', '/pilar/pub.html', '/pilar/pendaftaran-karang-taruna.html'] as $url) {
            $this->get($url)->assertNotFound();
        }
    }

    public function test_landing_editor_only_lists_general_and_lks_pages(): void
    {
        $response = $this->withSession(['demo_role' => 'admin'])->get('/admin/landing-content')->assertOk();

        foreach (['Beranda', 'Profil', 'Kontak', 'Pilar Sosial', 'LKS', 'Footer'] as $tab) {
            $response->assertSee($tab);
        }
        $response->assertDontSee('Karang Taruna');
        $response->assertSee('(tab baru)', false);
    }
}
