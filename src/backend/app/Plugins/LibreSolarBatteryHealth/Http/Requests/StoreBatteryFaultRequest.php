<?php

declare(strict_types=1);

namespace App\Plugins\LibreSolarBatteryHealth\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBatteryFaultRequest extends FormRequest {
    public function authorize(): bool {
        return true;
    }

    public function rules(): array {
        return [
            'battery_id' => ['required', 'string', 'max:255'],
            'rule_id' => ['required', 'string', 'max:255'],
            'severity' => ['required', 'string', 'in:info,warning,critical'],
            'title' => ['required', 'string', 'max:255'],
            'evidence' => ['sometimes', 'nullable', 'array'],
            'explanation' => ['sometimes', 'nullable', 'string'],
            'status' => ['required', 'string', 'in:open,acknowledged,resolved'],
            'occurred_at' => ['required', 'date'],
        ];
    }

}
