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
        //
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
