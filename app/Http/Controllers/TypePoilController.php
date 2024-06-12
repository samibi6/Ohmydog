<?php

namespace App\Http\Controllers;

use App\Models\TypePoil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class TypePoilController extends Controller
{
    public function index()
    {
        $typePoils = TypePoil::all();
        return Inertia::render('Admin/TypePoil/Index', [
            'typePoils' => $typePoils
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'illustration' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $path = null;

        if ($request->hasFile('illustration')) {
            $path = $request->file('illustration')->store('illustrations', 'public');
        }

        $typePoil = TypePoil::make([
            'nom' => $request->input('nom'),
            'illustration' => $path
        ]);

        $typePoil->save();

        return redirect()->route('typePoil.index')->with('success', 'Type de poil créé avec succès.');
    }

    public function edit(TypePoil $typePoil)
    {
        return Inertia::render('Admin/TypePoil/Edit', [
            'typePoil' => $typePoil
        ]);
    }

    public function update(Request $request, TypePoil $typePoil)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'illustration' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $path = null;

        if ($request->hasFile('illustration')) {
            if ($typePoil->illustration) {
                Storage::delete('public/' . $typePoil->illustration);
            }
            $path = $request->file('illustration')->store('illustrations', 'public');
            $typePoil->update([
                'illustration' => $path
            ]);
        }

        $typePoil->update([
            'nom' => $request->input('nom')
        ]);

        return redirect()->route('typePoil.index')->with('success', 'Type de poil modifié avec succès');
    }

    public function delete(Request $request)
    {
        $typePoil = TypePoil::findOrFail($request->input('id'));

        if ($typePoil->illustration && Storage::disk('public')->exists($typePoil->illustration)) {
            Storage::disk('public')->delete($typePoil->illustration);
        }

        $typePoil->delete();

        return redirect()->route('typePoil.index')->with('success', 'Type de poil supprimé avec succès');
    }
}