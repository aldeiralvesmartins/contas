<?php

use App\Http\Controllers\BilletController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/upload-billet', [BilletController::class, 'uploadBillet']);
});
