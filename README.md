# Mi Nube — Gestor de archivos en Laravel

Práctica de Laravel: gestor básico de archivos en la nube con dos secciones enlazadas desde la barra de navegación.

- **/photos** — Galería de imágenes: subir con título, verlas en la página, reemplazar y eliminar.
- **/docs** — Gestor de PDF: subir con título, listar, reemplazar y eliminar. Al pulsar un documento se abre en una pestaña nueva.

## Cómo está hecho

| Requisito | Dónde |
|---|---|
| Modelos `Photo` y `Doc` (solo `title` y `path`) | `app/Models`, `database/migrations/*_create_photos_table.php`, `*_create_docs_table.php` |
| Disco `public`, carpetas `photos` y `docs`, `storage:link` | `->store('photos', 'public')` / `->store('docs', 'public')` |
| Borrado del archivo antiguo al actualizar o eliminar | `Storage::disk('public')->delete(...)` en `update` y `destroy` |
| Controladores con `index`, `store`, `update`, `destroy` | `app/Http/Controllers/PhotoController.php`, `DocController.php` |
| Formularios `multipart/form-data` con `@method('PUT')` y `@method('DELETE')` | `resources/views/photos/index.blade.php`, `resources/views/docs/index.blade.php` |
| Validación: obligatorios, formato (imagen / pdf) y tamaño máximo (2 MB) | `$request->validate(...)` en `store` y `update` |
| `try / catch` en todas las funciones y mensajes de sesión `success` / `error` | Controladores + `resources/views/layouts/app.blade.php` |

Rutas (`routes/web.php`):

```php
Route::resource('photos', PhotoController::class)->only(['index', 'store', 'update', 'destroy']);
Route::resource('docs', DocController::class)->only(['index', 'store', 'update', 'destroy']);
```

## Puesta en marcha

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan storage:link
php artisan serve
```

Abrir http://localhost:8000 (redirige a `/photos`).

## Pruebas

```bash
php artisan test
```

`tests/Feature/GestorArchivosTest.php` comprueba subida, reemplazo y borrado (incluido que el archivo antiguo desaparece del disco) y la validación de formato y tamaño en las dos secciones.
