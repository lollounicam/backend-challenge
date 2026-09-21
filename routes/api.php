<?php

use App\Http\Controllers\ClientController;
use App\Http\Controllers\DebtCaseController;
use Illuminate\Support\Facades\Route;

Route::post('/clients', [ClientController::class, 'store']);

Route::post('/cases', [DebtCaseController::class, 'store']);
Route::get('/cases', [DebtCaseController::class, 'index']);
Route::get('/cases/{debtCase}', [DebtCaseController::class, 'show']);

Route::patch('/cases/{debtCase}/status', [DebtCaseController::class, 'updateStatus']);
