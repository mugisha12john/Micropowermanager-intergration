<?php

declare(strict_types=1);

use App\Models\MpmPlugin;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void {
        DB::table('mpm_plugins')->insert([
            'id' => MpmPlugin::LIBRE_SOLAR_BATTERY_HEALTH,
            'name' => 'LibreSolarBatteryHealth',
            'description' => 'Ingests low-frequency Libre Solar battery health and faults.',
            'installation_command' => null,
            'root_class' => 'LibreSolarBatteryHealth',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void {
        DB::table('mpm_plugins')
            ->where('id', MpmPlugin::LIBRE_SOLAR_BATTERY_HEALTH)
            ->delete();
    }
};
