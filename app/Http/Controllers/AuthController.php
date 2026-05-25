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

            // Generar código y guardarlo (Expiración 5 min)
            $codigo = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

            VerificacionCodigo::where('user_id', $user->id)->delete();

            $verificacion = VerificacionCodigo::create([
                'user_id' => $user->id,
                'codigo' => $codigo,
                'expiracion' => now()->addMinutes(5)
            ]);

            try {
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

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Sesión cerrada correctamente.');
    }
}
