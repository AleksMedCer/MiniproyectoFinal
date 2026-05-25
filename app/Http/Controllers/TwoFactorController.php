<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\VerificacionCodigo;
use App\Models\User;

class TwoFactorController extends Controller
{
    // Mostrar vista para escribir el código
    public function index()
    {
        // Si no hay sesión temporal, lo regresamos al login
        if (!session('auth_temp_id')) {
            return redirect()->route('login');
        }

        $user = User::find(session('auth_temp_id'));

        if (! $user) {
            session()->forget(['auth_temp_id', 'auth_temp_email']);

            return redirect()->route('login');
        }

        return view('auth.2fa', [
            'correoDestino' => $user->email,
        ]);
    }

    // Procesar y validar el código
    public function verify(Request $request)
    {
        $request->validate(['codigo' => 'required|digits:6']);
        $userId = session('auth_temp_id');

        if (!$userId) {
            return redirect()->route('login');
        }

        // Buscar el último código generado para este usuario
        $verificacion = VerificacionCodigo::where('user_id', $userId)
        ->where('codigo', $request->codigo)
        ->latest()
        ->first();

        if ($verificacion) {
            // Verificar expiración
            if ($verificacion->expiracion->isFuture()) {

                // REQUISITO CUMPLIDO: Iniciar sesión ahora sí
                Auth::loginUsingId($userId);

                // Limpieza de seguridad
                session()->forget(['auth_temp_id', 'auth_temp_email']);
                VerificacionCodigo::where('user_id', $userId)->delete();

                // Log obligatorio
                Log::channel('autenticacion')->info('Código validado correctamente', [
                    'usuario_id' => $userId, 'ip' => $request->ip()
                ]);

                return redirect()->route('dashboard'); // Redirigir al panel principal
            } else {
                // Log obligatorio
                Log::channel('autenticacion')->warning('Código expirado', [
                    'usuario_id' => $userId, 'ip' => $request->ip()
                ]);
                return back()->withErrors(['codigo' => 'El código de verificación ha expirado. Genera uno nuevo.']);
            }
        }

        // Log obligatorio
        Log::channel('autenticacion')->warning('Código inválido', [
            'usuario_id' => $userId, 'ip' => $request->ip()
        ]);

        return back()->withErrors(['codigo' => 'El código ingresado es incorrecto.']);
    }
}
