# Despliegue continuo

El despliegue continuo se ejecuta desde GitHub Actions con el archivo
`.github/workflows/laravel.yml`.

## Flujo automatico

Cada `push` hacia `main` ejecuta:

1. Build de assets con Vite.
2. Migraciones, seeders y pruebas automaticas con SQLite.
3. Despliegue automatico usando un deploy hook guardado como secreto.

Los `pull_request` hacia `main` ejecutan build y pruebas, pero no despliegan.

## Secreto requerido en GitHub

En el repositorio de GitHub se debe configurar:

```text
DEPLOY_HOOK_URL=
```

Ruta sugerida:

```text
Settings > Secrets and variables > Actions > New repository secret
```

El valor debe ser la URL privada del deploy hook de la plataforma cloud
utilizada. No debe agregarse en archivos del repositorio.


