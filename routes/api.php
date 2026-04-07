<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\FarmerController;
use App\Http\Controllers\Api\TransactionController;
use App\Http\Controllers\Api\RepaymentController;
use App\Http\Controllers\Api\DebtController;

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);

    // Admin only — manage users
    Route::middleware('role:admin')->group(function () {
        Route::get('/users', [UserController::class, 'index']);
        Route::post('/users', [UserController::class, 'store']);
        Route::put('/users/{id}', [UserController::class, 'update']);
        Route::delete('/users/{id}', [UserController::class, 'destroy']);
    });

    // Supervisor only — manage operators
    Route::middleware('role:supervisor')->group(function () {
        Route::post('/operators', [UserController::class, 'storeOperator']);
        Route::get('/operators', [UserController::class, 'listOperators']);
        Route::put('/operators/{id}', [UserController::class, 'update']);
        Route::delete('/operators/{id}', [UserController::class, 'destroy']);
    });

    // Admin & supervisor — manage catalog
    Route::middleware('role:admin,supervisor')->group(function () {
        Route::post('/categories', [CategoryController::class, 'store']);
        Route::put('/categories/{id}', [CategoryController::class, 'update']);
        Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);


        Route::post('/products', [ProductController::class, 'store']);
        Route::put('/products/{id}', [ProductController::class, 'update']);
        Route::delete('/products/{id}', [ProductController::class, 'destroy']);
        Route::get('/debts', [DebtController::class, 'index']);

    });

    // All authenticated roles — farmers & transaction reads
    Route::middleware('role:admin,supervisor,operator')->group(function () {
        Route::post('/farmers', [FarmerController::class, 'store']);
        Route::get('/farmers/search', [FarmerController::class, 'search']);
        Route::get('/farmers/{id}', [FarmerController::class, 'show']);

        Route::get('/transactions', [TransactionController::class, 'index']);
        Route::get('/transactions/{id}', [TransactionController::class, 'show']);
        Route::get('/farmers/{id}/debts', [FarmerController::class, 'debts']);
        Route::get('/farmers/{id}/repayments', [FarmerController::class, 'repayments']);

                Route::get('/categories', [CategoryController::class, 'index']);
                Route::get('/products', [ProductController::class, 'index']);
        });

    // Operator only — create transactions
        Route::middleware('role:operator')->group(function () {
        Route::post('/transactions', [TransactionController::class, 'store']);
        Route::post('/repayments', [RepaymentController::class, 'store']);

        });

});
