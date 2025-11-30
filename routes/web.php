<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\BilletController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

Route::resource('accounts', AccountController::class);
Route::post('accounts/{account}/pay', [AccountController::class, 'pay'])
    ->name('accounts.pay');

Route::resource('transactions', TransactionController::class);
Route::resource('categories', CategoryController::class);
Route::post('/billet/upload', [BilletController::class, 'uploadBillet'])->name('billet.upload');
Route::post('/accounts/{account}/duplicate', [AccountController::class, 'duplicate'])->name('accounts.duplicate');
Route::post('/transactions/{transaction}/duplicate', [TransactionController::class, 'duplicate'])->name('transactions.duplicate');
