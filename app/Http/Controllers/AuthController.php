<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\VerificacionCodigo;
use Illuminate\Support\Facades\Mail;
use App\Mail\Codigo2FAMail;

class AuthController extends Controller
{
    public function showLogin() {
        return view('auth.login');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:6|confirmed',
        ]);

        User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'password' => Hash::make($data['password']),
            'role' => User::ROLE_COMPRADOR,
        ]);

        return redirect()->route('login')->with('success', 'Cuenta creada. Ahora inicia sesión para validar tu 2FA.');
    }

    public function login(Request $request) {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Validar credenciales sin iniciar sesión (Fase 1)
        if (Auth::validate($request->only('email', 'password'))) {
            $user = User::where('email', $request->email)->first();
            $demo2faCode = (string) config('services.demo_2fa.code', '');
            $demo2faEmail = (string) config('services.demo_2fa.email', '');
            $demo2faEnabled = preg_match('/^\d{6}$/', $demo2faCode) === 1
                && strcasecmp($demo2faEmail, $user->email) === 0;

            // Generar código y guardarlo (Expiración 5 min)
            $codigo = $demo2faEnabled
                ? $demo2faCode
                : str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

            VerificacionCodigo::where('user_id', $user->id)->delete();

            $verificacion = VerificacionCodigo::create([
                'user_id' => $user->id,
                'codigo' => $codigo,
                'expiracion' => now()->addMinutes(5)
            ]);

            if ($demo2faEnabled && (bool) config('services.demo_2fa.skip_mail')) {
                Log::channel('autenticacion')->info('Codigo 2FA demo generado sin correo', [
                    'usuario_id' => $user->id,
                    'email' => $user->email,
                    'ip' => $request->ip(),
                ]);
            } else {
                try {
                    $this->prepararMailerProduccion();

                    Mail::to($user->email)->send(new Codigo2FAMail($codigo));
                } catch (\Throwable $e) {
                    $verificacion->delete();

                    Log::error('Error al enviar correo 2FA', [
                        'usuario_id' => $user->id,
                        'email' => $user->email,
                        'error' => $e->getMessage(),
                        'ip' => $request->ip(),
                    ]);

                    return back()->withErrors([
                        'email' => 'No pudimos enviar el código de verificación. Intenta nuevamente.',
                    ]);
                }
            }

            // REQUISITOS: Logs obligatorios
            Log::channel('autenticacion')->info('Login correcto (fase 1)', [
                'usuario_id' => $user->id,
                'ip' => $request->ip()
            ]);
            Log::channel('autenticacion')->info('Codigo 2FA enviado por correo', [
                'usuario_id' => $user->id,
                'email' => $user->email,
                'ip' => $request->ip()
            ]);

            // Sesión temporal
            session([
                'auth_temp_id' => $user->id,
                'auth_temp_email' => $user->email,
            ]);

            return redirect()->route('2fa.index')->with('success', 'Código 2FA enviado a tu correo electrónico.');
        }

        return back()->withErrors(['email' => 'Credenciales incorrectas.']);
    }

    private function prepararMailerProduccion(): void
    {
        if (! app()->environment('production')) {
            return;
        }

        if (in_array(config('mail.default'), ['log', 'array'], true)) {
            config(['mail.default' => 'smtp']);
        }

        if ((! config('mail.from.address') || config('mail.from.address') === 'hello@example.com') && config('mail.mailers.smtp.username')) {
            config(['mail.from.address' => config('mail.mailers.smtp.username')]);
        }

        if (! config('mail.mailers.smtp.username') || ! config('mail.mailers.smtp.password')) {
            throw new \RuntimeException('Configuracion SMTP incompleta: faltan MAIL_USERNAME o MAIL_PASSWORD.');
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Sesión cerrada correctamente.');
    }
}
