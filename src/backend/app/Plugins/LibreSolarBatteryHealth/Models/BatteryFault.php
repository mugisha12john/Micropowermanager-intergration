<?php

declare(strict_types=1);

namespace App\Plugins\LibreSolarBatteryHealth\Models;

use App\Models\Base\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BatteryFault extends BaseModel {
    protected $table = 'libre_solar_battery_faults';

    protected $fillable = [
        'battery_id',
        'rule_id',
        'severity',
        'title',
        'evidence',
        'explanation',
        'status',
        'occurred_at',
    ];

    protected $casts = [
        'evidence' => 'array',
        'occurred_at' => 'datetime',
    ];

    public function batteryHealth(): BelongsTo {
        return $this->belongsTo(BatteryHealth::class, 'battery_id', 'battery_id');
    }
}
