<?php

declare(strict_types=1);

use App\Plugins\LibreSolarBatteryHealth\Http\Controllers\BatteryHealthController;
use Illuminate\Support\Facades\Route;

Route::prefix('libre-solar/battery-health')
    ->middleware('auth:api-key')
    ->group(function (): void {
        Route::get('/health', [BatteryHealthController::class, 'index']);
        Route::get('/health/{batteryId}', [BatteryHealthController::class, 'show']);
        Route::match(['post', 'put'], '/health', [BatteryHealthController::class, 'upsert']);
        Route::post('/faults', [BatteryHealthController::class, 'storeFault']);
    });

Route::prefix('batteries')
    ->middleware('auth:api')
    ->group(function (): void {
        Route::get('/health', [BatteryHealthController::class, 'index']);
        Route::get('/health/{batteryId}', [BatteryHealthController::class, 'show']);
    });
