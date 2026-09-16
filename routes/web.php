<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Client\AuthController as ClientAuthController;
use App\Http\Controllers\Client\DashboardController as ClientDashboardController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DailyOperationController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\ManageUsersController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ServiceCategoryController;
use App\Http\Controllers\ServiceRequestAttachmentController;
use App\Http\Controllers\ServiceRequestController;
use App\Http\Controllers\ServiceRequestNoteController;
use App\Http\Controllers\Technician\AuthController as TechnicianAuthController;
use App\Http\Controllers\Technician\DashboardController as TechnicianDashboardController;
use App\Http\Controllers\TechnicianController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\BusinessSettingController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard');

Route::post('/locale/{locale}', [LocaleController::class, 'switch'])->name('locale.switch');

// Guest-only auth routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);

});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/unread', [NotificationController::class, 'unread'])->name('notifications.unread');

    Route::resource('customers', CustomerController::class);
    Route::patch('customers/{customer}/status', [CustomerController::class, 'toggleStatus'])->name('customers.toggle-status');

    Route::get('service-requests/board', [ServiceRequestController::class, 'board'])->name('service-requests.board');
    Route::resource('service-requests', ServiceRequestController::class);
    Route::patch('service-requests/{service_request}/status', [ServiceRequestController::class, 'updateStatus'])
        ->name('service-requests.update-status');
    Route::patch('service-requests/{service_request}/assign', [ServiceRequestController::class, 'assign'])
        ->name('service-requests.assign');

    Route::post('service-requests/{service_request}/notes', [ServiceRequestNoteController::class, 'store'])
        ->name('service-requests.notes.store');

    Route::post('service-requests/{service_request}/attachments', [ServiceRequestAttachmentController::class, 'store'])
        ->name('service-requests.attachments.store');
    Route::delete('attachments/{attachment}', [ServiceRequestAttachmentController::class, 'destroy'])
        ->name('attachments.destroy');

    Route::resource('invoices', InvoiceController::class);
    Route::post('invoices/{invoice}/record-payment', [InvoiceController::class, 'recordPayment'])
        ->name('invoices.record-payment');

    Route::resource('categories', ServiceCategoryController::class)->except(['show']);
    Route::resource('users', UserController::class)->only(['index', 'create', 'store']);
    Route::get('users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::get('users/{user}', [UserController::class, 'show'])->name('users.show');
    Route::patch('users/{user}/status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');

    Route::get('technicians', [TechnicianController::class, 'index'])->name('technicians.index');
    Route::get('technicians/create', [TechnicianController::class, 'create'])->name('technicians.create');
    Route::post('technicians', [TechnicianController::class, 'store'])->name('technicians.store');
    Route::get('technicians/complete', [TechnicianController::class, 'completed'])->name('technicians.complete');
    Route::get('technicians/assign', [TechnicianController::class, 'assign'])->name('technicians.assign');
    Route::post('technicians/assign', [TechnicianController::class, 'storeAssignment'])->name('technicians.assign.store');
    Route::get('technicians/{technician}/edit', [TechnicianController::class, 'edit'])->name('technicians.edit');
    Route::put('technicians/{technician}', [TechnicianController::class, 'update'])->name('technicians.update');
    Route::patch('technicians/{technician}/status', [TechnicianController::class, 'toggleStatus'])->name('technicians.toggle-status');

    Route::get('daily-operations', [DailyOperationController::class, 'index'])->name('daily-operations.index');
    Route::post('daily-operations/open', [DailyOperationController::class, 'open'])->name('daily-operations.open');
    Route::post('daily-operations/close', [DailyOperationController::class, 'close'])->name('daily-operations.close');
    Route::get('wallet', [WalletController::class, 'index'])->name('wallet.index');
    Route::post('wallet', [WalletController::class, 'store'])->name('wallet.store');
    Route::get('business-settings', [BusinessSettingController::class, 'edit'])->name('business-settings.edit');
    Route::put('business-settings', [BusinessSettingController::class, 'update'])->name('business-settings.update');

    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/revenue', [ReportController::class, 'revenue'])->name('revenue');
        Route::get('/service-requests', [ReportController::class, 'serviceRequests'])->name('service-requests');
        Route::get('/technicians', [ReportController::class, 'technicians'])->name('technicians');
        Route::get('/customers', [ReportController::class, 'customers'])->name('customers');
        Route::get('/invoices', [ReportController::class, 'invoices'])->name('invoices');
        Route::get('/cash', [ReportController::class, 'cash'])->name('cash');
    });

    Route::get('manage-users', [ManageUsersController::class, 'index'])->name('manage-users.index');
    Route::post('manage-users/{role}/{id}/reset-password', [ManageUsersController::class, 'resetPassword'])
        ->name('manage-users.reset-password');
});

// Technician portal (separate guard/login, no self-registration)
Route::prefix('technician')->name('technician.')->group(function () {
    Route::middleware('guest:technician')->group(function () {
        Route::get('/login', [TechnicianAuthController::class, 'create'])->name('login');
        Route::post('/login', [TechnicianAuthController::class, 'store']);
    });

    Route::middleware('auth:technician')->group(function () {
        Route::post('/logout', [TechnicianAuthController::class, 'destroy'])->name('logout');
        Route::get('/dashboard', [TechnicianDashboardController::class, 'index'])->name('dashboard');
        Route::patch('/jobs/{service_request}/status', [TechnicianDashboardController::class, 'updateStatus'])
            ->name('jobs.update-status');
        Route::post('/jobs/{service_request}/notes', [TechnicianDashboardController::class, 'storeNote'])
            ->name('jobs.notes.store');
    });
});

// Client (customer) portal — the only role that can self-register
Route::prefix('client')->name('client.')->group(function () {
    Route::middleware('guest:client')->group(function () {
        Route::get('/register', [ClientAuthController::class, 'showRegister'])->name('register');
        Route::post('/register', [ClientAuthController::class, 'register']);
        Route::get('/login', [ClientAuthController::class, 'create'])->name('login');
        Route::post('/login', [ClientAuthController::class, 'store']);
    });

    Route::middleware('auth:client')->group(function () {
        Route::post('/logout', [ClientAuthController::class, 'destroy'])->name('logout');
        Route::get('/dashboard', [ClientDashboardController::class, 'index'])->name('dashboard');
    });
});
