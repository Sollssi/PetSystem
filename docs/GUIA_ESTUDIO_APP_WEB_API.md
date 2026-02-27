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
- `Enums/AppoinmentStatus.php` → enum tipado para estados de cita (`pending`, `confirmed`, `completed`, `cancelled`)
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
- `status` (string; default `active`)
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
  'appointment_date' => 'datetime',
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
- **Enum `AppoinmentStatus`**: centraliza estados de cita y evita strings mágicos repetidos en controladores/modelo.
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

## 17) Funcionalidades por entidad (modelo) y por sistema

## Sistema general (qué hace completo)
- Gestiona cuentas de usuario con autenticación web y API.
- Permite registrar mascotas y mantener su perfil clínico básico.
- Permite crear y administrar turnos veterinarios.
- Mantiene carnet digital de vacunación por mascota.
- Aplica reglas de autorización por dueño y por rol admin.
- Emite correos transaccionales (confirmación de turno, verificación email, reset de password).

## `User` (usuario)
- Representa personas que usan el sistema (cliente o admin).
- Tiene roles/permisos con Spatie (`HasRoles`).
- Se autentica por sesión (web) y token (Sanctum API).
- Relaciona sus mascotas y sus turnos.

## `Pet` (mascota)
- Guarda identidad de la mascota: nombre, especie, raza, edad, descripción.
- Tiene un estado simple (`status` string, por defecto `active`).
- Pertenece a un usuario.
- Agrupa su historial de turnos y de vacunación.

## `Appointment` (cita)
- Registra fecha/hora, tipo, descripción, notas y estado.
- Estado tipado por enum `AppoinmentStatus` (`pending`, `confirmed`, `completed`, `cancelled`).
- Aplica regla automática: citas vencidas pendientes/confirmadas pasan a `completed`.
- Se vincula a usuario y mascota.

## `VaccinationRecord` (vacunación)
- Guarda vacuna aplicada, fecha, próxima dosis, veterinario y notas.
- Permite detectar próximas a vencer y vencidas.
- Se vincula a una mascota.

## Flujo integrado (usuario + backend)
1. Usuario se registra/inicia sesión.
2. Backend autentica (session o token) y autoriza acceso a recursos propios.
3. Usuario registra mascota.
4. Usuario crea cita para su mascota.
5. Backend valida ownership, persiste cita y dispara email de confirmación.
6. Admin visualiza turnos, confirma/cancela solicitudes.
7. Usuario/admin gestiona vacunación de cada mascota.
8. Dashboard muestra métricas y recordatorios calculados por backend.

---

## 18) Cómo se conectó y personalizó el email de confirmación (Gmail)

## Arquitectura del envío
1. En un controlador se crea la cita.
2. Luego se llama a `Mail::to(...)->send(new AppointmentCreatedMail($appointment))`.
3. `AppointmentCreatedMail` define asunto y la vista Blade del correo.
4. La plantilla `resources/views/emails/appointment-created.blade.php` renderiza contenido dinámico (mascota, fecha, estado, descripción).

## Archivos clave
- `app/Http/Controllers/AppointmentController.php` (web)
- `app/Http/Controllers/Admin/AppointmentController.php` (admin)
- `app/Mail/AppointmentCreatedMail.php`
- `resources/views/emails/appointment-created.blade.php`

## Configuración para Gmail (SMTP)
En `.env`:
- `MAIL_MAILER=smtp`
- `MAIL_HOST=smtp.gmail.com`
- `MAIL_PORT=587`
- `MAIL_USERNAME=tu_correo@gmail.com`
- `MAIL_PASSWORD=tu_app_password` (contraseña de aplicación de Google, no la contraseña normal)
- `MAIL_ENCRYPTION=tls`
- `MAIL_FROM_ADDRESS=tu_correo@gmail.com`
- `MAIL_FROM_NAME="VetClinic"`

## Personalización del mensaje
- **Asunto**: se define en `AppointmentCreatedMail::envelope()`.
- **Cuerpo HTML**: se define en la vista Blade del correo.
- **Datos dinámicos**: se pasan con el objeto `$appointment` a la vista.

## Recomendación de operación
- Si usas cola de correos (`ShouldQueue` en notificaciones/mails), ejecuta:
  - `php artisan queue:work --queue=default,emails`
- En local, para inspección visual rápida, puedes usar Mailtrap/Mailpit; para prueba real, Gmail SMTP.

---

Documento preparado para estudio y defensa técnica del proyecto.

---

## 19) Profundización (estilo clase) con ejemplos del proyecto VetClinic

Esta sección traduce conceptos típicos de Backend (migraciones, Eloquent, rutas, auth, middlewares, colas, email) a ejemplos concretos del proyecto.

## 19.1 De requerimiento funcional a implementación técnica

## Caso: “Como usuario quiero agendar una cita para mi mascota”

## Capa de datos (migración)
Se modela en `appointments` con `user_id`, `pet_id`, `appointment_date`, `type`, `status`.

## Capa de dominio (modelo)
- `Appointment` tiene relación con `User` y `Pet`.
- Regla de negocio automática: cuando la fecha ya pasó, una cita pendiente/confirmada se marca como completada.

## Capa HTTP (controlador)
- Web: `AppointmentController@store`
- API: `Api\AppointmentController@store`

## Capa de seguridad
- Validación de datos con `validate()`.
- Validación de ownership: la mascota debe pertenecer al usuario autenticado.

## Resultado
- Se guarda la cita.
- Se envía email de confirmación.
- Se devuelve vista o JSON según canal.

---

## 19.2 Eloquent en práctica (consultas típicas de examen)

## A) Relación y eager loading

```php
$appointments = $user->appointments()
    ->with('pet')
    ->orderBy('appointment_date')
    ->get();
```

Qué demuestra:
- Relación `hasMany`.
- `with()` evita problema N+1.

## B) Filtro por ownership (seguridad)

```php
if ($pet->user_id !== $request->user()->id) {
    abort(403, 'Esta mascota no te pertenece');
}
```

Qué demuestra:
- Seguridad a nivel de negocio (no solo autenticación).

## C) Actualización masiva con regla de negocio

```php
Appointment::query()
    ->whereIn('status', ['pending', 'confirmed'])
    ->where('appointment_date', '<=', now())
    ->update(['status' => 'completed']);
```

Qué demuestra:
- Regla centralizada en modelo.
- Uso eficiente de query builder.

---

## 19.3 Middleware y guards: qué protege qué

## Web
- Middleware `auth` protege vistas (`/dashboard`, `/pets`, `/appointments`).
- Guard de sesión (`web`) + tabla `sessions`.

## API
- Middleware `auth:sanctum` protege `/api/v1/*` privados.
- Token Bearer emitido por login.

## Admin
- Middleware `role:admin` (Spatie) protege `/admin/*` y `/api/users`.

Ejemplo mental de defensa:
"Autenticado" no implica "autorizado". Primero se valida identidad (auth), luego permisos/rol/propiedad (authorization).

---

## 19.4 Ejemplo real de request/response (API)

## Login

Request:

```http
POST /api/login
Content-Type: application/json

{
  "email": "user@email.com",
  "password": "password"
}
```

Response (200):

```json
{
  "status": "success",
  "message": "Authentication successful",
  "data": {
    "token": "1|...",
    "user": {
      "id": 5,
      "name": "Usuario Demo",
      "email": "user@email.com"
    }
  }
}
```

## Crear cita

```http
POST /api/v1/appointments
Authorization: Bearer 1|...
Content-Type: application/json

{
  "pet_id": 10,
  "appointment_date": "2026-03-01 10:30:00",
  "type": "consultation",
  "description": "Control general",
  "notes": "Sin ayuno"
}
```

Posibles respuestas:
- `201` creada
- `403` mascota no pertenece al usuario
- `422` validación

---

## 19.5 Flujo completo "usuario + backend" (end-to-end)

1. Usuario entra a `/register` o `/login`.
2. Backend valida credenciales/datos.
3. Se autentica usuario (session o token).
4. Usuario crea mascota (`pets.store` o `/api/v1/pets`).
5. Usuario agenda cita (`appointments.store` o `/api/v1/appointments`).
6. Backend envía email de confirmación.
7. Admin revisa solicitudes en `/admin/appointments`.
8. Admin confirma/cancela (actualiza `status`).
9. Usuario consulta dashboard y ve estado actualizado + recordatorios de vacunas.

---

## 19.6 Email de confirmación a Gmail (explicación detallada)

## Paso 1: Config SMTP en `.env`

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=tu_correo@gmail.com
MAIL_PASSWORD=tu_app_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=tu_correo@gmail.com
MAIL_FROM_NAME="VetClinic"
```

Nota: Gmail exige **App Password** (2FA activo), no la contraseña normal de la cuenta.

## Paso 2: Disparo desde controlador

En creación de cita:

```php
Mail::to($appointment->user->email)
    ->send(new AppointmentCreatedMail($appointment));
```

## Paso 3: Mailable personalizado

`AppointmentCreatedMail` define:
- asunto (envelope)
- plantilla Blade (content)
- datos para render (`$appointment`)

## Paso 4: Template HTML

`resources/views/emails/appointment-created.blade.php` usa datos dinámicos:
- nombre del usuario
- mascota
- fecha/hora
- tipo y estado
- descripción opcional

## Paso 5: Cola de correo (recomendado)

Para no bloquear el request web:

```bash
php artisan queue:work --queue=default,emails
```

---

## 19.7 Preguntas típicas de defensa y respuesta breve

## “¿Dónde está la lógica de negocio?”
- En controladores (validación de ownership), modelos (auto-complete de citas), y middleware (auth/roles).

## “¿Por qué separar Web y API?”
- Permite reutilizar dominio para clientes distintos (navegador, app móvil, integraciones) sin duplicar modelo de datos.

## “¿Cómo garantizan seguridad?”
- Auth (session/token), autorización (rol admin + ownership), validación de entrada, y respuestas de error estandarizadas para API.

## “¿Cómo escalarías esto?”
- Más policies por recurso, colas para procesos pesados, más tests de integración, versionado API (`v2`), observabilidad de errores.

---

## 20) Módulo de citas: implementación paso a paso (qué, dónde y por qué)

Esta sección documenta **exactamente** los cambios recientes para pasar de “turnos con revisión manual” a “agenda con confirmación automática y cupos”.

## 20.1 Objetivo funcional

Se buscó que:
- El usuario vea horarios disponibles y reserve directamente.
- El turno quede **confirmado al crearse** (sin aprobación admin).
- No exista doble reserva de la misma mascota en el mismo horario.
- Se limite la agenda por franja y por día.
- Admin conserve control operativo (cancelar/reactivar), pero no “aceptar pendientes”.

---

## 20.2 Paso 1 — Configuración central de agenda

## Qué se creó
- `config/appointments.php`

## Qué define
- `slot_minutes`: duración del turno (30 min)
- `slot_limit`: cupo por franja horaria
- `daily_limit`: cupo total diario
- `workday_start` / `workday_end`: ventana horaria

## Por qué
Evita hardcodear reglas en controladores y permite ajustar negocio desde configuración.

Ejemplo:

```php
return [
    'slot_minutes' => 30,
    'daily_limit' => 20,
    'slot_limit' => 2,
    'workday_start' => '09:00',
    'workday_end' => '19:00',
];
```

---

## 20.3 Paso 2 — Reglas de disponibilidad en el modelo

## Qué se modificó
- `app/Models/Appointment.php`

## Métodos agregados
- `schedulingRules()`
- `activeStatuses()`
- `hasScheduleConflict()`
- `hasCapacityForDateTime()`
- `availableSlotsForDate()`

## Por qué en el modelo
Porque son reglas de dominio reutilizables por web, admin y API.

## Resultado
- Misma lógica en todos los canales.
- Menos duplicación en controladores.

---

## 20.4 Paso 3 — Endpoints web para disponibilidad

## Qué se agregó
- Ruta: `GET /appointments/availability`
  - Archivo: `routes/web.php`
  - Método: `AppointmentController@availability`

## Qué hace
Recibe `date=YYYY-MM-DD` y devuelve JSON con:
- cupo diario
- ocupación diaria
- lista de slots (hora, cupo, disponibilidad)

## Por qué
Permite construir UI de agenda dinámica sin exponer lógica en frontend.

---

## 20.5 Paso 4 — Confirmación automática del turno

## Qué se modificó
- `app/Http/Controllers/AppointmentController.php` (web)
- `app/Http/Controllers/Api/AppointmentController.php` (API)

## Cambios clave
1. En `store`, el `status` pasa a `confirmed` al crear.
2. Antes de crear, se valida:
   - conflicto por mascota+fecha/hora
   - cupo de franja y cupo diario
3. Se normaliza `appointment_date` a segundos `00`.

## Sintaxis importante

```php
if (!Appointment::hasCapacityForDateTime($appointmentDate)) {
    throw ValidationException::withMessages([
        'appointment_date' => 'Ese horario ya no está disponible. Elegí otro turno.',
    ]);
}
```

## Por qué
- UX más simple (no esperar aceptación).
- Menos carga operativa en admin.
- Evita sobre-reserva por concurrencia.

---

## 20.6 Paso 5 — Calendario de slots en la vista web

## Qué se modificó
- `resources/views/appointments/create.blade.php`

## Qué cambió en UI
- Se reemplazó `datetime-local` libre por:
  - input `date` (día)
  - grilla de botones con horarios disponibles (slots)
  - input hidden `appointment_date` para enviar el slot elegido

## Técnica usada
- `fetch()` al endpoint de disponibilidad.
- Render dinámico de botones por horario.
- Marcado visual del slot seleccionado.

## Por qué
Reducir errores de usuario y forzar selección solo de horarios válidos.

---

## 20.7 Paso 6 — Admin sin aprobación manual

## Qué se modificó
- `app/Http/Controllers/Admin/AppointmentController.php`
- `resources/views/admin/appointments/create.blade.php`
- `resources/views/admin/appointments/index.blade.php`
- `app/Http/Controllers/UserDashboardController.php`
- `resources/views/user/dashboard.blade.php`

## Cambios funcionales
- Ya no existe “aceptar pendiente” como flujo principal.
- El alta admin queda confirmada por defecto.
- Admin puede cancelar/reactivar cuando haga falta.
- Dashboard admin ya no muestra “solicitudes pendientes de revisión”.

## Por qué
Modelo operativo de agenda directa, con administración por excepción.

---

## 20.8 Paso 7 — Integridad de datos y seeders

## Qué se modificó
- `database/migrations/2026_02_27_130000_add_unique_pet_datetime_to_appointments_table.php`
- `database/seeders/AppointmentSeeder.php`

## Cambios
- Índice único `pet_id + appointment_date`.
- Seeder con `updateOrCreate` para evitar duplicados al re-seedear.
- Ajuste de estados seed para coherencia con auto-confirmación.

## Por qué
Blindaje de integridad incluso fuera de la UI.

---

## 20.9 Web vs API: qué se tocó en cada capa

## Web
- Ruta nueva de disponibilidad.
- Controlador de citas con transiciones automáticas.
- Vista de creación con selector de slots.

## API
- Validaciones de capacidad/solapamiento.
- Estado confirmado al crear.
- Mismo contrato de reglas del dominio.

## Dominio común
- Modelo `Appointment` concentra reglas y evita divergencia.

---

## 20.10 Comandos esenciales para reproducir todo

## Inicialización

```bash
php artisan optimize:clear
php artisan migrate:fresh --seed
php artisan route:list | grep appointments
```

## Prueba rápida de disponibilidad (tinker)

```bash
php artisan tinker
>>> App\Models\Appointment::availableSlotsForDate(now()->addDay())
```

## Verificación de duplicados (pet + datetime)

```bash
php artisan tinker --execute='echo App\Models\Appointment::query()->selectRaw("pet_id, appointment_date, count(*) as total")->groupBy("pet_id","appointment_date")->having("total",">",1)->count();'
```

Si devuelve `0`, no hay duplicados.

---

## 20.11 Orden sugerido para implementarlo desde cero (examen/práctica)

1. Crear config de agenda.
2. Agregar helpers en modelo (`Appointment`).
3. Exponer endpoint de disponibilidad.
4. Cambiar `store/update` web con validaciones de cupo.
5. Cambiar `store/update` API con mismas reglas.
6. Migrar UI de fecha/hora libre a selección por slots.
7. Ajustar panel/admin y seeders.
8. Ejecutar `migrate:fresh --seed` y validar.

Ese orden minimiza regresiones y permite testear cada capa por separado.
