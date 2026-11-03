<?php

namespace Tests\Feature;

use App\Models\Pengguna;
use Database\Seeders\DataContohSeeder;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class LoginTest extends TestCase
{
    private function asLocal(): void
    {
        $this->app->detectEnvironment(fn () => 'local');
        // Perlindungan CSRF hanya dilewati otomatis saat APP_ENV=testing.
        $this->withoutMiddleware(PreventRequestForgery::class);
    }

    public function test_login_with_email_or_username_opens_the_role_dashboard(): void
    {
        $this->post('/login', ['email' => 'admin@sidaya.example', 'password' => DataContohSeeder::SANDI_ADMIN])->assertRedirect('/admin/overview');
        $this->assertAuthenticatedAs($this->admin());
        $this->post('/logout');

        $this->post('/login', ['email' => $this->lks()->nama_pengguna, 'password' => DataContohSeeder::SANDI_LKS])->assertRedirect('/lks/overview');
        $this->assertAuthenticatedAs($this->lks());
    }

    public function test_wrong_password_and_empty_form_are_refused(): void
    {
        $this->post('/login', ['email' => 'admin@sidaya.example', 'password' => 'salah'])->assertSessionHasErrors('email');
        $this->assertGuest();
        $this->post('/login', [])->assertSessionHasErrors(['email', 'password']);
    }

    public function test_revoked_account_cannot_login_and_loses_its_session(): void
    {
        $akun = $this->lks();
        $this->actingAs($akun)->get('/lks/overview')->assertOk();
        $akun->forceFill(['aktif' => false])->save();
        $this->get('/lks/overview')->assertRedirect('/login');
        $this->assertGuest();
        $this->post('/login', ['email' => $akun->email, 'password' => DataContohSeeder::SANDI_LKS])->assertSessionHasErrors('email');
    }

    public function test_quick_login_only_works_locally_and_only_for_known_roles(): void
    {
        $this->post('/login/demo/admin')->assertNotFound();
        $this->get('/login')->assertOk()->assertDontSee('data-demo-account', false);

        $this->asLocal();
        $this->post('/login/demo/psm')->assertNotFound();
        $this->post('/login/demo/lks')->assertRedirect('/lks/overview');
    }

    public function test_logout_ends_the_session(): void
    {
        $this->actingAs($this->admin())->post('/logout')->assertRedirect('/login');
        $this->assertGuest();
    }

    public function test_temporary_admin_password_must_be_changed_first(): void
    {
        $admin = $this->admin();
        $admin->forceFill(['wajib_ganti_sandi' => true])->save();
        $this->actingAs($admin)->get('/admin/overview')->assertRedirect('/account/password');
        $this->get('/account/password')->assertOk();

        $this->putJson('/account/password', ['current_password' => 'salah', 'password' => 'SandiBaru12345', 'password_confirmation' => 'SandiBaru12345'])->assertUnprocessable();
        $this->putJson('/account/password', ['current_password' => DataContohSeeder::SANDI_ADMIN, 'password' => 'pendek1', 'password_confirmation' => 'pendek1'])->assertUnprocessable();
        $this->putJson('/account/password', ['current_password' => DataContohSeeder::SANDI_ADMIN, 'password' => 'SandiBaru12345', 'password_confirmation' => 'SandiBaru12345'])->assertOk();
        $this->assertFalse($admin->fresh()->wajib_ganti_sandi);
        $this->get('/admin/overview')->assertOk();
    }

    public function test_admin_recovery_command_forces_a_password_change(): void
    {
        $this->artisan('account:reset-admin', ['email' => 'tidak.ada@sidaya.example'])->assertExitCode(1);
        $this->artisan('account:reset-admin', ['email' => 'admin@sidaya.example'])->assertExitCode(0);
        $this->assertTrue($this->admin()->fresh()->wajib_ganti_sandi);
        $this->post('/login', ['email' => 'admin@sidaya.example', 'password' => DataContohSeeder::SANDI_ADMIN])->assertSessionHasErrors('email');
    }

    public function test_lks_cannot_change_its_own_password(): void
    {
        $this->actingAs($this->lks())->get('/account/password')->assertForbidden();
        $this->putJson('/account/password', [])->assertForbidden();
    }
}
