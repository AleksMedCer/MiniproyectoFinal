<?php

namespace App\Http\Middleware;

use App\Models\Producto;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class LogRequirementChecks
{
    public function handle(Request $request, Closure $next): Response
    {
        /** @var Response $response */
        $response = $next($request);

        if (! app()->environment('local')) {
            return $response;
        }

        $this->logRequiredCase($request, $response);

        return $response;
    }

    private function logRequiredCase(Request $request, Response $response): void
    {
        $method = $request->method();
        $path = $request->getPathInfo();
        $status = $response->getStatusCode();

        if ($method === 'GET' && $path === '/') {
            $this->write('Página principal responde correctamente', $status === 200, [
                'assert' => '$response->assertStatus(200)',
                'status' => $status,
            ]);
        }

        if ($method === 'GET' && $path === '/entrar') {
            $this->write('Página login responde correctamente', $status === 200, [
                'assert' => '$response->assertStatus(200)',
                'status' => $status,
            ]);
        }

        if ($method === 'GET' && $path === '/dashboard') {
            $redirect = $response->headers->get('Location');
            $redirectPath = parse_url($redirect ?? '', PHP_URL_PATH);

            if ($response->isRedirect() && $redirectPath === '/entrar') {
                $this->write('Dashboard requiere autenticación', true, [
                    'assert' => '$response->assertRedirect("/entrar")',
                    'status' => $status,
                    'redirect' => $redirect,
                ]);
            }
        }

        if ($method === 'POST' && $path === '/entrar') {
            $errors = $request->session()->get('errors');
            $hasErrors = false;
            $errorKeys = [];

            if (is_object($errors) && method_exists($errors, 'any')) {
                $hasErrors = $errors->any();
                $errorBag = method_exists($errors, 'getBag') ? $errors->getBag('default') : $errors;
                $errorKeys = method_exists($errorBag, 'keys') ? $errorBag->keys() : [];
            } elseif (is_array($errors)) {
                $hasErrors = count($errors) > 0;
                $errorKeys = array_keys($errors);
            }

            if ($hasErrors || $request->input('email') === 'correo@incorrecto.com') {
                $this->write('Login incorrecto muestra error', $hasErrors, [
                    'assert' => '$response->assertSessionHasErrors()',
                    'status' => $status,
                    'errors' => $errorKeys,
                ]);
            }
        }

        if ($method === 'POST' && $path === '/productos') {
            $nombre = $request->input('nombre');
            $exists = $nombre && Producto::where('nombre', $nombre)->exists();

            $this->write('Registro almacenado en base de datos', (bool) $exists, [
                'assert' => '$this->assertDatabaseHas("productos", ["nombre" => "'.$nombre.'"])',
                'status' => $status,
                'nombre' => $nombre,
            ]);
        }

        if ($method === 'GET' && $path === '/dashboard' && Auth::check()) {
            $this->write('Usuario autenticado puede acceder', $status === 200, [
                'assert' => '$response->assertStatus(200)',
                'status' => $status,
                'user_id' => Auth::id(),
            ]);
        }
    }

    private function write(string $case, bool $passed, array $context): void
    {
        Log::channel('stderr')->info(($passed ? 'OK' : 'FALLÓ').' | '.$case, $context);
    }
}
