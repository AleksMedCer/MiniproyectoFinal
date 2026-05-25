<?php

namespace Database\Seeders;

use App\Models\Categoria;
use App\Models\Producto;
use App\Models\User;
use App\Models\Venta;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $telefonoDemo = env('DEMO_PHONE', '9617017722');

        User::factory()->create([
            'name' => 'Admin Master',
            'email' => 'admin@netehis.com',
            'phone' => $telefonoDemo,
            'password' => bcrypt('password'),
            'role' => User::ROLE_ADMIN,
        ]);

        User::factory()->create([
            'name' => 'Gerente Ventas',
            'email' => 'gerente@netehis.com',
            'phone' => $telefonoDemo,
            'password' => bcrypt('password'),
            'role' => User::ROLE_GERENTE,
        ]);

        User::factory()->create([
            'name' => 'Comprador Demo',
            'email' => 'comprador@netehis.com',
            'phone' => $telefonoDemo,
            'password' => bcrypt('password'),
            'role' => User::ROLE_COMPRADOR,
        ]);

        User::factory(69)->create([
            'role' => User::ROLE_COMPRADOR,
        ]);

        $vendedores = collect([
            User::factory()->create([
                'name' => 'TecnoNorte MX',
                'email' => 'vendedor@netehis.com',
                'phone' => $telefonoDemo,
                'password' => bcrypt('password'),
                'role' => User::ROLE_VENDEDOR,
            ]),
        ])->merge($this->crearTiendasDemo($telefonoDemo));

        $categorias = collect($this->categoriasDemo())
            ->mapWithKeys(fn (array $categoria) => [
                $categoria['nombre'] => Categoria::create($categoria),
            ]);

        foreach ($this->catalogoProductos() as $indice => $producto) {
            Producto::create([
                'categoria_id' => $categorias[$producto['categoria']]->id,
                'vendedor_id' => $vendedores->values()->get($indice % $vendedores->count())->id,
                'nombre' => $producto['nombre'],
                'descripcion' => $producto['descripcion'],
                'precio' => $producto['precio'],
                'fotos' => [
                    $this->imagenProducto($producto['nombre'].' '.$producto['imagen']),
                ],
            ]);
        }

        $productos = Producto::all();
        $compradores = User::where('role', User::ROLE_COMPRADOR)->get();

        Storage::disk('local')->put('tickets/demo-dashboard.txt', "Ticket demo para ventas sembradas.\n");

        for ($i = 0; $i < 110; $i++) {
            $fecha = now()->subDays(rand(0, 20))->subHours(rand(0, 23))->subMinutes(rand(0, 59));

            Venta::create([
                'producto_id' => $productos->random()->id,
                'comprador_id' => $compradores->random()->id,
                'ticket_path' => 'tickets/demo-dashboard.txt',
                'validada' => rand(1, 100) <= 74,
                'created_at' => $fecha,
                'updated_at' => $fecha,
            ]);
        }
    }

    private function crearTiendasDemo(string $telefonoDemo)
    {
        return collect([
            ['name' => 'ElectroHogar Tuxtla', 'email' => 'ventas@electrohogar.mx'],
            ['name' => 'UrbanTrend Store', 'email' => 'hola@urbantrend.mx'],
            ['name' => 'GamerHub Pro', 'email' => 'ventas@gamerhub.mx'],
            ['name' => 'CasaNova Market', 'email' => 'contacto@casanova.mx'],
            ['name' => 'FitLab Mexico', 'email' => 'equipo@fitlab.mx'],
            ['name' => 'Belleza Viva Shop', 'email' => 'ventas@bellezaviva.mx'],
            ['name' => 'AudioMax Center', 'email' => 'contacto@audiomax.mx'],
            ['name' => 'CompuPlus Express', 'email' => 'ventas@compuplus.mx'],
            ['name' => 'SmartCell Outlet', 'email' => 'soporte@smartcell.mx'],
            ['name' => 'Kira Home Design', 'email' => 'hola@kirahome.mx'],
            ['name' => 'RutaAuto Store', 'email' => 'ventas@rutaauto.mx'],
            ['name' => 'Sportiva MX', 'email' => 'tienda@sportiva.mx'],
            ['name' => 'Oficina Total', 'email' => 'ventas@oficinatotal.mx'],
            ['name' => 'Cocina Select', 'email' => 'contacto@cocinaselect.mx'],
            ['name' => 'TravelGear Shop', 'email' => 'ventas@travelgear.mx'],
            ['name' => 'Lumiere Beauty', 'email' => 'hola@lumierebeauty.mx'],
            ['name' => 'ClickTech Depot', 'email' => 'ventas@clicktech.mx'],
            ['name' => 'Familia Plus Market', 'email' => 'contacto@familiaplus.mx'],
            ['name' => 'GreenLife Wellness', 'email' => 'ventas@greenlife.mx'],
            ['name' => 'Zapateria Central', 'email' => 'hola@zapateriacentral.mx'],
            ['name' => 'MegaGadgets MX', 'email' => 'ventas@megagadgets.mx'],
            ['name' => 'Home Studio Shop', 'email' => 'contacto@homestudio.mx'],
            ['name' => 'Moda Clara Boutique', 'email' => 'ventas@modaclara.mx'],
            ['name' => 'PowerTools Mexico', 'email' => 'ventas@powertools.mx'],
            ['name' => 'BikeRunner Store', 'email' => 'contacto@bikerunner.mx'],
            ['name' => 'Cafe Barista MX', 'email' => 'ventas@cafebarista.mx'],
            ['name' => 'Arena Gamer Store', 'email' => 'hola@arenagamer.mx'],
            ['name' => 'Decoralia Home', 'email' => 'ventas@decoralia.mx'],
            ['name' => 'Outlet Premium MX', 'email' => 'contacto@outletpremium.mx'],
        ])->map(fn (array $tienda) => User::factory()->create([
            'name' => $tienda['name'],
            'email' => $tienda['email'],
            'phone' => $telefonoDemo,
            'password' => bcrypt('password'),
            'role' => User::ROLE_VENDEDOR,
        ]));
    }

    private function categoriasDemo(): array
    {
        return [
            ['nombre' => 'Electronica', 'descripcion' => 'Audio, video, seguridad y gadgets para uso diario.'],
            ['nombre' => 'Computo', 'descripcion' => 'Laptops, monitores, redes, almacenamiento y perifericos.'],
            ['nombre' => 'Celulares', 'descripcion' => 'Smartphones, cargadores, fundas y accesorios moviles.'],
            ['nombre' => 'Gaming', 'descripcion' => 'Consolas, controles, accesorios y equipo gamer.'],
            ['nombre' => 'Hogar', 'descripcion' => 'Muebles, iluminacion, limpieza y organizacion.'],
            ['nombre' => 'Cocina', 'descripcion' => 'Electrodomesticos, utensilios y productos para preparar alimentos.'],
            ['nombre' => 'Moda', 'descripcion' => 'Ropa, calzado, bolsas, relojes y accesorios.'],
            ['nombre' => 'Belleza', 'descripcion' => 'Cuidado personal, skincare, fragancias y maquillaje.'],
            ['nombre' => 'Deportes', 'descripcion' => 'Equipo deportivo, entrenamiento y actividades al aire libre.'],
            ['nombre' => 'Oficina', 'descripcion' => 'Escritorios, sillas, papeleria y productividad.'],
            ['nombre' => 'Automotriz', 'descripcion' => 'Accesorios, limpieza, seguridad y herramientas para auto.'],
            ['nombre' => 'Herramientas', 'descripcion' => 'Herramientas electricas, manuales y organizadores.'],
        ];
    }

    private function catalogoProductos(): array
    {
        return [
            ['categoria' => 'Electronica', 'nombre' => 'Sony WH-CH720N Audifonos Bluetooth', 'descripcion' => 'Audifonos inalambricos con cancelacion de ruido, bateria de larga duracion y sonido nitido para llamadas.', 'precio' => 2299, 'imagen' => 'wireless headphones'],
            ['categoria' => 'Electronica', 'nombre' => 'JBL Flip 6 Bocina Portatil', 'descripcion' => 'Bocina resistente al agua con sonido potente, graves profundos y bateria para llevar a cualquier reunion.', 'precio' => 2399, 'imagen' => 'portable speaker'],
            ['categoria' => 'Electronica', 'nombre' => 'Echo Dot 5 Generacion con Alexa', 'descripcion' => 'Asistente inteligente compacto para controlar musica, recordatorios y dispositivos compatibles del hogar.', 'precio' => 1099, 'imagen' => 'smart speaker'],
            ['categoria' => 'Electronica', 'nombre' => 'TP-Link Tapo C200 Camara WiFi', 'descripcion' => 'Camara de seguridad con movimiento 360, vision nocturna y monitoreo desde el celular.', 'precio' => 649, 'imagen' => 'security camera'],
            ['categoria' => 'Electronica', 'nombre' => 'Samsung HW-C400 Barra de Sonido', 'descripcion' => 'Barra compacta con sonido envolvente, bluetooth y modo de dialogo claro para series y peliculas.', 'precio' => 2999, 'imagen' => 'soundbar'],
            ['categoria' => 'Electronica', 'nombre' => 'Anker PowerCore 20000 mAh', 'descripcion' => 'Bateria externa de alta capacidad con carga rapida para celular, audifonos y accesorios.', 'precio' => 1299, 'imagen' => 'power bank'],
            ['categoria' => 'Electronica', 'nombre' => 'Fifine Studio Microfono USB', 'descripcion' => 'Microfono condensador para streaming, clases y reuniones con soporte estable y filtro antipop.', 'precio' => 899, 'imagen' => 'usb microphone'],
            ['categoria' => 'Electronica', 'nombre' => 'Wanbo Mini Proyector Full HD', 'descripcion' => 'Proyector compacto para cine en casa con entrada HDMI, enfoque sencillo y buena luminosidad.', 'precio' => 3999, 'imagen' => 'mini projector'],

            ['categoria' => 'Computo', 'nombre' => 'Lenovo IdeaPad Slim 3 15', 'descripcion' => 'Laptop ligera con pantalla Full HD, procesador moderno y almacenamiento SSD para trabajo y escuela.', 'precio' => 11999, 'imagen' => 'laptop computer'],
            ['categoria' => 'Computo', 'nombre' => 'MacBook Air M2 13 Renew', 'descripcion' => 'Equipo reacondicionado certificado con chip M2, bateria duradera y acabado premium.', 'precio' => 18999, 'imagen' => 'macbook laptop'],
            ['categoria' => 'Computo', 'nombre' => 'LG UltraWide Monitor 29 Pulgadas', 'descripcion' => 'Monitor panoramico para productividad, edicion y multitarea con panel IPS y alta definicion.', 'precio' => 5499, 'imagen' => 'ultrawide monitor'],
            ['categoria' => 'Computo', 'nombre' => 'Logitech MX Mechanical Mini', 'descripcion' => 'Teclado mecanico compacto, silencioso y recargable para escribir comodo durante horas.', 'precio' => 2799, 'imagen' => 'mechanical keyboard'],
            ['categoria' => 'Computo', 'nombre' => 'Logitech M650 Mouse Inalambrico', 'descripcion' => 'Mouse ergonomico con clic silencioso, desplazamiento preciso y conexion bluetooth.', 'precio' => 699, 'imagen' => 'wireless mouse'],
            ['categoria' => 'Computo', 'nombre' => 'Kingston NV2 SSD 1TB M.2', 'descripcion' => 'Unidad de estado solido NVMe para acelerar arranque, juegos y programas pesados.', 'precio' => 1199, 'imagen' => 'ssd drive'],
            ['categoria' => 'Computo', 'nombre' => 'Epson EcoTank L3250 Multifuncional', 'descripcion' => 'Impresora con tanque de tinta, WiFi, escaner y costo bajo por pagina.', 'precio' => 4199, 'imagen' => 'printer'],
            ['categoria' => 'Computo', 'nombre' => 'TP-Link Archer AX55 Router WiFi 6', 'descripcion' => 'Router de alta velocidad con mayor cobertura, baja latencia y soporte para multiples equipos.', 'precio' => 1999, 'imagen' => 'wifi router'],

            ['categoria' => 'Celulares', 'nombre' => 'iPhone 13 128GB Renew', 'descripcion' => 'Smartphone reacondicionado con pantalla Super Retina, doble camara y rendimiento fluido.', 'precio' => 11999, 'imagen' => 'iphone smartphone'],
            ['categoria' => 'Celulares', 'nombre' => 'Samsung Galaxy A55 5G', 'descripcion' => 'Celular con pantalla AMOLED, camara triple, bateria de larga duracion y conectividad 5G.', 'precio' => 8499, 'imagen' => 'samsung phone'],
            ['categoria' => 'Celulares', 'nombre' => 'Xiaomi Redmi Note 13 Pro', 'descripcion' => 'Equipo con carga rapida, pantalla de alta tasa de refresco y camara de alta resolucion.', 'precio' => 6999, 'imagen' => 'xiaomi smartphone'],
            ['categoria' => 'Celulares', 'nombre' => 'Motorola Edge 40 Neo', 'descripcion' => 'Smartphone ligero con gran pantalla, carga TurboPower y fotografia nocturna mejorada.', 'precio' => 7299, 'imagen' => 'motorola phone'],
            ['categoria' => 'Celulares', 'nombre' => 'Belkin Cargador USB-C 30W', 'descripcion' => 'Cargador compacto de pared para carga rapida en celulares, tablets y accesorios USB-C.', 'precio' => 499, 'imagen' => 'usb c charger'],
            ['categoria' => 'Celulares', 'nombre' => 'Funda MagSafe Transparente Pro', 'descripcion' => 'Funda transparente con anillo magnetico, esquinas reforzadas y ajuste preciso.', 'precio' => 349, 'imagen' => 'phone case'],
            ['categoria' => 'Celulares', 'nombre' => 'Samsung Galaxy Buds FE', 'descripcion' => 'Audifonos compactos con sonido claro, cancelacion activa y estuche de carga.', 'precio' => 1599, 'imagen' => 'earbuds'],
            ['categoria' => 'Celulares', 'nombre' => 'Spigen MagFit Soporte para Auto', 'descripcion' => 'Soporte magnetico firme para tablero con giro ajustable y montaje rapido.', 'precio' => 599, 'imagen' => 'car phone holder'],

            ['categoria' => 'Gaming', 'nombre' => 'PlayStation 5 Slim Console', 'descripcion' => 'Consola de nueva generacion con unidad de almacenamiento rapida y control DualSense.', 'precio' => 9999, 'imagen' => 'playstation console'],
            ['categoria' => 'Gaming', 'nombre' => 'Xbox Wireless Controller Carbon Black', 'descripcion' => 'Control inalambrico compatible con consola y PC, agarre texturizado y boton compartir.', 'precio' => 1499, 'imagen' => 'xbox controller'],
            ['categoria' => 'Gaming', 'nombre' => 'Nintendo Switch OLED Neon', 'descripcion' => 'Consola hibrida con pantalla OLED, base mejorada y controles Joy-Con neon.', 'precio' => 7499, 'imagen' => 'nintendo switch'],
            ['categoria' => 'Gaming', 'nombre' => 'Cougar Armor One Silla Gamer', 'descripcion' => 'Silla reclinable con soporte lumbar, reposabrazos ajustables y estructura resistente.', 'precio' => 4499, 'imagen' => 'gaming chair'],
            ['categoria' => 'Gaming', 'nombre' => 'HyperX Cloud Stinger 2 Headset', 'descripcion' => 'Audifonos gamer ligeros con microfono abatible y audio espacial para sesiones largas.', 'precio' => 1299, 'imagen' => 'gaming headset'],
            ['categoria' => 'Gaming', 'nombre' => 'Razer DeathAdder Essential Mouse', 'descripcion' => 'Mouse gamer ergonomico con sensor preciso, botones programables e iluminacion verde.', 'precio' => 799, 'imagen' => 'gaming mouse'],
            ['categoria' => 'Gaming', 'nombre' => 'Tarjeta Regalo Steam 500 MXN', 'descripcion' => 'Codigo digital para comprar juegos, expansiones y contenido dentro de Steam.', 'precio' => 500, 'imagen' => 'gift card'],
            ['categoria' => 'Gaming', 'nombre' => 'AOC Monitor Gamer 27 165Hz', 'descripcion' => 'Monitor rapido con baja latencia, frecuencia alta y diseno ideal para eSports.', 'precio' => 5299, 'imagen' => 'gaming monitor'],

            ['categoria' => 'Hogar', 'nombre' => 'Xiaomi Robot Vacuum Mop 2', 'descripcion' => 'Aspiradora robot con mapeo inteligente, modo trapeador y control desde app movil.', 'precio' => 5299, 'imagen' => 'robot vacuum'],
            ['categoria' => 'Hogar', 'nombre' => 'Levoit Core Purificador de Aire', 'descripcion' => 'Purificador con filtro HEPA para habitaciones medianas, silencioso y facil de mantener.', 'precio' => 2499, 'imagen' => 'air purifier'],
            ['categoria' => 'Hogar', 'nombre' => 'Luuna Colchon Individual Memory Foam', 'descripcion' => 'Colchon de espuma adaptable con soporte firme, funda lavable y descanso fresco.', 'precio' => 3899, 'imagen' => 'mattress'],
            ['categoria' => 'Hogar', 'nombre' => 'Cotton House Set de Toallas Premium', 'descripcion' => 'Juego de toallas suaves y absorbentes para bano, fabricadas en algodon de alto gramaje.', 'precio' => 899, 'imagen' => 'bath towels'],
            ['categoria' => 'Hogar', 'nombre' => 'Philips Hue Go Lampara LED', 'descripcion' => 'Lampara inteligente con colores personalizables, bateria integrada y control por app.', 'precio' => 1799, 'imagen' => 'led lamp'],
            ['categoria' => 'Hogar', 'nombre' => 'HomeBox Organizadores de Closet', 'descripcion' => 'Set de cajas plegables para ropa, zapatos y accesorios con ventana frontal.', 'precio' => 649, 'imagen' => 'closet organizer'],
            ['categoria' => 'Hogar', 'nombre' => 'Honeywell Ventilador Torre Fresh', 'descripcion' => 'Ventilador de torre con oscilacion, control remoto y tres velocidades.', 'precio' => 1699, 'imagen' => 'tower fan'],
            ['categoria' => 'Hogar', 'nombre' => 'Nordic Home Mesa Auxiliar Roble', 'descripcion' => 'Mesa lateral minimalista para sala o recamara con acabado tipo madera natural.', 'precio' => 1299, 'imagen' => 'side table'],

            ['categoria' => 'Cocina', 'nombre' => 'Ninja Air Fryer 4QT Freidora de Aire', 'descripcion' => 'Freidora de aire para preparar alimentos crujientes con menos aceite y limpieza sencilla.', 'precio' => 2999, 'imagen' => 'air fryer'],
            ['categoria' => 'Cocina', 'nombre' => 'Nespresso Essenza Mini Cafetera', 'descripcion' => 'Cafetera compacta de capsulas con calentamiento rapido y extraccion consistente.', 'precio' => 2699, 'imagen' => 'espresso machine'],
            ['categoria' => 'Cocina', 'nombre' => 'Ninja Professional Licuadora 1000W', 'descripcion' => 'Licuadora potente para smoothies, hielo y salsas con vaso de gran capacidad.', 'precio' => 2499, 'imagen' => 'blender'],
            ['categoria' => 'Cocina', 'nombre' => 'T-fal Bateria de Cocina 12 Piezas', 'descripcion' => 'Juego antiadherente con tapas de vidrio, mangos comodos y distribucion uniforme de calor.', 'precio' => 2799, 'imagen' => 'cookware set'],
            ['categoria' => 'Cocina', 'nombre' => 'Stanley Quencher Termo 887 ml', 'descripcion' => 'Vaso termico con popote, asa comoda y conservacion de temperatura por horas.', 'precio' => 999, 'imagen' => 'stainless tumbler'],
            ['categoria' => 'Cocina', 'nombre' => 'Oster Horno Electrico 45L', 'descripcion' => 'Horno de sobremesa para hornear, gratinar y calentar con temporizador y charola.', 'precio' => 2299, 'imagen' => 'toaster oven'],
            ['categoria' => 'Cocina', 'nombre' => 'KitchenAid Bascula Digital Precision', 'descripcion' => 'Bascula para cocina con medicion exacta, pantalla clara y funcion tara.', 'precio' => 549, 'imagen' => 'kitchen scale'],
            ['categoria' => 'Cocina', 'nombre' => 'Tramontina Set de Cuchillos Pro', 'descripcion' => 'Set de cuchillos con bloque, acero inoxidable y mangos ergonomicos para uso diario.', 'precio' => 1199, 'imagen' => 'knife set'],

            ['categoria' => 'Moda', 'nombre' => 'Nike Revolution 7 Tenis Hombre', 'descripcion' => 'Tenis ligeros para caminar, entrenar y uso casual con suela flexible y ajuste comodo.', 'precio' => 1699, 'imagen' => 'running shoes'],
            ['categoria' => 'Moda', 'nombre' => 'Adidas Grand Court Tenis Mujer', 'descripcion' => 'Calzado casual con estilo clasico, plantilla confortable y exterior resistente.', 'precio' => 1599, 'imagen' => 'white sneakers'],
            ['categoria' => 'Moda', 'nombre' => 'Herschel Retreat Mochila 23L', 'descripcion' => 'Mochila urbana con compartimiento para laptop, cierre superior y correas acolchadas.', 'precio' => 1899, 'imagen' => 'backpack'],
            ['categoria' => 'Moda', 'nombre' => 'Casio Vintage A168 Reloj Digital', 'descripcion' => 'Reloj retro con cronometro, alarma, luz LED y correa de acero inoxidable.', 'precio' => 799, 'imagen' => 'digital watch'],
            ['categoria' => 'Moda', 'nombre' => 'Levi\'s Trucker Chamarra Denim', 'descripcion' => 'Chamarra de mezclilla con corte clasico, botones metalicos y bolsillos frontales.', 'precio' => 2199, 'imagen' => 'denim jacket'],
            ['categoria' => 'Moda', 'nombre' => 'Guess Noelle Bolsa Tote', 'descripcion' => 'Bolsa tote espaciosa con textura elegante, asas firmes y compartimentos interiores.', 'precio' => 2499, 'imagen' => 'tote bag'],
            ['categoria' => 'Moda', 'nombre' => 'Polo Ralph Lauren Playera Slim', 'descripcion' => 'Playera tipo polo de algodon con corte moderno, cuello acanalado y logo bordado.', 'precio' => 1499, 'imagen' => 'polo shirt'],
            ['categoria' => 'Moda', 'nombre' => 'Ray-Ban Erika Lentes de Sol', 'descripcion' => 'Lentes ligeros con armazon redondo, proteccion UV y estilo versatil para diario.', 'precio' => 2299, 'imagen' => 'sunglasses'],

            ['categoria' => 'Belleza', 'nombre' => 'Dyson Airwrap Multi-Styler', 'descripcion' => 'Moldeador premium para rizar, alisar y secar con control inteligente de temperatura.', 'precio' => 13999, 'imagen' => 'hair styler'],
            ['categoria' => 'Belleza', 'nombre' => 'CeraVe Rutina Hidratante Dia', 'descripcion' => 'Kit de limpieza e hidratacion con ceramidas para piel normal a seca.', 'precio' => 899, 'imagen' => 'skincare products'],
            ['categoria' => 'Belleza', 'nombre' => 'Carolina Herrera 212 VIP 80ml', 'descripcion' => 'Fragancia de noche con notas dulces, elegantes y duraderas para ocasiones especiales.', 'precio' => 2499, 'imagen' => 'perfume bottle'],
            ['categoria' => 'Belleza', 'nombre' => 'Philips Series 5000 Rasuradora', 'descripcion' => 'Rasuradora electrica recargable con cabezales flexibles y uso en seco o mojado.', 'precio' => 1999, 'imagen' => 'electric shaver'],
            ['categoria' => 'Belleza', 'nombre' => 'Revlon One-Step Secadora Voluminizadora', 'descripcion' => 'Cepillo secador que ayuda a dar volumen, brillo y peinado rapido en casa.', 'precio' => 1099, 'imagen' => 'hair dryer'],
            ['categoria' => 'Belleza', 'nombre' => 'Maybelline Fit Me Kit Maquillaje', 'descripcion' => 'Set de maquillaje con base, corrector y polvo para acabado natural.', 'precio' => 699, 'imagen' => 'makeup kit'],
            ['categoria' => 'Belleza', 'nombre' => 'The Ordinary Niacinamide Serum', 'descripcion' => 'Serum facial para textura, brillo y cuidado de poros con formula ligera.', 'precio' => 399, 'imagen' => 'serum skincare'],
            ['categoria' => 'Belleza', 'nombre' => 'Foreo Luna Mini Cepillo Facial', 'descripcion' => 'Dispositivo de limpieza facial con pulsaciones suaves y silicona higienica.', 'precio' => 2499, 'imagen' => 'facial cleansing brush'],

            ['categoria' => 'Deportes', 'nombre' => 'Mercurio Ranger Bicicleta 29', 'descripcion' => 'Bicicleta de montana con cuadro resistente, cambios multiples y rodada 29.', 'precio' => 6999, 'imagen' => 'mountain bike'],
            ['categoria' => 'Deportes', 'nombre' => 'UrbanFit Caminadora Plegable', 'descripcion' => 'Caminadora compacta para casa con pantalla, velocidad ajustable y plegado sencillo.', 'precio' => 8999, 'imagen' => 'treadmill'],
            ['categoria' => 'Deportes', 'nombre' => 'Mancuernas Ajustables 20 kg Par', 'descripcion' => 'Par de mancuernas con discos intercambiables para rutinas de fuerza en casa.', 'precio' => 1899, 'imagen' => 'dumbbells'],
            ['categoria' => 'Deportes', 'nombre' => 'Manduka PROlite Tapete Yoga', 'descripcion' => 'Tapete antiderrapante con buena amortiguacion, superficie estable y facil limpieza.', 'precio' => 1699, 'imagen' => 'yoga mat'],
            ['categoria' => 'Deportes', 'nombre' => 'Adidas Al Rihla Balon League', 'descripcion' => 'Balon de entrenamiento con cubierta resistente, buen bote y diseno profesional.', 'precio' => 799, 'imagen' => 'soccer ball'],
            ['categoria' => 'Deportes', 'nombre' => 'Nike HyperFuel Termo Deportivo', 'descripcion' => 'Botella deportiva con boquilla rapida, agarre comodo y capacidad de 946 ml.', 'precio' => 399, 'imagen' => 'sports bottle'],
            ['categoria' => 'Deportes', 'nombre' => 'Garmin Forerunner 55 GPS', 'descripcion' => 'Reloj deportivo con GPS, metricas de carrera, bateria amplia y planes de entrenamiento.', 'precio' => 3999, 'imagen' => 'gps watch'],
            ['categoria' => 'Deportes', 'nombre' => 'Wilson Federer Team Raqueta', 'descripcion' => 'Raqueta ligera para tenis recreativo con marco estable y agarre confortable.', 'precio' => 1599, 'imagen' => 'tennis racket'],

            ['categoria' => 'Oficina', 'nombre' => 'ErgoPro Silla Ejecutiva Mesh', 'descripcion' => 'Silla ergonomica con soporte lumbar, respaldo de malla y altura ajustable.', 'precio' => 3499, 'imagen' => 'office chair'],
            ['categoria' => 'Oficina', 'nombre' => 'FlexiSpot Escritorio Elevable', 'descripcion' => 'Escritorio ajustable electrico para trabajar de pie o sentado con memoria de altura.', 'precio' => 6499, 'imagen' => 'standing desk'],
            ['categoria' => 'Oficina', 'nombre' => 'Arzopa Monitor Portatil 15.6', 'descripcion' => 'Pantalla portatil Full HD para laptop, consolas o productividad en movimiento.', 'precio' => 3299, 'imagen' => 'portable monitor'],
            ['categoria' => 'Oficina', 'nombre' => 'Moleskine Classic Set Libretas', 'descripcion' => 'Set de libretas de tapa dura con papel suave, cinta marcadora y bolsillo interior.', 'precio' => 699, 'imagen' => 'notebook stationery'],
            ['categoria' => 'Oficina', 'nombre' => 'Logitech MK470 Kit Teclado Mouse', 'descripcion' => 'Combo inalambrico delgado y silencioso para oficina, home office y estudio.', 'precio' => 1099, 'imagen' => 'keyboard mouse'],
            ['categoria' => 'Oficina', 'nombre' => 'Baseus Lampara de Escritorio LED', 'descripcion' => 'Lampara con brillo regulable, brazo flexible y luz suave para lectura o trabajo.', 'precio' => 749, 'imagen' => 'desk lamp'],
            ['categoria' => 'Oficina', 'nombre' => 'Fellowes Powershred Trituradora', 'descripcion' => 'Trituradora de papel para oficina con corte cruzado y papelera de buena capacidad.', 'precio' => 2199, 'imagen' => 'paper shredder'],
            ['categoria' => 'Oficina', 'nombre' => 'Casio FX-991 Calculadora Cientifica', 'descripcion' => 'Calculadora cientifica con funciones avanzadas para ingenieria, escuela y oficina.', 'precio' => 599, 'imagen' => 'calculator'],

            ['categoria' => 'Automotriz', 'nombre' => 'Garmin Dash Cam Mini 2', 'descripcion' => 'Camara compacta para auto con grabacion Full HD, control por voz y conexion WiFi.', 'precio' => 2699, 'imagen' => 'dash cam'],
            ['categoria' => 'Automotriz', 'nombre' => 'NOCO Boost Arrancador Portatil', 'descripcion' => 'Arrancador de bateria para auto con linterna, proteccion de polaridad y puerto USB.', 'precio' => 2899, 'imagen' => 'jump starter'],
            ['categoria' => 'Automotriz', 'nombre' => 'Black+Decker Aspiradora para Auto', 'descripcion' => 'Aspiradora compacta con cable largo, boquilla estrecha y contenedor lavable.', 'precio' => 899, 'imagen' => 'car vacuum'],
            ['categoria' => 'Automotriz', 'nombre' => 'Sparco Cubreasientos Universal', 'descripcion' => 'Juego de cubreasientos resistentes con estilo deportivo y ajuste universal.', 'precio' => 999, 'imagen' => 'car seat cover'],
            ['categoria' => 'Automotriz', 'nombre' => 'Meguiar\'s Gold Kit Limpieza Auto', 'descripcion' => 'Kit de limpieza con shampoo, cera, microfibras y productos para acabado brillante.', 'precio' => 1199, 'imagen' => 'car cleaning kit'],
            ['categoria' => 'Automotriz', 'nombre' => 'Xiaomi Portable Electric Air Compressor', 'descripcion' => 'Compresor portatil para llantas con pantalla digital, bateria integrada y apagado automatico.', 'precio' => 1199, 'imagen' => 'air compressor'],

            ['categoria' => 'Herramientas', 'nombre' => 'DeWalt Taladro 20V Max', 'descripcion' => 'Taladro inalambrico con bateria recargable, luz LED y torque para trabajos en casa.', 'precio' => 3299, 'imagen' => 'cordless drill'],
            ['categoria' => 'Herramientas', 'nombre' => 'Stanley Set Herramientas 65 Piezas', 'descripcion' => 'Kit completo con dados, llaves, desarmadores y estuche rigido para organizar.', 'precio' => 1599, 'imagen' => 'tool set'],
            ['categoria' => 'Herramientas', 'nombre' => 'Klein Tools Multimetro Digital', 'descripcion' => 'Multimetro para medicion electrica con pantalla clara, puntas de prueba y carcasa resistente.', 'precio' => 1199, 'imagen' => 'multimeter'],
            ['categoria' => 'Herramientas', 'nombre' => 'Truper Pro Caja Organizadora', 'descripcion' => 'Caja robusta con charola interna, broches resistentes y espacio para accesorios.', 'precio' => 649, 'imagen' => 'tool box'],
        ];
    }

    private function imagenProducto(string $busqueda): string
    {
        $query = rawurlencode($busqueda.' product image');

        return "https://tse1.mm.bing.net/th?q={$query}&w=900&h=700&c=7&rs=1&p=0&o=5&pid=1.7";
    }
}
