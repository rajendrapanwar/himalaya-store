<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceablePincode;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ServiceabilityController extends Controller
{
    public function index(Request $request)
    {
        $pincodes = ServiceablePincode::when($request->search, fn ($query, $search) => $query->where(fn ($q) => $q->where('pincode', 'like', "%{$search}%")->orWhere('city', 'like', "%{$search}%")))->orderBy('pincode')->paginate(25)->withQueryString();

        return view('admin.serviceability.index', compact('pincodes'));
    }

    public function create()
    {
        return view('admin.serviceability.form', ['serviceability' => new ServiceablePincode]);
    }

    public function store(Request $request)
    {
        ServiceablePincode::create($this->data($request));

        return redirect()->route('admin.serviceability.index')->with('success', 'Pincode added.');
    }

    public function edit(ServiceablePincode $serviceability)
    {
        return view('admin.serviceability.form', compact('serviceability'));
    }

    public function update(Request $request, ServiceablePincode $serviceability)
    {
        $serviceability->update($this->data($request, $serviceability));

        return redirect()->route('admin.serviceability.index')->with('success', 'Pincode updated.');
    }

    public function destroy(ServiceablePincode $serviceability)
    {
        $serviceability->delete();

        return back()->with('success', 'Pincode removed.');
    }

    public function import(Request $request)
    {
        $request->validate(['file' => 'required|file|mimes:csv,txt|max:2048']);
        $handle = fopen($request->file('file')->getRealPath(), 'r');
        $header = array_map('trim', fgetcsv($handle) ?: []);
        $count = 0;
        while (($row = fgetcsv($handle)) !== false) {
            $record = array_combine($header, $row);
            if (! $record || ! preg_match('/^[1-9][0-9]{5}$/', $record['pincode'] ?? '')) {
                continue;
            }
            ServiceablePincode::updateOrCreate(['pincode' => $record['pincode']], [
                'city' => $record['city'] ?? null,
                'state' => $record['state'] ?? 'Uttarakhand',
                'shipping_amount' => max(0, (float) ($record['shipping_amount'] ?? 0)),
                'estimated_days_min' => max(1, (int) ($record['estimated_days_min'] ?? 2)),
                'estimated_days_max' => max(1, (int) ($record['estimated_days_max'] ?? 7)),
                'cod_available' => filter_var($record['cod_available'] ?? true, FILTER_VALIDATE_BOOL),
                'is_active' => filter_var($record['is_active'] ?? true, FILTER_VALIDATE_BOOL),
            ]);
            $count++;
        }
        fclose($handle);

        return back()->with('success', "Imported {$count} serviceability record(s).");
    }

    public function export()
    {
        return response()->streamDownload(function () {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['pincode', 'city', 'state', 'shipping_amount', 'estimated_days_min', 'estimated_days_max', 'cod_available', 'is_active']);
            ServiceablePincode::orderBy('pincode')->chunk(500, fn ($rows) => $rows->each(fn ($row) => fputcsv($handle, [$row->pincode, $row->city, $row->state, $row->shipping_amount, $row->estimated_days_min, $row->estimated_days_max, (int) $row->cod_available, (int) $row->is_active])));
            fclose($handle);
        }, 'serviceable-pincodes.csv', ['Content-Type' => 'text/csv']);
    }

    private function data(Request $request, ?ServiceablePincode $serviceability = null): array
    {
        return $request->validate([
            'pincode' => ['required', 'regex:/^[1-9][0-9]{5}$/', Rule::unique('serviceable_pincodes')->ignore($serviceability)],
            'city' => 'nullable|string|max:100', 'state' => 'required|string|max:100',
            'shipping_amount' => 'required|numeric|min:0|max:10000',
            'estimated_days_min' => 'required|integer|min:1|max:60',
            'estimated_days_max' => 'required|integer|gte:estimated_days_min|max:90',
            'cod_available' => 'required|boolean', 'is_active' => 'required|boolean',
        ]);
    }
}
