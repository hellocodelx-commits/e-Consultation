<?php

use App\Http\Controllers\AdminActivityController;
use App\Http\Controllers\AdminAppointmentController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PatientAppointmentController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\StaffAppointmentController;
use App\Http\Controllers\StaffAvailabilityController;
use App\Http\Controllers\StaffDashboardController;
use App\Http\Controllers\StaffProfileController;
use App\Models\User;
use Illuminate\Support\Facades\Route;

Route::view('/', 'home')->name('home');
Route::get('/professionals', [PublicController::class, 'professionals'])->name('professionals.index');
Route::get('/professionals/{staff}', [PublicController::class, 'showProfessional'])->name('professionals.show');
Route::get('/support', [PublicController::class, 'support'])->name('support');

Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,1');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

Route::middleware(['auth', 'role:'.User::ROLE_PATIENT])->prefix('my')->name('patient.')->group(function () {
    Route::get('/appointments', [PatientAppointmentController::class, 'index'])->name('appointments.index');
    Route::post('/appointments/book/{slot}', [PatientAppointmentController::class, 'store'])->name('appointments.store');
    Route::get('/appointments/{appointment}', [PatientAppointmentController::class, 'show'])->name('appointments.show');
    Route::post('/appointments/{appointment}/cancel', [PatientAppointmentController::class, 'cancel'])->name('appointments.cancel');
});

Route::middleware(['auth', 'role:'.User::ROLE_STAFF])->prefix('staff')->name('staff.')->group(function () {
    Route::get('/dashboard', [StaffDashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [StaffProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [StaffProfileController::class, 'update'])->name('profile.update');
    Route::get('/availability', [StaffAvailabilityController::class, 'index'])->name('availability.index');
    Route::post('/availability', [StaffAvailabilityController::class, 'store'])->name('availability.store');
    Route::delete('/availability/{slot}', [StaffAvailabilityController::class, 'destroy'])->name('availability.destroy');
    Route::get('/appointments', [StaffAppointmentController::class, 'index'])->name('appointments.index');
    Route::get('/appointments/{appointment}', [StaffAppointmentController::class, 'show'])->name('appointments.show');
    Route::patch('/appointments/{appointment}/status', [StaffAppointmentController::class, 'updateStatus'])->name('appointments.status');
    Route::put('/appointments/{appointment}/consultation', [StaffAppointmentController::class, 'saveConsultation'])->name('appointments.consultation');
});

Route::middleware(['auth', 'role:'.User::ROLE_ADMIN])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [AdminUserController::class, 'create'])->name('users.create');
    Route::post('/users', [AdminUserController::class, 'store'])->name('users.store');
    Route::patch('/users/{user}/role', [AdminUserController::class, 'updateRole'])->name('users.role');
    Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');
    Route::get('/appointments', [AdminAppointmentController::class, 'index'])->name('appointments.index');
    Route::get('/activity', [AdminActivityController::class, 'index'])->name('activity.index');
});
