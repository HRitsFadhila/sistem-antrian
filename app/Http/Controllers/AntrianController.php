<?php

namespace App\Http\Controllers;

use App\Events\PanggilanAntrian;
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

    public function dashboard()
    {
        $hariIni = now()->toDateString();

        $polis = Poli::where('status', true)->get()->map(function ($poli) use ($hariIni) {
            $antrianTerkini = Antrian::where('poli_id', $poli->id)
                ->whereDate('tanggal', $hariIni)
                ->whereIn('status', ['sedang_dipanggil', 'selesai', 'dilewati'])
                ->orderBy('updated_at', 'desc')
                ->first();

            return [
                'id' => $poli->id,
                'nama' => $poli->nama,
                'nomorTerkini' => $antrianTerkini ? $antrianTerkini->no_antrian : '-'
            ];
        });

        return Inertia::render('Dashboard', [
            'daftarPoli' => $polis
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

    public function panggilAntrian(Request $request)
    {

        $hariIni = now()->toDateString();

        $antrian = Antrian::where('poli_id', $request->poli_id)
                            ->whereDate('tanggal', $hariIni)
                            ->where('status', 'dipanggil')
                            ->orderBy('angka_antrian', 'asc')
                            ->first();

        if (!$antrian) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada antrian yang aktif.'
            ], 404);
        }


        $poli = Poli::find($request->poli_id);
        $loketTujuan = $request->input('loket', $poli->nama);

        // PERBAIKAN 2: Tambahkan $poli->id ke dalam event
        broadcast(new PanggilanAntrian($antrian->no_antrian, $loketTujuan, $poli->id));

        return response()->json([
            'success' => true,
            'message' => 'Antrian ' . $antrian->no_antrian . ' berhasil dipanggil',
        ]);
    }

    public function lewatiAntrian(Request $request)
    {
        $request->validate([
            'poli_id' => 'required|exists:polis,id',
        ]);

        $hariIni = now()->toDateString();

        // Cari antrian yang sedang aktif saat ini
        $antrianSaatIni = Antrian::where('poli_id', $request->poli_id)
                                ->whereDate('tanggal', $hariIni)
                                ->where('status', 'dipanggil')
                                ->first();

        if ($antrianSaatIni) {
            // Ubah status antrian tersebut menjadi 'dilewati'
            $antrianSaatIni->update(['status' => 'dilewati']);

            return response()->json([
                'success' => true,
                'message' => 'Antrian ' . $antrianSaatIni->no_antrian . ' berhasil dilewati.'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Tidak ada antrian aktif untuk dilewati.'
        ], 404);
    }

    public function antrianBerikutnya(Request $request)
    {
        $request->validate([
            'poli_id' => 'required|exists:polis,id',
        ]);

        $hariIni = now()->toDateString();
        $poli = Poli::findOrFail($request->poli_id);
        $loketTujuan = $request->input('loket', $poli->nama);

        // 1. Ubah status antrian yang "dipanggil" sebelumnya menjadi "selesai"
        // Ini mengasumsikan pasien sebelumnya sudah selesai dilayani
        Antrian::where('poli_id', $request->poli_id)
            ->whereDate('tanggal', $hariIni)
            ->where('status', 'dipanggil')
            ->update(['status' => 'selesai']);

        // 2. Cari nomor antrian berikutnya yang masih berstatus "menunggu"
        $antrianBaru = Antrian::where('poli_id', $request->poli_id)
                            ->whereDate('tanggal', $hariIni)
                            ->where('status', 'menunggu')
                            ->orderBy('angka_antrian', 'asc') // Urutkan dari yang terkecil/terlama
                            ->first();

        // Jika antrian baru tidak ditemukan (habis)
        if (!$antrianBaru) {
            return response()->json([
                'success' => false,
                'message' => 'Antrian sudah habis.'
            ], 404);
        }

        // 3. Ubah status antrian baru tersebut menjadi "dipanggil"
        $antrianBaru->update(['status' => 'dipanggil']);

        // 4. Broadcast event agar TV/Speaker memanggil nomor ini
        broadcast(new PanggilanAntrian($antrianBaru->no_antrian, $loketTujuan, $poli->id));

        // 5. Kembalikan respon ke Vue (Sangat penting menyertakan 'nomor_antrian' agar tidak blank)
        return response()->json([
            'success' => true,
            'message' => 'Memanggil antrian selanjutnya: ' . $antrianBaru->no_antrian,
            'nomor_antrian' => $antrianBaru->no_antrian
        ]);
    }



    public function layarAntrian()
{
    $hariIni = now()->toDateString();

    $polis = \App\Models\Poli::where('status', true)->get()->map(function ($poli) use ($hariIni) {
        $antrianTerkini = \App\Models\Antrian::where('poli_id', $poli->id)
            ->whereDate('tanggal', $hariIni)
            ->whereIn('status', ['sedang_dipanggil', 'selesai', 'dilewati'])
            ->orderBy('updated_at', 'desc')
            ->first();

        return [
            'id' => $poli->id,
            'nama' => $poli->nama,
            'nomorTerkini' => $antrianTerkini ? $antrianTerkini->no_antrian : '-'
        ];
    });

    return response()->json($polis);
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
