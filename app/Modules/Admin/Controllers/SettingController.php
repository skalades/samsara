<?php

namespace App\Modules\Admin\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Simaksi\Models\TrailSetting;
use App\Shared\Enums\TrailStatus;
use App\Shared\Enums\WeatherStatus;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingController extends Controller
{
    /**
     * Tampilkan halaman Pengaturan Tarif & Operasional SIMAKSI.
     */
    public function index(): View
    {
        $trailSetting = TrailSetting::current();
        $trailStatuses = TrailStatus::cases();
        $weatherStatuses = WeatherStatus::cases();

        return view('admin.settings.index', compact('trailSetting', 'trailStatuses', 'weatherStatuses'));
    }

    /**
     * Perbarui konfigurasi tarif SIMAKSI dan operasional jalur.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'ticket_fee_per_person' => ['required', 'integer', 'min:0', 'max:1000000'],
            'eco_deposit_fee' => ['required', 'integer', 'min:0', 'max:1000000'],
            'insurance_fee' => ['required', 'integer', 'min:0', 'max:500000'],
            'pad_desa_fee' => ['required', 'integer', 'min:0', 'max:500000'],
            'trail_status' => ['required', 'in:OPEN,WARNING,CLOSED'],
            'weather_status' => ['required', 'in:CERAH,BERAWAN,HUJAN_RINGAN,HUJAN_LEBAT,BADAI'],
            'announcement' => ['nullable', 'string', 'max:500'],
        ]);

        try {
            $setting = TrailSetting::first() ?? new TrailSetting();

            $setting->fill([
                'ticket_fee_per_person' => $validated['ticket_fee_per_person'],
                'eco_deposit_fee' => $validated['eco_deposit_fee'],
                'insurance_fee' => $validated['insurance_fee'],
                'pad_desa_fee' => $validated['pad_desa_fee'],
                'trail_status' => TrailStatus::from($validated['trail_status']),
                'weather_status' => WeatherStatus::from($validated['weather_status']),
                'announcement' => $validated['announcement'] ?? null,
                'updated_by' => auth()->id(),
            ]);

            $setting->save();

            return redirect()->route('admin.settings.index')
                ->with('success', 'Konfigurasi tarif SIMAKSI dan status operasional jalur berhasil diperbarui!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', 'Gagal memperbarui pengaturan: ' . $e->getMessage());
        }
    }
}
