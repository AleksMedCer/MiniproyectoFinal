# MarketPro

## Descripcion del Proyecto

MarketPro es un marketplace desarrollado en Laravel. Incluye catalogo publico, carrito de compras, registro e inicio de sesion con verificacion 2FA por correo, roles de usuario, gestion de productos, tickets de compra, validacion de ventas y dashboard administrativo con reportes.

## Tecnologias Usadas

- Laravel 11
- PHP 8.2+
- SQLite para desarrollo local
- MySQL o PostgreSQL para despliegue en la nube
- Blade
- Tailwind CSS
- Vite
- GitHub Actions
- PHPUnit

## Instalacion Local

1. Clonar el repositorio:

```bash
git clone https://github.com/AleksMedCer/MiniproyectoFinal
cd miniproyecto3
```

2. Instalar dependencias:

```bash
composer install
npm install
```

3. Crear archivo de entorno:

```bash
cp .env.example .env
php artisan key:generate
```

4. Configurar base de datos local en `.env`. Para SQLite:

```env
DB_CONNECTION=sqlite
```

5. Crear la base SQLite si no existe:

```bash
touch database/database.sqlite
```

6. Ejecutar migraciones y seeders:

```bash
php artisan migrate:fresh --seed
```

7. Crear enlace de almacenamiento:

```bash
php artisan storage:link
```

8. Ejecutar el servidor local:

```bash
php artisan serve --host=127.0.0.1 --port=8000
```

Abrir:

```text
http://127.0.0.1:8000
```

## Usuarios Demo

Todos usan la contraseña:

```text
password
```

```text
Admin: admin@netehis.com
Gerente: gerente@netehis.com
Vendedor: vendedor@netehis.com
Comprador: comprador@netehis.com
```

El codigo 2FA se envia al correo del usuario. En local se puede usar SMTP real o `MAIL_MAILER=log` para revisar el correo en `storage/logs/laravel.log`.

## Ejecucion de Pruebas

```bash
php artisan test
```

## Despliegue

El sistema se despliega en una plataforma cloud configurando variables de entorno desde el panel de la plataforma. El archivo `.env` no debe subirse al repositorio.

Variables minimas requeridas:

```env
APP_NAME=MarketPro
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=

DB_CONNECTION=mysql
DB_HOST=
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=
DB_PASSWORD=
```

En Laravel Cloud se recomienda usar como build command:

```bash
bash scripts/laravel-cloud-build.sh
```

Ese script instala dependencias y compila assets sin guardar credenciales en el
repositorio.

Como deploy command en Laravel Cloud:

```bash
bash scripts/laravel-cloud-deploy.sh
```

Ese script ejecuta migraciones y seeders sobre la base MySQL configurada en
Laravel Cloud, por lo que no es necesario capturar datos manualmente.

## URL Publica del Sistema

```text
Pendiente: agregar aqui la URL publica de Laravel Cloud.
```
