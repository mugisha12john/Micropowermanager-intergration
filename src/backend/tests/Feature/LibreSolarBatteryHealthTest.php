<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Plugins\LibreSolarBatteryHealth\Models\BatteryFault;
use App\Plugins\LibreSolarBatteryHealth\Models\BatteryHealth;
use Tests\RefreshMultipleDatabases;
use Tests\TestCase;

class LibreSolarBatteryHealthTest extends TestCase {
    use RefreshMultipleDatabases;

    public function testItUpsertsHealthAndReturnsFaults(): void {
        $this->withoutMiddleware('auth:api-key');

        $health = [
            'battery_id' => 'battery-001',
            'site_id' => 'site-001',
            'soc' => 82.5,
            'soh' => 97.1,
            'cycle_count' => 44,
            'cell_imbalance_v' => 0.012,
            'max_temperature_c' => 31.4,
            'bms_state' => 'ok',
            'last_seen_at' => '2026-10-09T17:00:00Z',
        ];

        $this->putJson('/api/libre-solar/battery-health/health', $health)
            ->assertOk()
            ->assertJsonPath('data.battery_id', 'battery-001')
            ->assertJsonPath('data.soc', 82.5);

        $this->postJson('/api/libre-solar/battery-health/faults', [
            'battery_id' => 'battery-001',
            'rule_id' => 'cell-imbalance',
            'severity' => 'warning',
            'title' => 'Cell imbalance',
            'evidence' => ['imbalance_v' => 0.012],
            'explanation' => 'Cell delta exceeded the warning threshold.',
            'status' => 'open',
            'occurred_at' => '2026-10-09T17:00:01Z',
        ])->assertCreated();

        $this->getJson('/api/libre-solar/battery-health/health/battery-001')
            ->assertOk()
            ->assertJsonPath('data.faults.0.rule_id', 'cell-imbalance');

        $this->assertSame(1, BatteryHealth::query()->count());
        $this->assertSame(1, BatteryFault::query()->count());
    }

    public function testItRejectsInvalidHealthValues(): void {
        $this->withoutMiddleware('auth:api-key');

        $this->putJson('/api/libre-solar/battery-health/health', [
            'battery_id' => 'battery-001',
            'site_id' => 'site-001',
            'soc' => 120,
            'last_seen_at' => 'not-a-date',
        ])->assertUnprocessable();
    }
}
