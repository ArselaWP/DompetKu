<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\WalletController;
use App\Http\Controllers\Api\AuthController;

// Mendapatkan data user yang sedang login
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Register wallet
Route::post('/wallets/register', [WalletController::class, 'register'])
    ->middleware('auth:sanctum');

Route::get('/wallets/{id}', [WalletController::class, 'balance'])
    ->middleware('auth:sanctum');

// Top Up Wallet
Route::post('/wallets/topup', [WalletController::class, 'topUp'])
    ->middleware('auth:sanctum');

// Authentication
Route::post('/login', [AuthController::class, 'login']);

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth:sanctum');    

// Transfer antar-wallet
Route::post('/wallets/transfer', [WalletController::class, 'transfer'])
    ->middleware('auth:sanctum');    