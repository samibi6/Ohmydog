<?php

namespace App\Http\Controllers;

use App\Models\Taille;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TailleController extends Controller
{
    public function index()
    {
        $tailles = Taille::all();
        return Inertia::render('Admin/Taille/Index', [
            'tailles' => $tailles
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'exemple' => 'nullable|string',
        ]);

        $taille = Taille::make([
            'nom' => $request->input('nom'),
            'exemple' => $request->input('exemple')
        ]);

        $taille->save();

        return redirect()->route('taille.index')->with('success', 'Taille créée avec succès.');
    }

    public function edit(Taille $taille)
    {
        return Inertia::render('Admin/Taille/Edit', [
            'taille' => $taille
        ]);
    }

    public function update(Request $request, Taille $taille)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'exemple' => 'nullable|string',
        ]);

        $taille->update([
            'nom' => $request->input('nom'),
            'exemple' => $request->input('exemple')
        ]);

        return redirect()->route('taille.index')->with('success', 'Taille modifiée avec succès.');
    }

    public function delete(Request $request)
    {
        $taille = Taille::findOrFail($request->input('id'));
        $taille->delete();
        return redirect()->route('taille.index')->with('success', 'Taille supprimée avec succès.');
    }
}
