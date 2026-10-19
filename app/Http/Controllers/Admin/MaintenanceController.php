<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\MaintenanceModeService;
use Illuminate\Http\Request;

class MaintenanceController extends Controller
{
    public function index(MaintenanceModeService $maintenance)
    {
        return view('admin.maintenance.index', ['maintenance' => $maintenance->status()]);
    }

    public function update(Request $request, MaintenanceModeService $maintenance)
    {
        $data = $request->validate([
            'enabled' => ['required', 'boolean'],
            'message' => ['required_if:enabled,1', 'nullable', 'string', 'min:20', 'max:500'],
            'expected_return_at' => ['nullable', 'date', 'after:now'],
        ]);

        if ((bool) $data['enabled']) {
            $maintenance->enable(
                $request->user(),
                $data['message'],
                $data['expected_return_at'] ?? null,
            );

            return back()->with('success', 'Maintenance mode is active. Customers now see the maintenance page.');
        }

        $maintenance->disable($request->user());

        return back()->with('success', 'Maintenance mode is disabled. The storefront is live again.');
    }
}
