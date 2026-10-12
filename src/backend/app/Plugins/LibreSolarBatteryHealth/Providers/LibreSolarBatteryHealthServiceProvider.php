<?php

declare(strict_types=1);

namespace App\Plugins\LibreSolarBatteryHealth\Providers;

use Illuminate\Support\ServiceProvider;

class LibreSolarBatteryHealthServiceProvider extends ServiceProvider {
    public function boot(): void {
        $this->app->register(RouteServiceProvider::class);
    }

    public function register(): void {}
}
