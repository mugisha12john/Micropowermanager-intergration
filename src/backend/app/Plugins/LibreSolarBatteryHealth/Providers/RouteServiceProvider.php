<?php

declare(strict_types=1);

namespace App\Plugins\LibreSolarBatteryHealth\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider {
    protected $namespace = 'App\Plugins\LibreSolarBatteryHealth\Http\Controllers';

    public function boot(): void {
        parent::boot();
    }

    public function map(): void {
        Route::prefix('api')
            ->middleware('api')
            ->namespace($this->namespace)
            ->group(__DIR__.'/../routes/api.php');
    }
}
