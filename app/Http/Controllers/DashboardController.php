<?php

namespace App\Http\Controllers;

use App\Models\Clinic\Patient;
use App\Models\Clinic\Appointment;
use App\Models\Clinic\Doctor;
use App\Models\Billing\Invoice;
use App\Models\Billing\Payment;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();
        
        $stats = [
            'total_patients' => Patient::count(),
            'total_doctors' => Doctor::count(),
            'today_appointments' => Appointment::whereDate('appointment_date', $today)->count(),
            'total_revenue' => Payment::sum('amount'), // Can be enhanced later to specific dates
        ];

        $todayAppointments = Appointment::with(['patient', 'doctor'])
            ->whereDate('appointment_date', $today)
            ->orderBy('appointment_time')
            ->take(5)
            ->get();

        $recentPatients = Patient::latest()->take(5)->get();
        
        $recentInvoices = Invoice::with('patient')->latest()->take(5)->get();

        // Data for last 7 days appointments chart
        $last7Days = collect(range(6, 0))->map(function ($days) {
            return Carbon::today()->subDays($days)->format('M d');
        });

        $appointmentsData = collect(range(6, 0))->map(function ($days) {
            return Appointment::whereDate('appointment_date', Carbon::today()->subDays($days))->count();
        });

        return view('dashboard', compact(
            'stats', 
            'todayAppointments', 
            'recentPatients', 
            'recentInvoices', 
            'last7Days', 
            'appointmentsData'
        ));
    }
}
