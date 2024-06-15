<?php

use App\Http\Controllers\DureeController;
use App\Http\Controllers\EtatController;
use App\Http\Controllers\GallerieController;
use App\Http\Controllers\HoraireController;
use App\Http\Controllers\PrixController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\TailleController;
use App\Http\Controllers\TypePoilController;
use App\Http\Controllers\UserController;
use App\Models\Horaire;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Http\Middleware\HandlePrecognitiveRequests;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Route::get('/', function () {
//     return Inertia::render('Welcome', [
//         'canLogin' => Route::has('login'),
//         'canRegister' => Route::has('register'),
//         'laravelVersion' => Application::VERSION,
//         'phpVersion' => PHP_VERSION,
//     ]);
// });

Route::get('/', function () {
    return Inertia::render('Index', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
        'horaires' => Horaire::all()
    ]);
})->name('home');

Route::get('/tarifs', [PrixController::class, 'frontIndex'])->name('tarifs');

Route::get('/contact', function () {
    return Inertia::render('Contact', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
})->name('contact');

Route::get('/mentions-legales', function () {
    return Inertia::render('MentionsLegales', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
})->name('mentionsLegales');

Route::get('/galerie', [GallerieController::class, 'frontIndex'])->name('galerie');

Route::middleware([
    'auth:sanctum',
    'role:admin',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::match(['get', 'post'], '/dashboard', [ReservationController::class, 'agenda'])->name('dashboard');
    Route::post('/dashboard/store', [ReservationController::class, 'storeUnavailable'])->name('dashboard.store');
});

Route::get('/reservation/login', function () {
    return Inertia::render('Reservation/Login', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
})->name('reservation.login');

Route::middleware('auth')->group(function () {
    Route::get('/reservation', [ReservationController::class, 'selectOptions'])->name('reservation.select');
    Route::post('/reservation/process', [ReservationController::class, 'processForm'])->name('reservation.process');
    Route::post('/reservation/updateCalendar', [ReservationController::class, 'updateCalendar'])->name('reservation.updateCalendar');
    Route::post('/reservation/index', [ReservationController::class, 'validate'])->name('reservation.validate');
    Route::match(['get', 'post'], '/reservation/calendar', [ReservationController::class, 'calendar'])->name('reservation.calendar');
    Route::get('/reservation/disponibilities', [ReservationController::class, 'getAvailableTimes'])->name('reservation.availableTimes');
    Route::post('/reservation/store', [ReservationController::class, 'store'])->name('reservation.store');
    Route::get('/reservation/retour', [ReservationController::class, 'backToMenu'])->name('reservation.retour');

    Route::get('/user/reservations', [ReservationController::class, 'userReservations'])->name('user.reservations');
    Route::delete('/user/reservations', [ReservationController::class, 'userDelete'])->name('userReservation.delete');
});


Route::middleware('auth', 'role:admin', 'optimizeImages', HandlePrecognitiveRequests::class)->group(function () {

    Route::post('/reservation/updateAgenda', [ReservationController::class, 'updateAgenda'])->name('reservation.updateAgenda');
    Route::get('/reservation/getRDV', [ReservationController::class, 'getAgenda'])->name('reservation.getRDV');

    Route::post('/admin/typePoil', [TypePoilController::class, 'store'])->name('typePoil.store');
    Route::get('/admin/typePoil', [TypePoilController::class, 'index'])->name('typePoil.index');
    Route::delete('/admin/typePoil', [TypePoilController::class, 'delete'])->name('typePoil.delete');
    Route::get('/admin/typePoil/edit/{typePoil}', [TypePoilController::class, 'edit'])->name('typePoil.edit');
    Route::patch('/admin/typePoil/{typePoil}', [TypePoilController::class, 'update'])->name('typePoil.update');

    Route::post('/admin/taille', [TailleController::class, 'store'])->name('taille.store');
    Route::get('/admin/taille', [TailleController::class, 'index'])->name('taille.index');
    Route::delete('/admin/taille', [TailleController::class, 'delete'])->name('taille.delete');
    Route::get('/admin/taille/edit/{taille}', [TailleController::class, 'edit'])->name('taille.edit');
    Route::patch('/admin/taille/{taille}', [TailleController::class, 'update'])->name('taille.update');

    Route::post('/admin/service', [ServiceController::class, 'store'])->name('service.store');
    Route::get('/admin/service', [ServiceController::class, 'index'])->name('service.index');
    Route::delete('/admin/service', [ServiceController::class, 'delete'])->name('service.delete');
    Route::get('/admin/service/edit/{service}', [ServiceController::class, 'edit'])->name('service.edit');
    Route::patch('/admin/service/{service}', [ServiceController::class, 'update'])->name('service.update');

    Route::post('/admin/horaire', [HoraireController::class, 'store'])->name('horaire.store');
    Route::get('/admin/horaire', [HoraireController::class, 'index'])->name('horaire.index');
    Route::delete('/admin/horaire', [HoraireController::class, 'delete'])->name('horaire.delete');
    Route::get('/admin/horaire/edit/{horaire}', [HoraireController::class, 'edit'])->name('horaire.edit');
    Route::patch('/admin/horaire/{horaire}', [HoraireController::class, 'update'])->name('horaire.update');
    Route::get('/admin/horaire/generate', [HoraireController::class, 'generate'])->name('horaire.generate');

    Route::post('/admin/etat', [EtatController::class, 'store'])->name('etat.store');
    Route::get('/admin/etat', [EtatController::class, 'index'])->name('etat.index');
    Route::delete('/admin/etat', [EtatController::class, 'delete'])->name('etat.delete');
    Route::get('/admin/etat/edit/{etat}', [EtatController::class, 'edit'])->name('etat.edit');
    Route::patch('/admin/etat/{etat}', [EtatController::class, 'update'])->name('etat.update');

    Route::post('/admin/gallerie', [GallerieController::class, 'store'])->name('gallerie.store');
    Route::get('/admin/gallerie', [GallerieController::class, 'index'])->name('gallerie.index');
    Route::delete('/admin/gallerie', [GallerieController::class, 'delete'])->name('gallerie.delete');
    Route::get('/admin/gallerie/edit/{gallerie}', [GallerieController::class, 'edit'])->name('gallerie.edit');
    Route::patch('/admin/gallerie/{gallerie}', [GallerieController::class, 'update'])->name('gallerie.update');

    Route::post('/admin/prix', [PrixController::class, 'store'])->name('prix.store');
    Route::get('/admin/prix', [PrixController::class, 'index'])->name('prix.index');
    Route::delete('/admin/prix', [PrixController::class, 'delete'])->name('prix.delete');
    Route::get('/admin/prix/edit/{prix}', [PrixController::class, 'edit'])->name('prix.edit');
    Route::patch('/admin/prix/{prix}', [PrixController::class, 'update'])->name('prix.update');
    Route::get('/admin/prix/generate', [PrixController::class, 'generate'])->name('prix.generate');

    Route::post('/admin/duree', [DureeController::class, 'store'])->name('duree.store');
    Route::get('/admin/duree', [DureeController::class, 'index'])->name('duree.index');
    Route::delete('/admin/duree', [DureeController::class, 'delete'])->name('duree.delete');
    Route::get('/admin/duree/edit/{duree}', [DureeController::class, 'edit'])->name('duree.edit');
    Route::patch('/admin/duree/{duree}', [DureeController::class, 'update'])->name('duree.update');
    Route::get('/admin/duree/generate', [DureeController::class, 'generate'])->name('duree.generate');

    Route::get('/admin/user', [UserController::class, 'index'])->name('user.index');

    Route::post('/admin/reservation', [ReservationController::class, 'adminStore'])->name('adminReservation.store');
    Route::get('/admin/reservation', [ReservationController::class, 'adminIndex'])->name('adminReservation.index');
    Route::delete('/admin/reservation', [ReservationController::class, 'adminDelete'])->name('adminReservation.delete');
    Route::get('/admin/reservation/edit/{reservation}', [ReservationController::class, 'edit'])->name('adminReservation.edit');
    Route::patch('/admin/reservation/{reservation}', [ReservationController::class, 'update'])->name('adminReservation.update');
});
