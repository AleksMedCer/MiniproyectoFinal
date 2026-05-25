<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProductoFactory extends Factory
{
    private static array $productos = [
        ['Mochila Fjallraven Foldsack No. 1', 'Tu mochila perfecta para el uso diario y caminatas en el bosque. Guarda tu laptop de 15 pulgadas con total seguridad y comodidad.', 1099, 'https://fakestoreapi.com/img/81fPKd-2AYL._AC_SL1500_.jpg'],
        ['Playera Premium Hombre Algodón', 'Playera ajustada para hombre, ideal para ropa casual o de calle. Ajuste cómodo y tela transpirable para cualquier ocasión.', 229, 'https://fakestoreapi.com/img/71-3HjGNDUL._AC_SY879._SX._UX._SY._UY_.jpg'],
        ['Chamarra de Algodón Casual', 'Estupenda prenda de abrigo para primavera y otoño. Diseño clásico de algodón resistente para múltiples ocasiones al aire libre.', 559, 'https://fakestoreapi.com/img/71li-ujtlHZ._AC_UX679_.jpg'],
        ['Pulsera de Oro y Plata Mujer', 'Pulsera de cadena legendaria diseñada con hermosos detalles. Un regalo perfecto de joyería fina para aniversarios.', 6950, 'https://fakestoreapi.com/img/71pWzhdJNwL._AC_UL640_QL65_ML3_.jpg'],
        ['Anillo de Oro Blanco Princesa', 'Anillo clásico de oro blanco, creado para ella. Una pieza deslumbrante que captura la luz con su diseño de corte princesa.', 1499, 'https://fakestoreapi.com/img/71YAIFU48IL._AC_UL640_QL65_ML3_.jpg'],
        ['Disco Duro SSD SanDisk 1TB', 'El disco duro interno SSD ofrece un rendimiento mejorado, carga rápida de aplicaciones y fiabilidad inigualable para tu PC.', 1290, 'https://fakestoreapi.com/img/61U7T1koQqL._AC_SX679_.jpg'],
        ['Disco Duro Externo WD 2TB', 'Compatibilidad con USB 3.0 y USB 2.0. Transferencias de datos súper rápidas. Mejora el almacenamiento de tu consola o PC.', 990, 'https://fakestoreapi.com/img/61IBBVJvSDL._AC_SY879_.jpg'],
        ['Monitor Gaming Curvo Acer 21.5"', 'Monitor Full HD con tiempo de respuesta de 4ms. Diseño Zero Frame, colores vibrantes e inmersión total para gaming.', 2899, 'https://fakestoreapi.com/img/81QpkIctqPL._AC_SX679_.jpg'],
        ['Monitor Samsung Curvo 49" QLED', 'Monitor ultra ancho de 144Hz. Panel avanzado con tecnología Quantum Dot y soporte multitarea para la mejor experiencia visual.', 14999, 'https://fakestoreapi.com/img/81Zt42ioCgL._AC_SX679_.jpg'],
        ['Chamarra de Invierno Mujer 3 en 1', 'Chamarra cálida y acolchada de invierno con gorro extraíble, bolsillos laterales y material resistente al agua y viento.', 899, 'https://fakestoreapi.com/img/51Y5NI-I5jL._AC_UX679_.jpg'],
        ['Chamarra de Cuero Motociclista', 'Chamarra de cuero sintético con detalles de cierre, perfecta para uso casual, estilo urbano o motociclismo.', 1299, 'https://fakestoreapi.com/img/81XH0e8fefL._AC_UY879_.jpg'],
        ['Camisa Manga Corta Slim Fit', 'Tela transpirable, ligera, muy cómoda de llevar. Diseño elegante con botones, perfecta para la oficina o salidas casuales.', 349, 'https://fakestoreapi.com/img/71HblAHs5xL._AC_UY879_-2.jpg'],
    ];

    public function definition(): array
    {
        $producto = $this->faker->randomElement(self::$productos);

        return [
            // No le pasamos vendedor_id ni categoria_id aquí, se los daremos en el Seeder
            'nombre' => $producto[0],
            'descripcion' => $producto[1],
            'precio' => $producto[2],
            'fotos' => [$producto[3]],
        ];
    }
}
