# KFacil Inmobiliaria - Puesta en marcha local

Guia breve para ejecutar la aplicacion en entorno local.

## Requisitos

- PHP 8.2+
- Composer
- Node.js 20+ y npm
- MySQL 8+

## Instalacion rapida

Desde la raiz del proyecto ejecuta:

```bash
composer setup
php artisan db:seed
```

El comando `composer setup` instala dependencias, crea `.env`, genera `APP_KEY`, ejecuta migraciones y compila assets.

## Arranque en desarrollo

Opcion 1 (todo en un comando):

```bash
composer dev
```

Opcion 2 (manual, en terminales separadas):

```bash
php artisan serve
npm run dev
```

La app quedara disponible en `http://127.0.0.1:8000`.

## Usuario de prueba (admin)

Si ejecutaste `php artisan db:seed`, se crea un usuario administrador:

- Email: `admin@kfacil.test`
- Password: `password123`

## Comandos utiles

```bash
php artisan test          # Ejecutar pruebas
php artisan migrate:fresh --seed
```

## Configuracion MySQL (paso a paso)

1. Crea una base de datos, por ejemplo: `kfacil`.
2. Configura estas variables en `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=kfacil
DB_USERNAME=tu_usuario
DB_PASSWORD=tu_password
```

3. Ejecuta migraciones y datos iniciales:

```bash
php artisan migrate
php artisan db:seed
```

## Notas

- En este entorno local, la aplicacion esta configurada con MySQL (`DB_CONNECTION=mysql`).
- El archivo `.env.example` del proyecto viene con SQLite como referencia inicial.
- Verifica y ajusta tus variables de base de datos en `.env` antes de ejecutar migraciones o seeders.
