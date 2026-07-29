<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class SettingController extends Controller
{
    public function index(Request $request){
        $settings = Setting::pluck('value', 'key')->toArray();

        return Inertia::render('Setting/Index', [
            'settings' => $settings
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'nama_klinik'  => 'required|string|max:255',
            'alamat'       => 'nullable|string|max:500',
            'telepon'      => 'nullable|string|max:50',
            'video'        => 'nullable|string|max:500',
            'logo'         => 'nullable|image|mimes:jpeg,png,jpg,svg|max:2048',
        ]);

        // 1. Simpan Data Teks
        $fields = ['nama_klinik', 'alamat', 'telepon', 'video'];
        foreach ($fields as $field) {
            Setting::updateOrCreate(
                ['key' => $field],
                ['value' => $request->input($field, '')]
            );
        }

        // 2. Handle Upload Logo (Jika Ada File Baru)
        if ($request->hasFile('logo')) {
            // Hapus logo lama jika sudah pernah ada
            $logoLama = Setting::where('key', 'logo_klinik')->value('value');
            if ($logoLama) {
                $pathLama = str_replace('/storage/', '', $logoLama);
                Storage::disk('public')->delete($pathLama);
            }

            // Simpan file baru ke storage/app/public/settings
            $path = $request->file('logo')->store('settings', 'public');

            Setting::updateOrCreate(
                ['key' => 'logo_klinik'],
                ['value' => '/storage/' . $path]
            );
        }

        return back()->with('success', 'Pengaturan berhasil diperbarui!');
    }
}
