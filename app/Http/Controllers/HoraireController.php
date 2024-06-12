<?php

namespace App\Http\Controllers;

use App\Models\Horaire;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class HoraireController extends Controller
{
    public function generate()
    {
        $horaires = Horaire::all();
        if (count($horaires) === 0) {
            $joursDeLaSemaine = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'];
            foreach ($joursDeLaSemaine as $jour) {
                Horaire::create([
                    'jour' => $jour,
                    'ouvert' => false,
                    'heure_debut' => '00:00',
                    'heure_fin' => '00:00'
                ]);
            }
        }

        $horaires = Horaire::all();

        return redirect()->route('horaire.index');
    }

    public function index()
    {
        $horaires = Horaire::all();
        return Inertia::render('Admin/Horaire/Index', [
            'horaires' => $horaires
        ]);
    }

    public function store(Request $request)
    {
        $heure_debut = Carbon::parse($request->input('heure_debut'))->format('H:i');
        $heure_fin = Carbon::parse($request->input('heure_fin'))->format('H:i');
        $request->validate([
            'jour' => 'required|string|max:255',
            'ouvert' => 'required|boolean',
            'heure_debut' => 'nullable|date_format:H:i',
            'heure_fin' => 'nullable|date_format:H:i',
        ]);

        $horaire = Horaire::make([
            'jour' => $request->input('jour'),
            'ouvert' => $request->input('ouvert'),
            'heure_debut' => $heure_debut,
            'heure_fin' => $heure_fin,
        ]);

        $horaire->save();

        return redirect()->route('horaire.index')->with('success', 'horaire créée avec succès.');
    }

    public function edit(Horaire $horaire)
    {
        return Inertia::render('Admin/Horaire/Edit', [
            'horaire' => $horaire
        ]);
    }

    public function update(Request $request, Horaire $horaire)
    {
        $heure_debut = Carbon::parse($request->input('heure_debut'))->format('H:i');
        $heure_fin = Carbon::parse($request->input('heure_fin'))->format('H:i');
        $request->validate([
            'jour' => 'required|string|max:255',
            'ouvert' => 'required|boolean',
            'heure_debut' => 'nullable|date_format:H:i',
            'heure_fin' => 'nullable|date_format:H:i',
        ]);

        $horaire->update([
            'jour' => $request->input('jour'),
            'ouvert' => $request->input('ouvert'),
            'heure_debut' => $heure_debut,
            'heure_fin' => $heure_fin,
        ]);

        return redirect()->route('horaire.index')->with('success', 'horaire modifié avec succès.');
    }

    public function delete(Request $request)
    {
        $horaire = Horaire::findOrFail($request->input('id'));
        $horaire->delete();
        return redirect()->route('horaire.index')->with('success', 'horaire supprimé avec succès.');
    }
}
