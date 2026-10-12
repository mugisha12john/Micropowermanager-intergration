<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::connection('tenant')->create('libre_solar_battery_health', function (Blueprint $table): void {
            $table->id();
            $table->string('battery_id')->unique();
            $table->string('site_id');
            $table->string('device_serial')->nullable();
            $table->unsignedBigInteger('village_id')->nullable();
            $table->decimal('soc', 5, 2)->nullable();
            $table->decimal('soh', 5, 2)->nullable();
            $table->unsignedInteger('cycle_count')->nullable();
            $table->decimal('cell_imbalance_v', 8, 3)->nullable();
            $table->decimal('max_temperature_c', 6, 2)->nullable();
            $table->string('bms_state', 100)->nullable();
            $table->timestamp('last_seen_at')->nullable();
            $table->timestamps();
        });

        Schema::connection('tenant')->create('libre_solar_battery_faults', function (Blueprint $table): void {
            $table->id();
            $table->string('battery_id')->index();
            $table->string('rule_id');
            $table->string('severity', 20);
            $table->string('title');
            $table->json('evidence')->nullable();
            $table->text('explanation')->nullable();
            $table->string('status', 20);
            $table->timestamp('occurred_at');
            $table->timestamps();
            $table->index(['battery_id', 'occurred_at']);
        });
    }

    public function down(): void {
        Schema::connection('tenant')->dropIfExists('libre_solar_battery_faults');
        Schema::connection('tenant')->dropIfExists('libre_solar_battery_health');
    }
};
