<?php

declare(strict_types=1);

namespace App\Plugins\LibreSolarBatteryHealth\Models;

use App\Models\Base\BaseModel;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BatteryHealth extends BaseModel {
    protected $table = 'libre_solar_battery_health';

    protected $fillable = [
        'battery_id',
        'site_id',
        'device_serial',
        'village_id',
        'soc',
        'soh',
        'cycle_count',
        'cell_imbalance_v',
        'max_temperature_c',
        'bms_state',
        'last_seen_at',
    ];

    protected $casts = [
        'soc' => 'float',
        'soh' => 'float',
        'cycle_count' => 'integer',
        'cell_imbalance_v' => 'float',
        'max_temperature_c' => 'float',
        'last_seen_at' => 'datetime',
    ];

    public function faults(): HasMany {
        return $this->hasMany(BatteryFault::class, 'battery_id', 'battery_id');
    }
}
