<?php

namespace App\Http\Controllers;

use App\Models\Poli;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PoliController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Poli::query();
            if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $polis = $query->paginate(10)->withQueryString();

        return Inertia::render('Poli/Index', [
            'polis' => $polis,
            'filters' => [
                'search' => $request->search,
            ],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Poli/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
        'nama' => 'required|string|max:255',
        'prefix' => 'required|string|max:5',
        'status' => 'required|boolean',
        ]);

        Poli::create([
            'nama' => $request->nama,
            'prefix' => $request->prefix,
            'status' => $request->status,
        ]);

        return redirect()->route('polis.index');
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
    public function edit(Poli $poli)
    {
        return Inertia::render('Poli/Edit',[
            'polis' => $poli,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Poli $poli)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'prefix' => 'required|string|max:5',
            'status' => 'required|boolean',
        ]);

        $poli->update($validated);

        return redirect()->route('polis.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Poli $poli)
    {
        $poli->delete();

        return redirect()
            ->route('polis.index')
            ->with('success', 'Poli berhasil dihapus.');
    }
}
