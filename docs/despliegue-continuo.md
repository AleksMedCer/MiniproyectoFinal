# Despliegue continuo

El despliegue continuo se ejecuta desde GitHub Actions con
`.github/workflows/laravel.yml`.

## Flujo automatico

Cada `push` hacia `main` ejecuta:

1. Build de assets con Vite.
2. Migraciones, seeders y pruebas automaticas con SQLite.
3. Despliegue automatico usando un deploy hook guardado como secreto.

Los `pull_request` hacia `main` ejecutan build y pruebas, pero no despliegan.

## GitHub Actions

En el repositorio de GitHub se debe configurar el secreto:

```text
DEPLOY_HOOK_URL=
LARAVEL_CLOUD_API_TOKEN=
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_FROM_ADDRESS=
MAIL_FROM_NAME=
```

Ruta:

```text
Settings > Secrets and variables > Actions > New repository secret
```

En GitHub, el campo `Name` lleva el nombre del secreto y el campo `Secret`
lleva solo el valor. Por ejemplo, para `LARAVEL_CLOUD_API_TOKEN` no pegar
`LARAVEL_CLOUD_API_TOKEN=...` ni `Bearer ...`; pegar solamente el token.

El valor es la URL privada del deploy hook de Laravel Cloud. No debe agregarse
en archivos del repositorio.

`LARAVEL_CLOUD_API_TOKEN` es un token de API de Laravel Cloud. El workflow lo
usa para copiar los secretos `MAIL_*` hacia las variables de entorno de Laravel
Cloud antes de disparar el deploy hook.

`LARAVEL_CLOUD_ENVIRONMENT_ID` es opcional. Si no existe, el workflow intenta
detectar el environment automaticamente usando el repositorio conectado en
Laravel Cloud.

`MAIL_FROM_ADDRESS` puede usar el mismo correo que `MAIL_USERNAME`.
`MAIL_FROM_NAME` puede ser `MarketPro`.

## Laravel Cloud

Las variables se configuran directamente en Laravel Cloud, no en `.env`.

Variables minimas:

```text
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

MAIL_MAILER=smtp
MAIL_SCHEME=null
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_FROM_ADDRESS=
MAIL_FROM_NAME=MarketPro
```

`APP_KEY` se genera con:

```bash
php artisan key:generate --show
```

Si se rota la contrasena de la base de datos, tambien debe actualizarse
`DB_PASSWORD` en Laravel Cloud.

Para Gmail, `MAIL_PASSWORD` debe ser una contrasena de aplicacion, no la
contrasena normal de la cuenta. Se crea desde la seguridad de la cuenta de
Google con verificacion en dos pasos activada.

## Build command

Usar este comando en Laravel Cloud:

```bash
bash scripts/laravel-cloud-build.sh
```

El script ejecuta:

```bash
composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist
npm ci
npm run build
```

## Deploy command

Usar este comando en Laravel Cloud:

```bash
bash scripts/laravel-cloud-deploy.sh
```

El script ejecuta migraciones y seeders sobre la base de datos configurada en
Laravel Cloud:

```bash
php artisan config:clear
php artisan migrate --force
php artisan db:seed --force
php artisan config:cache
```

Si los datos demo no aparecen, ejecutar un redeploy despues de revisar que
`DB_DATABASE`, `DB_HOST`, `DB_USERNAME` y `DB_PASSWORD` esten configuradas en
Laravel Cloud.

Para probar el envio de correo desde Laravel Cloud:

```bash
php artisan mail:diagnose
php artisan mail:test medcer94@gmail.com
```

Si ese comando falla, revisar `MAIL_USERNAME`, `MAIL_PASSWORD`,
`MAIL_FROM_ADDRESS` y que Gmail permita la contrasena de aplicacion.
Un error SMTP `530 Authentication Required` normalmente indica que Laravel
Cloud no esta usando credenciales SMTP al momento de enviar; primero ejecutar
`php artisan mail:diagnose` y confirmar que `MAIL_USERNAME` y `MAIL_PASSWORD`
salgan como `configurado`.

## Seguridad

No se debe subir:

```text
.env
.env.*
claves privadas
credenciales de base de datos
tokens
```

El repositorio solo incluye `.env.example` como plantilla sin credenciales.
