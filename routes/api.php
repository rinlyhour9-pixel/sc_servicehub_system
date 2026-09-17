<?php

use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\TechnicianJobController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login'])->middleware('throttle:6,1');
    Route::post('forgot-password', [AuthController::class, 'forgotPassword'])->middleware('throttle:6,1');
    Route::post('reset-password', [AuthController::class, 'resetPassword'])->middleware('throttle:6,1');
});
Route::get('services', [BookingController::class, 'services']);
Route::get('services/{service}/availability', [BookingController::class, 'availability']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('auth/logout', [AuthController::class, 'logout']);
    Route::get('me', [ProfileController::class, 'show']);
    Route::patch('me', [ProfileController::class, 'update']);
    Route::get('notifications', [NotificationController::class, 'index']);
    Route::patch('notifications/{notification}/read', [NotificationController::class, 'markRead']);

    Route::middleware('role:client')->group(function () {
        Route::post('bookings', [BookingController::class, 'store']);
        Route::get('bookings', [BookingController::class, 'index']);
    });
    // The policy makes this shared detail endpoint safe for every role.
    Route::get('bookings/{booking}', [BookingController::class, 'show']);

    Route::prefix('technician')->middleware('role:technician')->group(function () {
        Route::get('jobs', [TechnicianJobController::class, 'index']);
        Route::post('jobs/{booking}/start', [TechnicianJobController::class, 'start']);
        Route::post('jobs/{booking}/complete', [TechnicianJobController::class, 'complete']);
        Route::patch('availability', [TechnicianJobController::class, 'availability']);
    });

    Route::prefix('admin')->middleware('role:admin')->group(function () {
        Route::get('dashboard', [AdminController::class, 'dashboard']);
        Route::get('bookings', [AdminController::class, 'bookings']);
        Route::patch('bookings/{booking}/technician', [AdminController::class, 'assign']);
        Route::get('customers', [AdminController::class, 'customers']);
        Route::post('customers', [AdminController::class, 'createCustomer']);
        Route::get('customers/{customer}', [AdminController::class, 'customer']);
        Route::get('technicians', [AdminController::class, 'technicians']);
        Route::get('technicians/{technician}', [AdminController::class, 'technician']);
    });
});
