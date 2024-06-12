<?php

namespace App\Http\Controllers;

use App\Models\Prix;
use App\Models\Service;
use App\Models\Taille;
use Illuminate\Console\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

class PrixController extends Controller
{
    public function generate()
    {
        $prix = Prix::all();
        $services = Service::all();
        $tailles = Taille::all();

        if (count($prix) === 0 && count($services) != 0 && count($tailles) != 0) {
            foreach ($services as $service) {
                foreach ($tailles as $taille) {
                    Prix::create([
                        'service_id' => $service->id,
                        'taille_id' => $taille->id,
                        'prix' => '50'
                    ]);
                }
            }
        }

        return redirect()->route('prix.index');
    }

    public function index()
    {
        $prix = Prix::all();
        $services = Service::all();
        $tailles = Taille::all();

        return Inertia::render('Admin/Prix/Index', [
            'prixs' => $prix,
            'services' => $services,
            'tailles' => $tailles
        ]);
    }

    public function frontIndex()
    {
        $tarifs = Prix::all();
        $services = Service::all();
        $tailles = Taille::whereRaw('LOWER(nom) != ?', ['chat'])->get();
        return Inertia::render('Tarifs', [
            'canLogin' => Route::has('login'),
            'canRegister' => Route::has('register'),
            'tarifs' => $tarifs,
            'services' => $services,
            'tailles' => $tailles
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'service_id' => 'required|integer',
            'taille_id' => 'required|integer',
            'prix' => 'required|integer',
        ]);

        $prix = Prix::make([
            'service_id' => $request->input('service_id'),
            'taille_id' => $request->input('taille_id'),
            'prix' => $request->input('prix'),
        ]);

        $prix->save();

        return redirect()->route('prix.index')->with('success', 'prix créée avec succès.');
    }

    public function edit(Prix $prix)
    {
        $services = Service::all();
        $tailles = Taille::all();
        return Inertia::render('Admin/Prix/Edit', [
            'prix' => $prix,
            'services' => $services,
            'tailles' => $tailles
        ]);
    }

    public function update(Request $request, Prix $prix)
    {
        $request->validate([
            'service_id' => 'required|integer',
            'taille_id' => 'required|integer',
            'prix' => 'required|integer',
        ]);

        $prix->update([
            'service_id' => $request->input('service_id'),
            'taille_id' => $request->input('taille_id'),
            'prix' => $request->input('prix'),
        ]);

        return redirect()->route('prix.index')->with('success', 'prix modifié avec succès.');
    }

    public function delete(Request $request)
    {
        $prix = Prix::findOrFail($request->input('id'));
        $prix->delete();
        return redirect()->route('prix.index')->with('success', 'prix supprimé avec succès.');
    }
}
