<?php

declare(strict_types=1);

namespace App\Plugins\LibreSolarBatteryHealth\Http\Controllers;

use App\Plugins\LibreSolarBatteryHealth\Http\Requests\StoreBatteryFaultRequest;
use App\Plugins\LibreSolarBatteryHealth\Http\Requests\UpsertBatteryHealthRequest;
use App\Plugins\LibreSolarBatteryHealth\Models\BatteryFault;
use App\Plugins\LibreSolarBatteryHealth\Models\BatteryHealth;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BatteryHealthController {
    public function index(Request $request): JsonResponse {
        $perPage = min(max($request->integer('per_page', 50), 1), 100);

        return response()->json([
            'data' => BatteryHealth::query()->latest('last_seen_at')->paginate($perPage),
        ]);
    }

    public function show(string $batteryId): JsonResponse {
        $battery = BatteryHealth::query()->where('battery_id', $batteryId)->firstOrFail();

        return response()->json([
            'data' => $battery->load(['faults' => fn ($query) => $query->latest('occurred_at')]),
        ]);
    }

    public function upsert(UpsertBatteryHealthRequest $request): JsonResponse {
        $attributes = $request->validated();
        $battery = BatteryHealth::query()->updateOrCreate(
            ['battery_id' => $attributes['battery_id']],
            $attributes,
        );

        return response()->json(['data' => $battery->fresh()], 200);
    }

    public function storeFault(StoreBatteryFaultRequest $request): JsonResponse {
        $fault = BatteryFault::query()->create($request->validated());

        return response()->json(['data' => $fault], 201);
    }
}
