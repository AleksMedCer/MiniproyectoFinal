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

    public function test_dashboard_muestra_usuarios_sembrados()
    {
        $this->seed();

        $admin = User::where('email', 'admin@netehis.com')->firstOrFail();

        $response = $this->actingAs($admin)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Usuarios registrados');
        $response->assertSee('medcer94@gmail.com');
        $response->assertSee('gerente@netehis.com');
        $response->assertSee('vendedor@netehis.com');
        $response->assertSee('comprador.demo.01@netehis.com');
    }

    public function test_comando_demo_seed_users_asegura_usuarios_principales()
    {
        $this->artisan('demo:seed-users')
            ->expectsOutput('Usuarios demo asegurados.')
            ->assertExitCode(0);

        $this->assertDatabaseHas('users', [
            'email' => 'medcer94@gmail.com',
            'role' => User::ROLE_ADMIN,
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'vendedor@netehis.com',
            'role' => User::ROLE_VENDEDOR,
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'comprador.demo.01@netehis.com',
            'role' => User::ROLE_COMPRADOR,
        ]);
    }
}
