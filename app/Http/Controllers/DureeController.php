<?php

namespace App\Http\Controllers;

use App\Models\Duree;
use App\Models\Etat;
use App\Models\Service;
use App\Models\Taille;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DureeController extends Controller
{
    public function generate()
    {
        $duree = Duree::all();
        $services = Service::all();
        $tailles = Taille::all();
        $etats = Etat::all();

        if (count($duree) === 0 && count($services) !== 0 && count($tailles) !== 0 && count($etats) !== 0) {
            foreach ($services as $service) {
                foreach ($tailles as $taille) {
                    foreach ($etats as $etat) {
                        Duree::create([
                            'service_id' => $service->id,
                            'taille_id' => $taille->id,
                            'etat_id' => $etat->id,
                            'duree' => '02:00'
                        ]);
                    }
                }
            }
        }

        return redirect()->route('duree.index');
    }

    public function index()
    {
        $duree = Duree::all();
        $services = Service::all();
        $tailles = Taille::all();
        $etats = Etat::all();

        return Inertia::render('Admin/Duree/Index', [
            'durees' => $duree,
            'services' => $services,
            'tailles' => $tailles,
            'etats' => $etats
        ]);
    }

    public function store(Request $request)
    {
        $time = Carbon::parse($request->input('duree'))->format('H:i');
        $request->validate([
            'service_id' => 'required|integer',
            'taille_id' => 'required|integer',
            'etat_id' => 'required|integer',
            'duree' => 'required|date_format:H:i',
        ]);

        $duree = Duree::make([
            'service_id' => $request->input('service_id'),
            'taille_id' => $request->input('taille_id'),
            'etat_id' => $request->input('etat_id'),
            'duree' => $time,
        ]);

        $duree->save();

        return redirect()->route('duree.index')->with('success', 'duree créée avec succès.');
    }

    public function edit(Duree $duree)
    {
        $services = Service::all();
        $tailles = Taille::all();
        $etats = Etat::all();
        return Inertia::render('Admin/Duree/Edit', [
            'duree' => $duree,
            'services' => $services,
            'tailles' => $tailles,
            'etats' => $etats
        ]);
    }

    public function update(Request $request, Duree $duree)
    {
        $time = Carbon::parse($request->input('duree'))->format('H:i');
        $request->validate([
            'service_id' => 'required|integer',
            'taille_id' => 'required|integer',
            'etat_id' => 'required|integer',
            'duree' => 'required|date_format:H:i',
        ]);

        $duree->update([
            'service_id' => $request->input('service_id'),
            'taille_id' => $request->input('taille_id'),
            'etat_id' => $request->input('etat_id'),
            'duree' => $time,
        ]);

        return redirect()->route('duree.index')->with('success', 'duree modifiée avec succès.');
    }

    public function delete(Request $request)
    {
        $duree = Duree::findOrFail($request->input('id'));
        $duree->delete();
        return redirect()->route('duree.index')->with('success', 'duree supprimée avec succès.');
    }
}
