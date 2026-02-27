# Guía de estudio: cómo está hecha la app web + API REST (Laravel)

## 1) Resumen ejecutivo del proyecto

Este proyecto implementa un sistema de gestión para clínica veterinaria con **dos interfaces sobre la misma lógica de negocio**:

- **Web MVC (Blade + sesión)**: para usuarios y admin en navegador.
- **API REST (JSON + Sanctum)**: para clientes externos (Postman, front separado, mobile, etc.).

Ambas capas comparten:

- Los mismos **modelos Eloquent** (`User`, `Pet`, `Appointment`, `VaccinationRecord`)
- La misma **base de datos**
- Reglas de negocio similares (propiedad del recurso, validaciones, estados)

---

## 2) Estructura clave del proyecto

## Raíz
- `routes/`: define rutas web, api y consola (scheduler/commands)
- `app/`: controladores, modelos, políticas, jobs, servicios, notificaciones
- `database/migrations/`: esquema de base de datos
- `database/seeders/`: datos de ejemplo
- `resources/views/`: vistas Blade
- `config/`: configuración del framework y servicios
- `.env`: variables de entorno de runtime

## `app/` (lo más importante)

- `Http/Controllers/` → controladores web y de API
- `Models/` → entidades de dominio con relaciones
- `Http/Requests/` → validación desacoplada para mascotas
- `Enums/PetStatus.php` → enum tipado de estado de mascota
- `Policies/PetPolicy.php` → autorización por dueño de mascota
- `Jobs/CleanTemporaryUploadsJob.php` → limpieza de temporales por cola
- `Mail/AppointmentCreatedMail.php` → email de confirmación de turno
- `Notifications/*` → verificación de email y reset password
- `Console/Commands/ViewLogsCommand.php` → comando artisan custom para logs

---

## 3) Arquitectura técnica y flujo

## Flujo Web (session guard)
1. Usuario inicia sesión (`LoginController@authenticate`).
2. Laravel guarda sesión (`sessions` table con `SESSION_DRIVER=database`).
3. Rutas protegidas por middleware `auth`.
4. Controladores web leen/escriben en modelos Eloquent.
5. Render de Blade en `resources/views/*`.

## Flujo API (token guard con Sanctum)
1. Cliente llama `POST /api/v1/auth/login` con email/password.
2. `AuthController` genera token (`createToken`).
3. Cliente usa `Authorization: Bearer <token>`.
4. Rutas API protegidas por `auth:sanctum`.
5. Respuestas JSON consistentes con manejo custom de excepciones en `bootstrap/app.php`.

## Importante para explicar al profesor
La **web no consume internamente la API** en este repo; son dos entrypoints distintos (web y api) que comparten dominio y base de datos.

---

## 4) Rutas y endpoints

## 4.1 Web (`routes/web.php`)

### Públicas
- `GET /` → `DashboardController@index`
- `GET /endpoints` → vista de referencia de endpoints
- `GET /login` → `UserDashboardController@login`
- `POST /login` → `LoginController@authenticate`
- `GET /logout` → `LoginController@logout`

### Autenticadas (`middleware: auth`)

#### Dashboard usuario
- `GET /dashboard` → `UserDashboardController@index`

#### Mascotas (Resource)
- `Route::resource('pets', PetController::class)`
  - `index, create, store, show, edit, update, destroy`

#### Citas
- `GET /appointments` → `AppointmentController@index`
- `GET /appointments/create` → `AppointmentController@create`
- `POST /appointments` → `AppointmentController@store`
- `GET /appointments/{appointment}` → `AppointmentController@show`
- `GET /appointments/{appointment}/edit` → `AppointmentController@edit`
- `PUT /appointments/{appointment}` → `AppointmentController@update`
- `PATCH /appointments/{appointment}/cancel` → `AppointmentController@cancel`

#### Vacunación
- `GET /pets/{pet}/vaccinations` → `VaccinationController@index`
- `GET /pets/{pet}/vaccinations/create` → `VaccinationController@create`
- `POST /pets/{pet}/vaccinations` → `VaccinationController@store`
- `DELETE /pets/{pet}/vaccinations/{record}` → `VaccinationController@delete`
- `GET /pets/{pet}/vaccinations/{record}/certificate` → `VaccinationController@downloadCertificate`

### Admin (`middleware: auth + role:admin`)
Prefijo `/admin`:
- `GET /admin/appointments` → `Admin\AppointmentController@index`
- `GET /admin/appointments/create` → `Admin\AppointmentController@create`
- `POST /admin/appointments` → `Admin\AppointmentController@store`
- `PATCH /admin/appointments/{appointment}/status` → `Admin\AppointmentController@updateStatus`

## 4.2 API (`routes/api.php`)

### Público
- `GET /api/ping` (health de API)
- `POST /api/v1/auth/login`

### Protegido (`auth:sanctum`)
- `POST /api/v1/auth/logout`
- `GET /api/v1/auth/me`
- `Route::apiResource('pets', Api\PetController::class)`
  - `index, store, show, update, destroy`
- `Route::apiResource('appointments', Api\AppointmentController::class)`
  - `index, store, show, update, destroy`
- Vacunación por mascota:
  - `GET /api/v1/pets/{pet}/vaccinations`
  - `POST /api/v1/pets/{pet}/vaccinations`
  - `DELETE /api/v1/pets/{pet}/vaccinations/{record}`

---

## 5) Controladores: qué existe y para qué sirve

## 5.1 Web controllers

- `DashboardController`
  - Landing pública (`/`).

- `LoginController`
  - Login por sesión (`Auth::attempt`) y logout.

- `UserDashboardController`
  - Dashboard con métricas.
  - Si usuario tiene rol `admin`, muestra estadísticas globales.
  - Si no, muestra solo su data (mascotas, citas, vacunas próximas).

- `PetController`
  - CRUD de mascotas del usuario autenticado.
  - Usa `StorePetRequest` y `UpdatePetRequest`.
  - Impone ownership (`$pet->user_id === Auth::id()`).

- `AppointmentController`
  - CRUD de citas de usuario.
  - Auto-completa estados vencidos (`Appointment::autoCompleteDueAppointments`).
  - Envía mail de confirmación al crear.

- `VaccinationController`
  - CRUD de registros de vacunación por mascota.
  - Calcula resumen (`total`, `due_soon`, `expired`).

- `Admin\AppointmentController`
  - Gestión de turnos a nivel admin.
  - Crea cliente + mascota + cita en un solo flujo.
  - Cambia estado de citas.

## 5.2 API controllers (`app/Http/Controllers/Api`)

- `AuthController`
  - `login`, `logout`, `me` con Sanctum.

- `PetController`
  - CRUD JSON de mascotas del usuario token-auth.

- `AppointmentController`
  - CRUD JSON de citas con validación de ownership de mascota/cita.

- `VaccinationController`
  - Lista/crea/elimina registros de vacunación por mascota.

---

## 6) Modelos y relaciones Eloquent

## Modelos principales
- `User`
- `Pet`
- `Appointment`
- `VaccinationRecord`

## Relaciones

### `User`
- `hasMany(Pet::class)` → un usuario tiene muchas mascotas.
- `hasMany(Appointment::class)` → un usuario tiene muchas citas.

### `Pet`
- `belongsTo(User::class)` → cada mascota pertenece a un usuario.
- `hasMany(Appointment::class)` → una mascota tiene muchas citas.
- `hasMany(VaccinationRecord::class)` → una mascota tiene muchos registros.

### `Appointment`
- `belongsTo(User::class)`
- `belongsTo(Pet::class)`

### `VaccinationRecord`
- `belongsTo(Pet::class)`

## Cardinalidades (para exposición)
- `users 1 ── N pets`
- `users 1 ── N appointments`
- `pets 1 ── N appointments`
- `pets 1 ── N vaccination_records`

---

## 7) Base de datos completa: tablas, columnas y claves

A continuación, las tablas relevantes del dominio y de infraestructura Laravel.

## 7.1 Dominio funcional

### `users`
- `id` (PK)
- `name`
- `email` (UNIQUE)
- `email_verified_at`
- `profile_image` (nullable, migración adicional)
- `password`
- `remember_token`
- `created_at`, `updated_at`

### `pets`
- `id` (PK)
- `name`
- `species`
- `breed`
- `age` (unsigned int)
- `description` (nullable)
- `status` (string; default enum `PetStatus::Available`)
- `user_id` (FK → `users.id`, `cascadeOnDelete`)
- `created_at`, `updated_at`

### `appointments`
- `id` (PK)
- `user_id` (FK → `users.id`, cascade)
- `pet_id` (FK → `pets.id`, cascade)
- `appointment_date` (datetime)
- `type` (enum: `consultation|vaccination|surgery|grooming|other`)
- `description` (nullable)
- `status` (enum: `pending|confirmed|completed|cancelled`)
- `notes` (nullable)
- `created_at`, `updated_at`

### `vaccination_records`
- `id` (PK)
- `pet_id` (FK → `pets.id`, cascade)
- `vaccine_name`
- `application_date` (date)
- `next_due_date` (nullable)
- `veterinarian` (nullable)
- `notes` (nullable)
- `created_at`, `updated_at`

## 7.2 Seguridad / auth / API

### `personal_access_tokens` (Sanctum)
- `id` (PK)
- `tokenable_type`, `tokenable_id` (morph)
- `name`
- `token` (UNIQUE)
- `abilities`, `last_used_at`, `expires_at`
- `created_at`, `updated_at`

### `password_reset_tokens`
- `email` (PK)
- `token`
- `created_at`

### `sessions`
- `id` (PK)
- `user_id` (index)
- `ip_address`, `user_agent`, `payload`, `last_activity`

## 7.3 Roles y permisos (Spatie)

Tablas creadas por paquete:
- `roles`
- `permissions`
- `model_has_roles`
- `model_has_permissions`
- `role_has_permissions`

## 7.4 Infraestructura Laravel

- `jobs`, `job_batches`, `failed_jobs` (colas)
- `cache`, `cache_locks`
- `temporary_uploads` (subidas temporales, con expiración)

---

## 8) Filas (datos iniciales) con seeders

Se cargan desde `DatabaseSeeder` en este orden:
1. `RoleSeeder`
2. `UserSeeder`
3. `PetSeeder`
4. `AppointmentSeeder`
5. `VaccinationRecordSeeder`

## Qué filas genera (resumen)
- Roles base: `user`, `verified`, `admin` + permisos.
- Usuarios:
  - 1 admin desde `.env` (`ADMIN_*`)
  - 1 user desde `.env` (`USER_*`)
  - 107 users fake adicionales (rol `user`)
- Mascotas demo para un user con rol `user`.
- Citas demo con estados mixtos (pending/confirmed/completed/cancelled).
- Registros de vacunación demo (vigentes, próximas y vencidas).

---

## 9) Variables `.env` importantes para que funcione todo

Basado en `.env.example`.

## Núcleo app
- `APP_NAME`, `APP_ENV`, `APP_KEY`, `APP_DEBUG`, `APP_URL`
- `APP_LOCALE`, `APP_FALLBACK_LOCALE`, `APP_FAKER_LOCALE`

## Usuarios por defecto (seed)
- `ADMIN_FIRST_NAME`, `ADMIN_LAST_NAME`, `ADMIN_EMAIL`, `ADMIN_PASSWORD`
- `USER_FIRST_NAME`, `USER_LAST_NAME`, `USER_EMAIL`, `USER_PASSWORD`

## Base de datos
- `DB_CONNECTION` (ejemplo actual: `sqlite`)
- si MySQL: `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`

## Sesión, cache, colas
- `SESSION_DRIVER=database`
- `CACHE_STORE=database`
- `QUEUE_CONNECTION=database`

## Mail (clave para emails de citas/verificación/reset)
- `MAIL_MAILER`, `MAIL_HOST`, `MAIL_PORT`
- `MAIL_USERNAME`, `MAIL_PASSWORD`
- `MAIL_FROM_ADDRESS`, `MAIL_FROM_NAME`

## Seguridad / auth
- `AUTH_VERIFICATION_EXPIRE` (minutos de expiración para verificación)
- `SANCTUM_STATEFUL_DOMAINS` (si se usa SPA stateful)

## Octane / RoadRunner
- `OCTANE_SERVER=roadrunner`

---

## 10) Sintaxis Laravel importante (con ejemplos)

## 10.1 Rutas

```php
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('pets', PetController::class);
});
```

## 10.2 Validación

```php
$validated = $request->validate([
    'appointment_date' => ['required', 'date', 'after:now'],
    'type' => ['required', 'in:consultation,vaccination,surgery,grooming,other'],
]);
```

## 10.3 Relaciones Eloquent

```php
public function pets(): HasMany
{
    return $this->hasMany(Pet::class);
}
```

## 10.4 Crear token Sanctum

```php
$token = $user->createToken('api_token')->plainTextToken;
```

## 10.5 Control de autorización por ownership

```php
if ($pet->user_id !== $request->user()->id) {
    abort(403, 'No autorizado');
}
```

## 10.6 Enum en cast de modelo

```php
protected $casts = [
    'status' => PetStatus::class,
];
```

## 10.7 Programación de jobs

```php
Schedule::job(new CleanTemporaryUploadsJob)->hourly();
```

---

## 11) Comandos artisan y comandos de proyecto útiles

## Setup / DB

```bash
php artisan key:generate
php artisan migrate:fresh --seed
php artisan db:seed
```

## Desarrollo

```bash
php artisan serve
php artisan route:list
php artisan optimize:clear
php artisan queue:work --queue=default,emails
php artisan octane:start --watch --host=0.0.0.0 --port=8000
```

## Testing

```bash
php artisan test
composer test
```

## Comando custom

```bash
php artisan logs:view --lines=100
php artisan logs:view --tail
php artisan logs:view --clear
```

## Scripts del repo

```bash
./start.sh
./migrate.sh
```

---

## 12) ¿Cómo se conecta la web con la API?

Hay **dos canales**:

1. **Canal Web interno**
   - Browser → rutas web (`web.php`) → controladores web → modelos Eloquent.
   - Auth por sesión (`auth`).

2. **Canal API externo**
   - Cliente HTTP → rutas API (`api.php`) → controladores API → modelos Eloquent.
   - Auth por token (`auth:sanctum`).

Conclusión técnica: no hay un BFF intermedio ni un front SPA consumiendo API dentro del mismo código; son dos interfaces paralelas sobre el mismo dominio.

---

## 13) Decisiones de diseño para justificar “qué se hizo y por qué”

- **Separación Web/API**: permite atender navegador tradicional y clientes externos sin duplicar modelos.
- **Sanctum**: autenticación API simple y segura para tokens personales.
- **Spatie Permission**: roles/permisos robustos sin reinventar RBAC.
- **Form Requests en mascotas**: validación limpia y reutilizable (`StorePetRequest`, `UpdatePetRequest`).
- **Enum `PetStatus`**: evita strings mágicos y reduce errores de estado.
- **Jobs + scheduler**: tareas de mantenimiento (limpieza temporales) fuera del request web.
- **Seeders realistas**: facilitan demo y evaluación académica con dataset funcional.
- **Mails transaccionales**: confirmación de turnos y flujos de cuenta (verificación/reset).

---

## 14) Observaciones que conviene mencionar en la defensa

- En la vista de endpoints se muestra `POST /logout`, pero en `web.php` actualmente está definido `GET /logout`.
- `RoleSeeder` corta si hay más de 2 roles (`if (Role::count() > 2) return;`), útil para evitar duplicados.
- `Appointment::autoCompleteDueAppointments()` centraliza la regla de negocio de turnos vencidos.
- `bootstrap/app.php` personaliza errores JSON de API (validación, auth, autorización, 404 model not found).

---

## 15) Guion corto de exposición (2-3 minutos)

1. “Construimos un sistema veterinario en Laravel con doble entrada: Web MVC y API REST.”
2. “Ambas usan los mismos modelos Eloquent (`User`, `Pet`, `Appointment`, `VaccinationRecord`) y la misma BD.”
3. “La web usa sesión (`auth`), la API usa tokens Sanctum (`auth:sanctum`).”
4. “Definimos relaciones 1:N entre usuario-mascotas, usuario-citas, mascota-citas y mascota-vacunas.”
5. “Implementamos roles con Spatie (`user/admin`) y rutas admin protegidas con middleware `role:admin`.”
6. “Automatizamos datos de demo con seeders y tareas de mantenimiento con colas + scheduler.”
7. “Así logramos una app demostrable, escalable y preparada para integración externa por API.”

---

## 16) Checklist para demo en vivo

1. `cp .env.example .env`
2. Configurar `APP_KEY` y credenciales de mail/DB
3. `php artisan key:generate`
4. `php artisan migrate:fresh --seed`
5. `php artisan queue:work --queue=default,emails`
6. `php artisan serve` (u octane con `start.sh`)
7. Login web con usuario seed
8. Probar `/endpoints`
9. Probar login API y llamadas con Bearer token

---

Documento preparado para estudio y defensa técnica del proyecto.
