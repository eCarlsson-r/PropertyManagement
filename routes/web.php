<?php

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\TenantController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\ReceiptController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SemanticSearchController;
use App\Http\Controllers\AIInsightController;

Route::inertia('/', 'Welcome', [
    'canRegister' => Features::enabled(Features::registration()),
])->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [ReportController::class, 'dashboard'])->name('dashboard');
    Route::resource('tenants', TenantController::class);
    Route::resource('locations', LocationController::class);
    Route::resource('properties', PropertyController::class);
    Route::resource('rooms', RoomController::class);
    Route::resource('receipts', ReceiptController::class);
    Route::resource('expenses', ExpenseController::class);
    Route::post('/semantic-search', [SemanticSearchController::class, 'search']);
    Route::get('/ai/insights', [AIInsightController::class, 'index'])->name('ai.insights.index');
    Route::post('/ai-insight', [AIInsightController::class, 'generate'])->name('ai.insights.generate');

    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', function () {
            return Inertia\Inertia::render('reports/Index', [
                'properties' => App\Models\Property::all()
            ]);
        })->name('index');

        // Data API for Reports
        Route::get('/stats', [ReportController::class, 'stats'])->name('api.stats');
        Route::get('/finance-data', [ReportController::class, 'finance'])->name('api.finance');
        Route::get('/profit-loss-data', [ReportController::class, 'profitLoss'])->name('api.profit-loss');
        Route::get('/occupancy', [ReportController::class, 'occupancy'])->name('occupancy');
        Route::get('/schedule', [ReportController::class, 'schedule'])->name('schedule');
        Route::get('/financial', [ReportController::class, 'financial'])->name('financial');
        Route::get('/download', [ReportController::class, 'download'])->name('download');
    });
});

require __DIR__.'/settings.php';