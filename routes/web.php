<?php

use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\ClinicContextController;
use App\Http\Controllers\CommissionController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DoctorWorkspaceController;
use App\Http\Controllers\NurseWorkspaceController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\PatientPortalController;
use App\Http\Controllers\SuperAdminController;
use Illuminate\Support\Facades\Route;

// Guest Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// Authenticated Application Routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Tenant Switching
    Route::post('/switch-clinic/{clinic}', [ClinicContextController::class, 'switch'])->name('clinic.switch');

    // Core Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('home');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Patients
    Route::get('/patients', [PatientController::class, 'index'])->name('patients.index');
    Route::get('/patients/create', [PatientController::class, 'create'])->name('patients.create');
    Route::post('/patients', [PatientController::class, 'store'])->name('patients.store');
    Route::get('/patients/{patient}', [PatientController::class, 'show'])->name('patients.show');

    // Appointments
    Route::get('/appointments', [AppointmentController::class, 'index'])->name('appointments.index');
    Route::get('/appointments/create', [AppointmentController::class, 'create'])->name('appointments.create');
    Route::post('/appointments', [AppointmentController::class, 'store'])->name('appointments.store');
    Route::post('/appointments/{appointment}/check-in', [AppointmentController::class, 'checkIn'])->name('appointments.check-in');
    Route::post('/appointments/{appointment}/cancel', [AppointmentController::class, 'cancel'])->name('appointments.cancel');

    // Nurse Workspace
    Route::middleware('role:nurse,admin')->prefix('nurse')->name('nurse.')->group(function () {
        Route::get('/', [NurseWorkspaceController::class, 'index'])->name('index');
        Route::get('/patients/{patient}/call', [NurseWorkspaceController::class, 'createCall'])->name('call.create');
        Route::post('/patients/{patient}/call', [NurseWorkspaceController::class, 'storeCall'])->name('call.store');
    });

    // Doctor Workspace
    Route::middleware('role:doctor,admin')->prefix('doctor')->name('doctor.')->group(function () {
        Route::get('/', [DoctorWorkspaceController::class, 'index'])->name('index');
        Route::get('/reports/{report}', [DoctorWorkspaceController::class, 'showReport'])->name('reports.show');
        Route::post('/reports/{report}/review', [DoctorWorkspaceController::class, 'submitReview'])->name('reports.review');
        Route::get('/consultations/{appointment}', [DoctorWorkspaceController::class, 'consultation'])->name('consultations.create');
        Route::post('/consultations/{appointment}', [DoctorWorkspaceController::class, 'storeConsultation'])->name('consultations.store');
    });

    // Commission Engine
    Route::middleware('role:admin,accountant,doctor,nurse')->prefix('commissions')->name('commissions.')->group(function () {
        Route::get('/', [CommissionController::class, 'index'])->name('index');
        Route::post('/{entry}/approve', [CommissionController::class, 'approve'])->name('approve');
        Route::get('/payouts/list', [CommissionController::class, 'payouts'])->name('payouts');
        Route::post('/payouts/prepare', [CommissionController::class, 'preparePayout'])->name('payouts.store');
        Route::get('/payouts/{payout}', [CommissionController::class, 'showPayout'])->name('payouts.show');
        Route::post('/payouts/{payout}/approve', [CommissionController::class, 'approvePayout'])->name('payouts.approve');
        Route::post('/payouts/{payout}/process', [CommissionController::class, 'processPayout'])->name('payouts.process');
    });

    // Billing & Invoices
    Route::middleware('role:admin,receptionist,accountant')->prefix('billing')->name('billing.')->group(function () {
        Route::get('/', [BillingController::class, 'index'])->name('index');
        Route::get('/create', [BillingController::class, 'create'])->name('create');
        Route::post('/', [BillingController::class, 'store'])->name('store');
        Route::get('/{invoice}', [BillingController::class, 'show'])->name('show');
        Route::post('/{invoice}/payments', [BillingController::class, 'recordPayment'])->name('payments.store');
    });

    // Patient Portal
    Route::prefix('portal')->name('portal.')->group(function () {
        Route::get('/', [PatientPortalController::class, 'dashboard'])->name('dashboard');
        Route::get('/book', [PatientPortalController::class, 'book'])->name('book');
        Route::post('/book', [PatientPortalController::class, 'storeBooking'])->name('book.store');
        Route::get('/records', [PatientPortalController::class, 'records'])->name('records');
    });

    // Super Admin Platform Management
    Route::middleware('super_admin')->prefix('superadmin')->name('superadmin.')->group(function () {
        Route::get('/clinics', [SuperAdminController::class, 'clinics'])->name('clinics');
        Route::post('/clinics/{clinic}/toggle', [SuperAdminController::class, 'toggleClinicStatus'])->name('clinics.toggle');
        Route::get('/plans', [SuperAdminController::class, 'plans'])->name('plans');
    });
});
