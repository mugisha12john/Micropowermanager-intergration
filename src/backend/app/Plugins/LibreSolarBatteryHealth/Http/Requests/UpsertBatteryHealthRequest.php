<?php

declare(strict_types=1);

namespace App\Plugins\LibreSolarBatteryHealth\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpsertBatteryHealthRequest extends FormRequest {
    public function authorize(): bool {
        return true;
    }

    public function rules(): array {
        return [
            'battery_id' => ['required', 'string', 'max:255'],
            'site_id' => ['required', 'string', 'max:255'],
            'device_serial' => ['sometimes', 'nullable', 'string', 'max:255'],
            'village_id' => ['sometimes', 'nullable', 'integer'],
            'soc' => ['sometimes', 'nullable', 'numeric', 'between:0,100'],
            'soh' => ['sometimes', 'nullable', 'numeric', 'between:0,100'],
            'cycle_count' => ['sometimes', 'nullable', 'integer', 'min:0'],
            'cell_imbalance_v' => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'max_temperature_c' => ['sometimes', 'nullable', 'numeric'],
            'bms_state' => ['sometimes', 'nullable', 'string', 'max:100'],
            'last_seen_at' => ['required', 'date'],
        ];
    }

}
