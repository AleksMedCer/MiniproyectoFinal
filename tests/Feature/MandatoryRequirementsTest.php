<?php

namespace Tests\Feature;

use App\Models\Categoria;
use App\Models\Producto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MandatoryRequirementsTest extends TestCase
{
    use RefreshDatabase;

    public function test_pagina_principal_responde_correctamente()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('MarketPro');
        $response->assertSee('Ver catálogo');
    }

    public function test_pagina_login_responde_correctamente()
    {
        $response = $this->get('/entrar');

        $response->assertStatus(200);
        $response->assertSee('Iniciar sesión');
        $response->assertSee('Correo electrónico');
        $response->assertSee('Contraseña');
    }

    public function test_dashboard_requiere_autenticacion()
    {
        $response = $this->get('/dashboard');

        $response->assertRedirect('/entrar');
        $this->assertGuest();
    }

    public function test_login_incorrecto_muestra_error()
    {
        $response = $this->from('/entrar')->post('/entrar', [
            'email' => 'correo@incorrecto.com',
            'password' => 'incorrecto'
        ]);

        $response->assertRedirect('/entrar');
        $response->assertSessionHasErrors(['email']);
        $this->assertGuest();
    }

    public function test_registro_almacenado_en_base_de_datos()
    {
        Storage::fake('public');

        $vendedor = User::factory()->create(['role' => User::ROLE_VENDEDOR]);
        $categoria = Categoria::factory()->create();

        $foto = UploadedFile::fake()->image('producto.jpg');

        $response = $this->actingAs($vendedor)->post('/productos', [
            'nombre' => 'Teclado Mecanico',
            'descripcion' => 'Un teclado mecanico excelente para programar.',
            'precio' => 1500,
            'categoria_id' => $categoria->id,
            'fotos' => [$foto]
        ]);

        $response->assertRedirect(route('productos.index'));
        $this->assertDatabaseHas('productos', [
            'nombre' => 'Teclado Mecanico',
            'precio' => 1500,
            'categoria_id' => $categoria->id,
            'vendedor_id' => $vendedor->id,
        ]);

        $producto = Producto::where('nombre', 'Teclado Mecanico')->first();

        $this->assertNotNull($producto);
        Storage::disk('public')->assertExists($producto->fotos[0]);
    }

    public function test_usuario_autenticado_puede_acceder()
    {
        $user = User::factory()->create(['role' => User::ROLE_GERENTE]);

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Dashboard Operativo');
        $this->assertAuthenticatedAs($user);
    }
}
