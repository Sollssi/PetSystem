<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PetController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserDashboardController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\VaccinationController;
use App\Http\Controllers\Admin\AppointmentController as AdminAppointmentController;

// Rutas públicas
Route::get('/', [DashboardController::class, 'index'])->name('home');
Route::view('/endpoints', 'endpoints.index')->name('endpoints.index');

Route::middleware(['guest'])->group(function () {
    Route::get('/login', [UserDashboardController::class, 'login'])->name('login');
    Route::post('/login', [LoginController::class, 'authenticate'])->name('authenticate');
    Route::get('/register', [RegisterController::class, 'create'])->name('register');
    Route::post('/register', [RegisterController::class, 'store'])->name('register.store');
});

Route::get('/logout', [LoginController::class, 'logout'])->name('logout');

// Rutas autenticadas (clientes)
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('user.dashboard');

    // Citas
    Route::get('/appointments', [AppointmentController::class, 'index'])->name('appointments.index');
    Route::get('/appointments/create', [AppointmentController::class, 'create'])->name('appointments.create');
    Route::post('/appointments', [AppointmentController::class, 'store'])->name('appointments.store');
    Route::get('/appointments/{appointment}', [AppointmentController::class, 'show'])->name('appointments.show');
    Route::get('/appointments/{appointment}/edit', [AppointmentController::class, 'edit'])->name('appointments.edit');
    Route::put('/appointments/{appointment}', [AppointmentController::class, 'update'])->name('appointments.update');
    Route::patch('/appointments/{appointment}/cancel', [AppointmentController::class, 'cancel'])->name('appointments.cancel');

    // Vacunación
    Route::get('/pets/{pet}/vaccinations', [VaccinationController::class, 'index'])->name('vaccinations.index');
    Route::get('/pets/{pet}/vaccinations/create', [VaccinationController::class, 'create'])->name('vaccinations.create');
    Route::post('/pets/{pet}/vaccinations', [VaccinationController::class, 'store'])->name('vaccinations.store');
    Route::delete('/pets/{pet}/vaccinations/{record}', [VaccinationController::class, 'delete'])->name('vaccinations.delete');
    Route::get('/pets/{pet}/vaccinations/{record}/certificate', [VaccinationController::class, 'downloadCertificate'])->name('vaccinations.certificate');

    // Mascotas
    Route::resource('pets', PetController::class);
});

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/appointments', [AdminAppointmentController::class, 'index'])->name('appointments.index');
    Route::get('/appointments/create', [AdminAppointmentController::class, 'create'])->name('appointments.create');
    Route::post('/appointments', [AdminAppointmentController::class, 'store'])->name('appointments.store');
    Route::patch('/appointments/{appointment}/status', [AdminAppointmentController::class, 'updateStatus'])->name('appointments.status');
});
