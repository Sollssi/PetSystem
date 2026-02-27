# VetClinic Manager 🏥🐾

Sistema de gestión para clínicas veterinarias desarrollado con Laravel 11 y PHP 8.3+.

## 🎯 Características

### Sistema de Citas Veterinarias 📅
- Creación de citas (consulta, vacunación, cirugía, peluquería)
- Estados de citas (pendiente, confirmada, completada, cancelada)
- Vista de calendario e historial completo
- Notas y descripciones detalladas

### Carnet de Vacunación Digital 💉
- Registro de vacunas aplicadas
- Alertas de vacunas vencidas o próximas
- Información del veterinario responsable
- Descarga del carnet

### Gestión de Mascotas 🐕🐈
- Registro completo de mascotas
- Historial médico
- Estados: disponible, adoptado, en tratamiento
- Integración con citas y vacunas

## 🚀 Instalación Rápida

### Requisitos
- PHP 8.3+
- Composer
- MySQL o SQLite

### Pasos
1. Instalar dependencias
```bash
composer install
```

2. Configurar variables de entorno
```bash
cp .env.example .env
php artisan key:generate
```

3. Crear base de datos y cargar datos de prueba
```bash
php artisan migrate:fresh --seed
```

4. Iniciar servidor de desarrollo
```bash
php artisan serve
```

Accede a: http://localhost:8000

## 🔐 Credenciales de Prueba

Después de ejecutar los seeders:

**Usuario Regular:**
- Email: user@email.com
- Contraseña: password

**Administrador:**
- Email: admin@email.com
- Contraseña: password

## 🧪 Testing

```bash
php artisan test tests/Feature
```

## 📄 Licencia

MIT
