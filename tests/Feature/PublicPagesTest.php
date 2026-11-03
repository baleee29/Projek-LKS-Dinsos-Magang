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

    public function test_other_pillar_pages_do_not_exist(): void
    {
        foreach (['/pilar/psm.html', '/pilar/karang-taruna.html', '/pilar/pub.html', '/pilar/pendaftaran-karang-taruna.html'] as $url) {
            $this->get($url)->assertNotFound();
        }
    }

    public function test_landing_editor_only_lists_general_and_lks_pages(): void
    {
        $response = $this->actingAs($this->admin())->get('/admin/landing-content')->assertOk();

        foreach (['Beranda', 'Profil', 'Kontak', 'Pilar Sosial', 'LKS', 'Footer'] as $tab) {
            $response->assertSee($tab);
        }
        $response->assertDontSee('Karang Taruna');
        $response->assertSee('(tab baru)', false);
    }
}
