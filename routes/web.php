<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PortfolioController;

Route::get('/', function () {
    return view('portfolio');
});

Route::get('/api/portfolios', [PortfolioController::class, 'index']);
Route::post('/api/portfolios', [PortfolioController::class, 'store']);
Route::put('/api/portfolios/{portfolio}', [PortfolioController::class, 'update']);
Route::delete('/api/portfolios/{portfolio}', [PortfolioController::class, 'destroy']);
