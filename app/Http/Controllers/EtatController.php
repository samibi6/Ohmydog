<?php

namespace App\Http\Controllers;

use App\Models\Etat;
use Illuminate\Http\Request;
use Inertia\Inertia;

class EtatController extends Controller
{
    public function index()
    {
        $etats = Etat::all();
        return Inertia::render('Admin/Etat/Index', [
            'etats' => $etats
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $etat = Etat::make([
            'nom' => $request->input('nom'),
            'description' => $request->input('description')
        ]);

        $etat->save();

        return redirect()->route('etat.index')->with('success', 'etat créée avec succès.');
    }

    public function edit(Etat $etat)
    {
        return Inertia::render('Admin/Etat/Edit', [
            'etat' => $etat
        ]);
    }

    public function update(Request $request, Etat $etat)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $etat->update([
            'nom' => $request->input('nom'),
            'description' => $request->input('description')
        ]);

        return redirect()->route('etat.index')->with('success', 'etat modifié avec succès.');
    }

    public function delete(Request $request)
    {
        $etat = Etat::findOrFail($request->input('id'));
        $etat->delete();
        return redirect()->route('etat.index')->with('success', 'etat supprimé avec succès.');
    }
}
