<?php

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\TenantController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\ReceiptController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\SemanticSearchController;
use App\Http\Controllers\AIInsightController;

Route::inertia('/', 'Welcome', [
    'canRegister' => Features::enabled(Features::registration()),
])->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');
    Route::resource('tenants', TenantController::class);
    Route::resource('locations', LocationController::class);
    Route::resource('properties', PropertyController::class);
    Route::resource('rooms', RoomController::class);
    Route::resource('receipts', ReceiptController::class);
    Route::resource('expenses', ExpenseController::class);
    Route::post('/semantic-search', [SemanticSearchController::class, 'search']);
    Route::post('/ai-insight', [AIInsightController::class, 'generate']);
    Route::get('/ai/insights', function () {
        return Inertia::render('AI/Insights');
    });
});

require __DIR__.'/settings.php';
