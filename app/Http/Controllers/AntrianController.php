<?php

namespace App\Http\Controllers;

use App\Models\Antrian;
use App\Models\Poli;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class AntrianController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $polis = Poli::where('status', true)->select('id', 'nama', 'prefix')->get();

        return Inertia::render('Antrian/AmbilAntrian', [
            'polis' => $polis
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'poli_id' => 'required|exists:polis,id',
        ]);

        $hariIni = now()->toDateString();

        // Hitung urutan angka antrian terakhir hari ini di poli tersebut
        $antrianTerakhir = Antrian::where('poli_id', $request->poli_id)
                                  ->where('tanggal', $hariIni)
                                  ->orderBy('angka_antrian', 'desc')
                                  ->first();

        $angkaBaru = $antrianTerakhir ? $antrianTerakhir->angka_antrian + 1 : 1;

        // Ambil data poli untuk mendapatkan prefix-nya
        $poli = Poli::find($request->poli_id);

        // Buat format nomor antrian, misal: UMU-001
        $nomorCetak = $poli->prefix . '-' . Str::padLeft($angkaBaru, 3, '0');

        // Simpan ke database
        Antrian::create([
            'poli_id' => $poli->id,
            'angka_antrian' => $angkaBaru,
            'no_antrian' => $nomorCetak,
            'tanggal' => $hariIni,
            'status' => 'menunggu'
        ]);

        // Kembalikan dengan flash message bawaan Laravel untuk memicu modal tiket di Vue
        return back()->with([
            'success' => 'Antrian berhasil diambil!',
            'no_antrian' => $nomorCetak
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
