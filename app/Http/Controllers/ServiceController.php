<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::all();
        return Inertia::render('Admin/Service/Index', [
            'services' => $services
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $service = Service::make([
            'nom' => $request->input('nom'),
            'description' => $request->input('description')
        ]);

        $service->save();

        return redirect()->route('service.index')->with('success', 'service créé avec succès.');
    }

    public function edit(Service $service)
    {
        return Inertia::render('Admin/Service/Edit', [
            'service' => $service
        ]);
    }

    public function update(Request $request, Service $service)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $service->update([
            'nom' => $request->input('nom'),
            'description' => $request->input('description')
        ]);

        return redirect()->route('service.index')->with('success', 'service modifié avec succès.');
    }

    public function delete(Request $request)
    {
        $service = Service::findOrFail($request->input('id'));
        $service->delete();
        return redirect()->route('service.index')->with('success', 'service supprimé avec succès.');
    }
}
