<?php

namespace Tests\Feature;

use App\Mail\Codigo2FAMail;
use App\Models\User;
use App\Models\VerificacionCodigo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class TwoFactorEmailTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_correcto_envia_codigo_2fa_al_correo_del_usuario(): void
    {
        Mail::fake();

        $user = User::factory()->create([
            'email' => 'cliente@example.com',
            'password' => bcrypt('password'),
        ]);

        $response = $this->from('/entrar')->post('/entrar', [
            'email' => 'cliente@example.com',
            'password' => 'password',
        ]);

        $response->assertRedirect('/2fa');
        $response->assertSessionHas('auth_temp_id', $user->id);
        $response->assertSessionHas('auth_temp_email', 'cliente@example.com');
        $response->assertSessionMissing('auth_temp_phone');

        $this->assertDatabaseHas('verificacion_codigos', [
            'user_id' => $user->id,
        ]);

        Mail::assertSent(Codigo2FAMail::class, function (Codigo2FAMail $mail) {
            return $mail->hasTo('cliente@example.com');
        });
    }

    public function test_pantalla_2fa_muestra_correo_destino_y_no_telefono_de_prueba(): void
    {
        $user = User::factory()->create([
            'email' => 'cliente@example.com',
        ]);

        VerificacionCodigo::create([
            'user_id' => $user->id,
            'codigo' => '123456',
            'expiracion' => now()->addMinutes(5),
        ]);

        $response = $this
            ->withSession([
                'auth_temp_id' => $user->id,
                'auth_temp_email' => 'cliente@example.com',
            ])
            ->get('/2fa');

        $response->assertOk();
        $response->assertSee('cliente@example.com');
        $response->assertDontSee('9617017722');
        $response->assertDontSee('Código de prueba');
    }

    public function test_usuario_demo_puede_generar_codigo_2fa_sin_smtp(): void
    {
        Mail::fake();

        config([
            'services.demo_2fa.email' => 'medcer94@gmail.com',
            'services.demo_2fa.code' => '123456',
            'services.demo_2fa.skip_mail' => true,
        ]);

        $user = User::factory()->create([
            'email' => 'medcer94@gmail.com',
            'password' => bcrypt('password'),
        ]);

        $response = $this->from('/entrar')->post('/entrar', [
            'email' => 'medcer94@gmail.com',
            'password' => 'password',
        ]);

        $response->assertRedirect('/2fa');

        $this->assertDatabaseHas('verificacion_codigos', [
            'user_id' => $user->id,
            'codigo' => '123456',
        ]);

        Mail::assertNothingSent();
    }

    public function test_comando_mail_test_envia_correo_de_prueba(): void
    {
        Mail::fake();

        $this->artisan('mail:test', ['email' => 'medcer94@gmail.com'])
            ->assertExitCode(0);

        Mail::assertSent(Codigo2FAMail::class, function (Codigo2FAMail $mail) {
            return $mail->hasTo('medcer94@gmail.com');
        });
    }

    public function test_comando_mail_diagnose_no_revela_password(): void
    {
        config([
            'mail.mailers.smtp.username' => 'correo@example.com',
            'mail.mailers.smtp.password' => 'secreto-super-privado',
        ]);

        $this->artisan('mail:diagnose')
            ->expectsOutput('MAIL_USERNAME=configurado')
            ->expectsOutput('MAIL_PASSWORD=configurado')
            ->assertExitCode(0);
    }
}
