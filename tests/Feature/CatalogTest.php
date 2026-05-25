<?php

namespace Tests\Feature;

use App\Models\Categoria;
use App\Models\Producto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\WithoutErrorHandler;
use Tests\TestCase;

class CatalogTest extends TestCase
{
    use RefreshDatabase;

    #[WithoutErrorHandler]
    public function test_catalog_renders_products_and_cart_action(): void
    {
        $vendedor = User::factory()->create([
            'role' => User::ROLE_VENDEDOR,
        ]);

        $categoria = Categoria::create([
            'nombre' => 'Computo',
            'descripcion' => 'Productos de computo.',
        ]);

        Producto::create([
            'categoria_id' => $categoria->id,
            'vendedor_id' => $vendedor->id,
            'nombre' => 'Laptop Demo Pro',
            'descripcion' => 'Equipo de prueba para validar el catalogo.',
            'precio' => 12999,
            'fotos' => ['https://loremflickr.com/900/700/laptop?lock=5001'],
        ]);

        $response = $this->get('/compras?tab=mas-vendidos&orden=precio-menor');

        $response->assertOk();
        $response->assertSee('Laptop Demo Pro');
        $response->assertSee('Agregar al carrito');
    }

    public function test_todas_muestra_todos_los_productos_de_la_tienda(): void
    {
        $vendedor = User::factory()->create([
            'role' => User::ROLE_VENDEDOR,
        ]);

        $computo = Categoria::create([
            'nombre' => 'Computo',
            'descripcion' => 'Productos de computo.',
        ]);

        $gaming = Categoria::create([
            'nombre' => 'Gaming',
            'descripcion' => 'Productos gamer.',
        ]);

        foreach (range(1, 15) as $index) {
            Producto::create([
                'categoria_id' => $index % 2 === 0 ? $computo->id : $gaming->id,
                'vendedor_id' => $vendedor->id,
                'nombre' => sprintf('Producto Visible %02d', $index),
                'descripcion' => 'Producto creado para validar que todas muestra el catalogo completo.',
                'precio' => 100 + $index,
                'fotos' => ['https://loremflickr.com/900/700/product?lock='.$index],
            ]);
        }

        $response = $this->get('/compras?categoria=todos&tab=todos');

        $response->assertOk();
        $response->assertSee('15 resultados');

        foreach (range(1, 15) as $index) {
            $response->assertSee(sprintf('Producto Visible %02d', $index));
        }
    }
}
