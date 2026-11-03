<?php

namespace Tests\Feature;

use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Tests\TestCase;

class DemoLoginTest extends TestCase
{
    private function asLocal(): void
    {
        $this->app->detectEnvironment(fn () => 'local');
        // Perlindungan CSRF hanya dilewati otomatis saat APP_ENV=testing.
        $this->withoutMiddleware(PreventRequestForgery::class);
    }

    public function test_quick_login_fills_the_session_and_opens_the_role_dashboard(): void
    {
        $this->asLocal();

        foreach (['admin', 'lks'] as $role) {
            $this->flushSession();
            $this->post("/login/demo/{$role}")
                ->assertRedirect("/{$role}/overview")
                ->assertSessionHas('demo_role', $role);
        }
    }

    public function test_quick_login_refuses_roles_outside_the_scope(): void
    {
        $this->asLocal();

        foreach (['psm', 'karang-taruna', 'psm-member'] as $role) {
            $this->post("/login/demo/{$role}")->assertNotFound();
        }
    }

    public function test_demo_login_is_inactive_outside_local(): void
    {
        $this->post('/login/demo/admin')->assertNotFound();
        $this->post('/login', ['email' => 'admin', 'password' => 'apa saja'])->assertNotFound();
        $this->get('/login')->assertOk()->assertDontSee('data-demo-account', false);
    }

    public function test_login_form_ignores_the_password_and_does_not_keep_it(): void
    {
        $this->asLocal();

        $this->post('/login', ['email' => 'lks@sidaya.example', 'password' => 'sembarang'])
            ->assertRedirect('/lks/overview')
            ->assertSessionHas('demo_role', 'lks')
            ->assertSessionMissing('password');
    }

    public function test_login_form_reports_empty_and_unknown_accounts(): void
    {
        $this->asLocal();

        $this->post('/login', [])->assertSessionHasErrors(['email', 'password']);
        $this->post('/login', ['email' => 'tidak-dikenal', 'password' => 'x'])->assertSessionHasErrors('email');
    }

    public function test_logout_clears_the_session(): void
    {
        $this->withSession(['demo_role' => 'admin'])->post('/logout')
            ->assertRedirect('/login')
            ->assertSessionMissing('demo_role');
    }

    public function test_change_password_page_requires_a_session(): void
    {
        $this->get('/account/password')->assertRedirect('/login');
        $this->withSession(['demo_role' => 'lks'])->get('/account/password')->assertOk();
    }

    public function test_demo_state_preview_only_works_locally(): void
    {
        $url = '/lks/occupancy?demo_state=empty&residence=';
        $this->withSession(['demo_role' => 'lks'])->get($url)->assertOk()->assertDontSee('Belum ada data sesuai pilihan');

        $this->asLocal();
        $this->withSession(['demo_role' => 'lks'])->get($url)->assertOk()->assertSee('Belum ada data sesuai pilihan');
    }
}
