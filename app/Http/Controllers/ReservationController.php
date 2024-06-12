<?php

namespace App\Http\Controllers;

use App\Models\Duree;
use App\Models\Etat;
use App\Models\Horaire;
use App\Models\Prix;
use App\Models\Reservation;
use App\Models\Service;
use App\Models\Taille;
use App\Models\TypePoil;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Spatie\Holidays\Holidays;

class ReservationController extends Controller
{
    public function index()
    {
        $tailles = Taille::all();
        $typePoils = TypePoil::all();

        return Inertia::render('Reservation/Index', [
            'tailles' => $tailles,
            'typePoils' => $typePoils
        ]);
    }

    public function edit(Reservation $reservation)
    {
        return Inertia::render('Admin/Reservation/Edit', [
            'reservation' => $reservation,
        ]);
    }

    public function update(Request $request, Reservation $reservation)
    {
        $reservation->update([
            'date' => $request->input('date'),
            'heure' => $request->input('heure'),
        ]);

        return redirect()->route('adminReservation.index')->with('success', 'réservation modifiée avec succès.');
    }

    public function agenda(Request $request)
    {
        $users = User::all();
        $tailles = Taille::all();
        $poils = TypePoil::all();
        $etats = Etat::all();
        $services = Service::all();
        $durees = Duree::all();
        $indispos = Reservation::where('heure_fin', '!=', null)->where('date', '>=', Carbon::today())->get();

        // Récupérer le mois et l'année courants ou ceux passés dans la requête
        $month = $request->session()->get('month', Carbon::now()->month);
        $year = $request->session()->get('year', Carbon::now()->year);

        // Créer une instance de Carbon pour le premier jour du mois
        $date = Carbon::createFromDate($year, $month, 1);

        // Récupérer tous les jours du mois
        $daysInMonth = $date->daysInMonth;
        $days = [];

        // Array des jours de la semaine en français
        $joursSemaine = [
            0 => 'dimanche',
            1 => 'lundi',
            2 => 'mardi',
            3 => 'mercredi',
            4 => 'jeudi',
            5 => 'vendredi',
            6 => 'samedi',
        ];

        for ($i = 1; $i <= $daysInMonth; $i++) {
            $currentDate = Carbon::createFromDate($year, $month, $i)->startOfDay();

            // Récupérer le nom du jour en français
            $jourEnFrancais = $joursSemaine[$currentDate->dayOfWeek];

            // Vérifier si le jour est un jour férié
            $isHoliday = $this->isHoliday($currentDate);

            // Récupérer l'horaire pour ce jour
            $horaire = Horaire::where('jour', $jourEnFrancais)->first();

            // Vérifier si le jour est fermé
            $isClosed = !$horaire || !$horaire->ouvert;

            // Ajouter les informations du jour à l'array $days
            $days[] = [
                'date' => $currentDate->toDateString(),
                'dayOfWeek' => $currentDate->format('l'), // Nom complet du jour de la semaine
                'isHoliday' => $isHoliday,
                'isClosed' => $isClosed,
            ];
        }

        return Inertia::render('Dashboard', [
            'days' => $days,
            'month' => $month,
            'year' => $year,
            'indispos' => $indispos,
            'durees' => $durees,
            'tailles' => $tailles,
            'poils' => $poils,
            'etats' => $etats,
            'services' => $services,
            'users' => $users,
        ]);
    }

    public function updateAgenda(Request $request)
    {
        $request->validate([
            'month' => 'required',
            'year' =>   'required'
        ]);
        // Récupérer le mois et l'année depuis la requête
        $month = $request->input('month');
        $year = $request->input('year');

        // Mettre le mois et l'année en session
        $request->session()->put('month', $month);
        $request->session()->put('year', $year);

        // Rediriger l'utilisateur vers la page de calendrier
        return Inertia::location(route('dashboard'));
    }

    public function storeUnavailable(Request $request)
    {
        Reservation::create([
            'user_id' => Auth::user()->id,
            'date' => $request->input('date'),
            'heure' => $request->input('heure'),
            'heure_fin' => $request->input('heure_fin'),
            'race' => 'Indisponibilité',
            'type_poil_id' => 1,
            'duree_id' => 1
        ]);
        return Inertia::location(route('dashboard'));
    }

    public function adminIndex()
    {
        $reservations = Reservation::where('heure_fin', '=', null)->where('date', '>=', Carbon::today())->orderBy('date', 'asc')->orderBy('heure', 'asc')->get();
        $tailles = Taille::all();
        $poils = TypePoil::all();
        $etats = Etat::all();
        $services = Service::all();
        $durees = Duree::all();
        $users = User::all();
        return Inertia::render('Admin/Reservation/Index', [
            'tailles' => $tailles,
            'poils' => $poils,
            'etats' => $etats,
            'services' => $services,
            'durees' => $durees,
            'reservations' => $reservations,
            'users' => $users
        ]);
    }

    public function adminStore(Request $request)
    {
        Reservation::create([
            'user_id' => $request->input('user_id'),
            'date' => $request->input('date'),
            'heure' => $request->input('heure'),
            'race' => $request->input('race'),
            'type_poil_id' => $request->input('poil_id'),
            'duree_id' => $request->input('duree_id')
        ]);
        return Inertia::location(route('adminReservation.index'));
    }

    public function userReservations()
    {
        $reservations = Reservation::where('user_id', Auth::user()->id)
            ->where('date', '>=', Carbon::today())
            ->where('heure_fin', '=', null)
            ->orderBy('date', 'asc')
            ->orderBy('heure', 'asc')
            ->get();
        // Grouper les réservations par date
        $groupedReservations = $reservations->groupBy(function ($reservation) {
            return Carbon::parse($reservation->date)->toDateString();
        });

        // Ajouter une propriété 'isCancellable' à chaque réservation
        $groupedReservations->transform(function ($reservations) {
            return $reservations->map(function ($reservation) {
                $reservation->isCancellable = Carbon::parse($reservation->date)->diffInHours(Carbon::now()) <= 12;
                return $reservation;
            });
        });
        return Inertia::render('Profile/Reservations', [
            'groupedReservations' => $groupedReservations,
        ]);
    }

    public function userDelete(Request $request)
    {
        $reservation = Reservation::findOrFail($request->input('id'));
        if ($reservation->user_id === Auth::user()->id) {
            $reservation->delete();
        }
        return redirect()->route('user.reservations')->with('success', 'réservation annulée avec succès.');
    }

    public function adminDelete(Request $request)
    {
        $reservation = Reservation::findOrFail($request->input('id'));
        $reservation->delete();
        return redirect()->route('adminReservation.index')->with('success', 'réservation annulée avec succès.');
    }

    public function processForm(Request $request)
    {
        $request->validate([
            'race' => 'required|string'
        ]);
        // Traiter le formulaire et stocker les données soumises dans la session
        $request->session()->put('form_data', $request->all());

        // Rediriger l'utilisateur vers la page de calendrier
        return Inertia::location(route('reservation.calendar'));
    }

    public function updateCalendar(Request $request)
    {
        $request->validate([
            'month' => 'required',
            'year' =>   'required'
        ]);
        // Récupérer le mois et l'année depuis la requête
        $month = $request->input('month');
        $year = $request->input('year');

        // Mettre le mois et l'année en session
        $request->session()->put('month', $month);
        $request->session()->put('year', $year);

        // Rediriger l'utilisateur vers la page de calendrier
        return Inertia::location(route('reservation.calendar'));
    }

    protected $minResTime = 12;

    public function calendar(Request $request)
    {
        $formData = $request->session()->get('form_data', null);
        if ($formData === null) {
            return redirect()->route('reservation.select');
        }
        $advanceNoticeHours = $this->minResTime; // Heures minimum à l'avance pour réserver
        $reservationDuration = Carbon::createFromFormat('H:i:s', $formData['duree'])->hour * 60 + Carbon::createFromFormat('H:i:s', $formData['duree'])->minute; // Durée de la réservation en minutes
        // Récupérer le mois et l'année courants ou ceux passés dans la requête
        $month = $request->session()->get('month', Carbon::now()->month);
        $year = $request->session()->get('year', Carbon::now()->year);
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;
        $maxAllowedDate = Carbon::now()->addMonths(2);
        $storedDate = Carbon::create($year, $month);

        if ($year < $currentYear || ($year == $currentYear && $month < $currentMonth)) {
            $month = $currentMonth;
            $year = $currentYear;

            $request->session()->put('year', $year);
            $request->session()->put('month', $month);
        } else if ($storedDate->gt($maxAllowedDate)) {
            // Si la date stockée est dépassée, la mettre à la valeur maximale autorisée
            $year = $maxAllowedDate->year;
            $month = $maxAllowedDate->month;

            // Mettre à jour les valeurs en session
            $request->session()->put('year', $year);
            $request->session()->put('month', $month);
        }

        // Créer une instance de Carbon pour le premier jour du mois
        $date = Carbon::createFromDate($year, $month, 1);

        // Récupérer tous les jours du mois
        $daysInMonth = $date->daysInMonth;
        $days = [];

        // Array des jours de la semaine en français
        $joursSemaine = [
            0 => 'dimanche',
            1 => 'lundi',
            2 => 'mardi',
            3 => 'mercredi',
            4 => 'jeudi',
            5 => 'vendredi',
            6 => 'samedi',
        ];

        for ($i = 1; $i <= $daysInMonth; $i++) {
            $currentDate = Carbon::createFromDate($year, $month, $i)->startOfDay();

            $isPast = $currentDate->lt(Carbon::today()->startOfDay());
            $isTooLate = false;

            // Vérification si la réservation est trop tardive pour le jour actuel
            if (!$isPast) {
                $latestReservationTime = Carbon::now()->addHours($advanceNoticeHours);
                if ($currentDate->lt($latestReservationTime)) {
                    $isTooLate = false;
                }
            }

            // Récupérer le nom du jour en français
            $jourEnFrancais = $joursSemaine[$currentDate->dayOfWeek];

            // Vérifier si le jour est un jour férié
            $isHoliday = $this->isHoliday($currentDate);

            // Récupérer l'horaire pour ce jour
            $horaire = Horaire::where('jour', $jourEnFrancais)->first();

            // Vérifier si le jour est fermé
            $isClosed = !$horaire || !$horaire->ouvert;

            // Initialiser un booléen pour indiquer si le jour est complet
            $isFull = false;

            // Si le jour n'est ni férié, ni fermé, ni passé, ni trop tardif, effectuer les vérifications pour déterminer si le jour est complet
            if (!$isHoliday && !$isClosed && !$isPast) {
                // Récupérer tous les créneaux horaires disponibles pour ce jour
                $openingTime = $currentDate->copy()->setTimeFromTimeString($horaire->heure_debut);
                $closingTime = $currentDate->copy()->setTimeFromTimeString($horaire->heure_fin); // Adjust closing time by reservation duration

                $availableTimes = [];

                $now = Carbon::now();

                while ($openingTime->diffInMinutes($closingTime, false) >= $reservationDuration) {
                    // Ne pas ajouter de créneaux horaires dans le passé pour aujourd'hui
                    if ($currentDate->isToday() && $openingTime->lt($now) || $openingTime->lt($latestReservationTime)) {
                        $openingTime->addMinutes(30);
                        continue;
                    }

                    $proposedEndTime = $openingTime->copy()->addMinutes($reservationDuration);
                    if ($proposedEndTime->lte($closingTime)) {
                        // Ajouter le créneau horaire seulement s'il ne dépasse pas l'heure de fermeture
                        $availableTimes[] = $openingTime->copy();
                    }
                    $openingTime->addMinutes(30); // ou ajustez selon votre intervalle de temps
                }

                if (!empty($availableTimes)) {
                    // Récupérer toutes les réservations pour ce jour
                    $reservations = Reservation::whereDate('date', $currentDate)->get();

                    // Parcourir tous les créneaux horaires disponibles pour ce jour
                    foreach ($availableTimes as $time) {
                        // Vérifier si le créneau horaire est réservé pour la durée spécifiée
                        $isReserved = false;
                        foreach ($reservations as $reservation) {
                            $duree = Duree::find($reservation->duree_id);
                            $reservationStart = Carbon::createFromTimeString($reservation->heure);
                            $reservationEnd = $reservationStart->copy()->addMinutes(Carbon::createFromFormat('H:i:s', $duree->duree)->hour * 60 + Carbon::createFromFormat('H:i:s', $duree->duree)->minute);

                            // Vérifier si le créneau horaire est déjà réservé
                            if ($reservationStart <= $time && $time < $reservationEnd) {
                                $isReserved = true;
                                break;
                            }
                        }
                        // Si le créneau horaire n'est pas réservé, le jour n'est pas complet
                        if (!$isReserved) {
                            $isFull = false;
                            break;
                        }
                        $isFull = true;
                    }
                } else {
                    $isTooLate = true;
                }
            }

            // Ajouter les informations du jour à l'array $days
            $days[] = [
                'date' => $currentDate->toDateString(),
                'dayOfWeek' => $currentDate->format('l'), // Nom complet du jour de la semaine
                'isHoliday' => $isHoliday,
                'isClosed' => $isClosed,
                'isFull' => $isFull,
                'isPast' => $isPast,
                'isTooLate' => $isTooLate,
            ];
        }

        return Inertia::render('Reservation/Calendar', [
            'days' => $days,
            'month' => $month,
            'year' => $year,
            'service_id' => $formData['service_id'],
            'taille_id' => $formData['taille_id'],
            'etat_id' => $formData['etat_id'],
            'poil_id' => $formData['poil_id'],
            'race' => $formData['race'],
            'duree' => $formData['duree'],
            'duree_id' => $formData['duree_id'],
            'advance_notice_hours' => $advanceNoticeHours,
        ]);
    }



    // !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
    // AJOUTER METHODE POUR SUPPRIMER DONNEES EN SESSION LORSQUE USER SELECT UNE DATE ET HEURE DANS CALENDAR DONC DANS STORE
    // !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
    // GRISER JOURS PASSéS | TESTER isPast

    private function isHoliday($date)
    {
        $holidays = Holidays::for('be', $date->year)->get();
        foreach ($holidays as $holiday) {
            if ($date->equalTo(Carbon::parse($holiday['date']))) {
                return true;
            }
        }
        return false;
    }

    public function selectOptions()
    {
        return Inertia::render('Reservation/Index', [
            'tailles' => Taille::all(),
            'poils' => TypePoil::all(),
            'services' => Service::all(),
            'etats' => Etat::all(),
            'durees' => Duree::all(),
            'prixs' => Prix::all()
        ]);
    }

    public function validate(Request $request)
    {
        dd($request->input('race'));
    }

    public function store(Request $request)
    {
        Reservation::create([
            'user_id' => Auth::user()->id,
            'date' => $request->input('date'),
            'heure' => $request->input('heure'),
            'race' => $request->input('race'),
            'type_poil_id' => $request->input('poil_id'),
            'duree_id' => $request->input('duree_id')
        ]);
        $request->session()->forget('form_data');
        $request->session()->forget('month');
        $request->session()->forget('year');
        return Inertia::render('Reservation/Confirmation', [
            'canLogin' => Route::has('login'),
            'canRegister' => Route::has('register'),
            'date' => $request->input('date'),
            'heure' => $request->input('heure'),
        ]);
    }

    public function backToMenu(Request $request)
    {
        $request->session()->forget('form_data');
        $request->session()->forget('month');
        $request->session()->forget('year');
        return redirect()->route('home');
    }

    public function getAgenda(Request $request)
    {
        $date = $request->query('date');

        // Vérification des paramètres d'entrée
        if (!$date) {
            return response()->json(['message' => 'Invalid input'], 400);
        }

        $dateCarbon = Carbon::parse($date);
        $dayOfWeek = $dateCarbon->dayOfWeek; // 0 (Sunday) to 6 (Saturday)

        $joursSemaine = [
            0 => 'dimanche',
            1 => 'lundi',
            2 => 'mardi',
            3 => 'mercredi',
            4 => 'jeudi',
            5 => 'vendredi',
            6 => 'samedi',
        ];

        $jourEnFrancais = $joursSemaine[$dayOfWeek];

        $horaires = Horaire::where('jour', $jourEnFrancais)->first();

        // Vérifier si le jour est ouvert
        if (!$horaires || !$horaires->ouvert) {
            return response()->json(['message' => 'Fermé'], 200);
        }

        // Récupérer les réservations existantes pour cette date
        $reservations = Reservation::whereDate('date', $date)->where('heure_fin', '=', null)->orderBy('heure', 'asc')->get();

        return response()->json($reservations);
    }

    public function getAvailableTimes(Request $request)
    {
        $date = $request->query('date');
        $duree = $request->query('duree');

        $reservationDuration = Carbon::createFromFormat('H:i:s', $duree)->hour * 60 + Carbon::createFromFormat('H:i:s', $duree)->minute;  // durée en minutes
        $now = Carbon::now();
        $maxReservationTime = $now->copy()->addHours($this->minResTime);
        // Vérification des paramètres d'entrée
        if (!$date || !$duree) {
            return response()->json(['message' => 'Invalid input'], 400);
        }

        $dateCarbon = Carbon::parse($date);
        $dayOfWeek = $dateCarbon->dayOfWeek; // 0 (Sunday) to 6 (Saturday)

        $joursSemaine = [
            0 => 'dimanche',
            1 => 'lundi',
            2 => 'mardi',
            3 => 'mercredi',
            4 => 'jeudi',
            5 => 'vendredi',
            6 => 'samedi',
        ];

        $jourEnFrancais = $joursSemaine[$dayOfWeek];

        $horaires = Horaire::where('jour', $jourEnFrancais)->first();

        // Vérifier si le jour est ouvert
        if (!$horaires || !$horaires->ouvert) {
            return response()->json(['message' => 'Fermé'], 200);
        }

        // Récupérer les réservations existantes pour cette date
        $reservations = Reservation::whereDate('date', $date)->get();

        // Calculer les créneaux disponibles
        $openingTime = $dateCarbon->copy()->setTimeFromTimeString($horaires->heure_debut);
        $closingTime = $dateCarbon->copy()->setTimeFromTimeString($horaires->heure_fin);

        // Si l'heure d'ouverture est après l'heure de fermeture, renvoyer une réponse vide
        if ($openingTime->greaterThanOrEqualTo($closingTime)) {
            return response()->json(['message' => 'Invalid opening or closing times'], 200);
        }

        $availableTimes = [];

        while ($openingTime->diffInMinutes($closingTime, false) >= $reservationDuration) {
            if ($openingTime->gte($maxReservationTime)) {
                $isAvailable = true;

                foreach ($reservations as $reservation) {
                    $dureeRdv = Duree::find($reservation->duree_id);
                    $reservationStart = $dateCarbon->copy()->setTimeFromTimeString($reservation->heure);
                    $reservationEnd = $reservationStart->copy()->addMinutes(
                        Carbon::createFromFormat('H:i:s', $dureeRdv->duree)->hour * 60 +
                            Carbon::createFromFormat('H:i:s', $dureeRdv->duree)->minute
                    );

                    $proposedEnd = $openingTime->copy()->addMinutes($reservationDuration);

                    // Check if the proposed time overlaps with any existing reservation
                    if (
                        ($openingTime->between($reservationStart, $reservationEnd) || $proposedEnd->between($reservationStart, $reservationEnd)) ||
                        ($reservationStart->between($openingTime, $proposedEnd) || $reservationEnd->between($openingTime, $proposedEnd))
                    ) {
                        if (!$reservationEnd->eq($openingTime) && !$reservationStart->eq($proposedEnd)) {
                            $isAvailable = false;
                            break;
                        }
                    }
                }

                if ($isAvailable) {
                    // Vérifiez si l'heure de début proposée est après l'heure de fin de la dernière réservation
                    if ($reservations->isEmpty() || $openingTime->greaterThanOrEqualTo($reservationEnd) || $openingTime->lessThanOrEqualTo($reservationStart)) {
                        $availableTimes[] = $openingTime->format('H:i');
                    }
                }
            }
            // Move to the next possible slot
            $openingTime->addMinutes(30); // assuming you check every 30 minutes, you can adjust this value

            // Pour éviter une boucle infinie, vérifiez si $openingTime est toujours avant $closingTime
            if ($openingTime->greaterThanOrEqualTo($closingTime)) {
                break;
            }
        }

        return response()->json($availableTimes);
    }
}
