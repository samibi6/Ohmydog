<?php

namespace App\Http\Controllers;

use App\Models\Gallerie;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

class GallerieController extends Controller
{
    public function index()
    {
        $galleries = Gallerie::all();
        return Inertia::render('Admin/Gallerie/Index', [
            'galleries' => $galleries
        ]);
    }

    public function frontIndex()
    {
        $galleries = Gallerie::all();
        return Inertia::render('Gallerie', [
            'canLogin' => Route::has('login'),
            'canRegister' => Route::has('register'),
            'galleries' => $galleries,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'imgBefore' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'imgAfter' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'description' => 'required'
        ]);

        $pathBefore = null;
        $pathAfter = null;

        $pathAfter = $request->file('imgAfter')->store('gallerie', 'public');

        if ($request->hasFile('imgBefore')) {
            $pathBefore = $request->file('imgBefore')->store('gallerie', 'public');
        }

        $gallerie = Gallerie::make([
            'imgBefore' => $pathBefore,
            'imgAfter' => $pathAfter,
            'description' => $request->input('description'),
        ]);

        $gallerie->save();

        return redirect()->route('gallerie.index')->with('success', 'image ajoutée avec succès.');
    }

    public function edit(Gallerie $gallerie)
    {
        return Inertia::render('Admin/Gallerie/Edit', [
            'gallerie' => $gallerie
        ]);
    }

    public function update(Request $request, Gallerie $gallerie)
    {
        $request->validate([
            'imgBefore' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'imgAfter' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'description' => 'required|string'
        ]);

        $pathBefore = null;
        $pathAfter = null;

        if ($request->hasFile('imgBefore')) {
            if ($gallerie->imgBefore) {
                Storage::delete('public/' . $gallerie->imgBefore);
            }
            $pathBefore = $request->file('imgBefore')->store('gallerie', 'public');
            $gallerie->update([
                'imgBefore' => $pathBefore
            ]);
        }

        if ($request->hasFile('imgAfter')) {
            if ($gallerie->imgBefore) {
                Storage::delete('public/' . $gallerie->imgAfter);
            }
            $pathAfter = $request->file('imgAfter')->store('gallerie', 'public');
            $gallerie->update([
                'imgAfter' => $pathAfter
            ]);
        }

        $gallerie->update([
            'description' => $request->input('description')
        ]);

        return redirect()->route('gallerie.index')->with('success', 'description modifiée avec succès');
    }

    public function delete(Request $request)
    {
        $gallerie = Gallerie::findOrFail($request->input('id'));

        if ($gallerie->imgBefore && Storage::disk('public')->exists($gallerie->imgBefore)) {
            Storage::disk('public')->delete($gallerie->imgBefore);
        }

        if ($gallerie->imgAfter && Storage::disk('public')->exists($gallerie->imgAfter)) {
            Storage::disk('public')->delete($gallerie->imgAfter);
        }

        $gallerie->delete();

        return redirect()->route('gallerie.index')->with('success', 'image supprimée avec succès');
    }
}
