<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Pet;
use App\Models\VaccinationRecord;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserDashboardController extends Controller
{
    public function index(Request $request)
    {
        Appointment::autoCompleteDueAppointments();

        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($user->hasRole('admin')) {
            $appointments = Appointment::query()
                ->with(['pet', 'user'])
                ->orderBy('appointment_date')
                ->take(8)
                ->get();

            $upcomingVaccinations = collect();
            $petsForCarnet = collect();

            $stats = [
                'total_pets' => Pet::count(),
                'appointments' => Appointment::count(),
                'pending_appointments' => Appointment::query()->where('status', 'pending')->count(),
            ];

            $adminPendingAppointments = $stats['pending_appointments'];
        } else {
            $appointments = $user->appointments()->with(['pet'])->latest()->take(5)->get();

            $petsForCarnet = $user->pets()
                ->withCount('vaccinationRecords')
                ->orderBy('name')
                ->get();

            $upcomingVaccinations = VaccinationRecord::query()
                ->whereHas('pet', function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                })
                ->whereNotNull('next_due_date')
                ->whereBetween('next_due_date', [Carbon::today(), Carbon::today()->addDays(7)])
                ->with('pet')
                ->orderBy('next_due_date')
                ->get();

            $stats = [
                'total_pets' => $user->pets()->count(),
                'appointments' => $user->appointments()->count(),
                'pending_appointments' => $user->appointments()->where('status', 'pending')->count(),
            ];

            $adminPendingAppointments = null;
        }

        return view('user.dashboard', [
            'user' => $user,
            'appointments' => $appointments,
            'upcomingVaccinations' => $upcomingVaccinations,
            'petsForCarnet' => $petsForCarnet,
            'adminPendingAppointments' => $adminPendingAppointments,
            'stats' => $stats
        ]);
    }

    public function login()
    {
        return view('login');
    }
}
