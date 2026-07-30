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

        $daftarPoli = Poli::all()->map(function ($poli) use ($hariIni) {
            $antrianAktif = Antrian::where('poli_id', $poli->id)
                ->whereDate('tanggal', $hariIni)
                ->where('status', 'dipanggil')
                ->first();

            $antrianDilewati = Antrian::where('poli_id', $poli->id)
                ->whereDate('tanggal', $hariIni)
                ->where('status', 'dilewati')
                ->pluck('no_antrian')
                ->toArray();

            $sisaAntrean = Antrian::where('poli_id', $poli->id)
                ->whereDate('tanggal', $hariIni)
                ->where('status', 'menunggu')
                ->count();

            return [
                'id' => $poli->id,
                'nama' => $poli->nama,
                'nomorTerkini' => $antrianAktif ? $antrianAktif->no_antrian : '-',
                'daftarDilewati' => $antrianDilewati,
                'sisaAntrean' => $sisaAntrean,
            ];
        });

        return Inertia::render('Dashboard', [
            'daftarPoli' => $daftarPoli
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
                'message' => 'Antrian ' . $antrianSaatIni->no_antrian . ' berhasil dilewati.',
                'nomor_antrian' => $antrianSaatIni->no_antrian
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Tidak ada antrian aktif untuk dilewati.'
        ], 404);
    }

    public function panggilDilewati(Request $request)
    {
        // Validasi input
        $request->validate([
            'poli_id' => 'required|exists:polis,id',
            'nomor_antrian' => 'required'
        ]);

        $hariIni = now()->toDateString();
        $poli = Poli::findOrFail($request->poli_id);
        $loketTujuan = $request->input('loket', $poli->nama);

        // 1. Selesaikan antrian yang mungkin sedang aktif di dalam ruangan saat ini
        Antrian::where('poli_id', $request->poli_id)
            ->whereDate('tanggal', $hariIni)
            ->where('status', 'dipanggil')
            ->update(['status' => 'selesai']);

        // 2. Cari antrian dilewati berdasarkan nomornya
        $antrianPending = Antrian::where('poli_id', $request->poli_id)
                                ->whereDate('tanggal', $hariIni)
                                ->where('no_antrian', $request->nomor_antrian)
                                ->where('status', 'dilewati')
                                ->first();

        if (!$antrianPending) {
            return response()->json([
                'success' => false,
                'message' => 'Data antrian tidak ditemukan di database.'
            ], 404);
        }

        // 3. Ubah statusnya kembali jadi dipanggil
        $antrianPending->update(['status' => 'dipanggil']);

        // 4. Bunyikan layar TV / Speaker (Pastikan class PanggilanAntrian sudah di-import di atas)
        broadcast(new PanggilanAntrian($antrianPending->no_antrian, $loketTujuan, $poli->id));

        return response()->json([
            'success' => true,
            'nomor_antrian' => $antrianPending->no_antrian
        ]);
    }
    public function antrianBerikutnya(Request $request)
    {
        $request->validate([
            'poli_id' => 'required|exists:polis,id',
        ]);

        $hariIni = now()->toDateString();
        $poli = Poli::findOrFail($request->poli_id);
        $loketTujuan = $request->input('loket', $poli->nama);

        // 1. CARI DULU nomor antrian berikutnya yang masih berstatus "menunggu"
        $antrianBaru = Antrian::where('poli_id', $request->poli_id)
                            ->whereDate('tanggal', $hariIni)
                            ->where('status', 'menunggu')
                            ->orderBy('angka_antrian', 'asc')
                            ->first();

        // 2. CEK: Jika antrian baru tidak ditemukan (habis)
        // BATALKAN PROSES. Jangan ubah status pasien saat ini agar tetap bisa di-Panggil Ulang.
        if (!$antrianBaru) {
            return response()->json([
                'success' => false,
                'message' => 'Antrian sudah habis.'
            ], 404);
        }

        // 3. Jika antrian baru ADA, barulah pasien yang lama kita ubah menjadi "selesai"
        Antrian::where('poli_id', $request->poli_id)
            ->whereDate('tanggal', $hariIni)
            ->where('status', 'dipanggil')
            ->update(['status' => 'selesai']);

        // 4. Ubah status antrian baru tersebut menjadi "dipanggil"
        $antrianBaru->update(['status' => 'dipanggil']);

        // 5. Broadcast event agar TV/Speaker memanggil nomor ini
        broadcast(new PanggilanAntrian($antrianBaru->no_antrian, $loketTujuan, $poli->id));

        // 6. Kembalikan respon ke Vue
        return response()->json([
            'success' => true,
            'message' => 'Memanggil antrian selanjutnya: ' . $antrianBaru->no_antrian,
            'nomor_antrian' => $antrianBaru->no_antrian
        ]);
    }

public function layarAntrian()
{
    $hariIni = now()->toDateString();

    // 1. Ambil data untuk kotak-kotak kecil di bawah (Daftar Poli)
    $polis = \App\Models\Poli::where('status', true)->get()->map(function ($poli) use ($hariIni) {
        // Asumsi status antrian Anda: 'menunggu', 'dipanggil', 'selesai', 'dilewati'
        $antrianTerkini = \App\Models\Antrian::where('poli_id', $poli->id)
            ->whereDate('tanggal', $hariIni)
            ->whereIn('status', ['dipanggil', 'selesai'])
            ->orderBy('updated_at', 'desc')
            ->first();

        return [
            'id' => $poli->id,
            'nama' => $poli->nama,
            'nomorTerkini' => $antrianTerkini ? $antrianTerkini->no_antrian : '-'
        ];
    });

    // 2. Ambil 1 antrian terakhir SATU KLINIK untuk ditampilkan di kotak BESAR (Sebelah Video)
    $antrianBesar = \App\Models\Antrian::whereDate('tanggal', $hariIni)
        ->where('status', 'dipanggil')
        ->orderBy('updated_at', 'desc')
        ->first();

    $loketBesar = 'MENUNGGU PANGGILAN';
    if ($antrianBesar) {
        $poliBesar = \App\Models\Poli::find($antrianBesar->poli_id);
        $loketBesar = $poliBesar ? $poliBesar->nama : 'LOKET';
    }

    // Kembalikan dalam bentuk JSON
    return response()->json([
        'polis' => $polis,
        'aktif' => [
            'nomor' => $antrianBesar ? $antrianBesar->no_antrian : '---',
            'loket' => $loketBesar
        ]
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
