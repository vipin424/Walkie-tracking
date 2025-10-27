<?php

namespace App\Http\Controllers;

use App\Models\Dispatch;
use App\Models\Payment;
use App\Models\Client;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $stats = [
                    'clients' => Client::count(),
                    'dispatches' => Dispatch::count(),
                    'active_dispatches' => Dispatch::where('status', Dispatch::STATUS_ACTIVE)->count(),
                    'partial_dispatches' => Dispatch::where('status', Dispatch::STATUS_PARTIAL)->count(),
                    'returned_dispatches' => Dispatch::where('status', Dispatch::STATUS_RETURNED)->count(),
                    'unpaid_dispatches' => Payment::where('payment_status', Payment::STATUS_UNPAID)->count(),
                    'advance_dispatches' => Payment::where('payment_status', Payment::STATUS_ADVANCE)->count(),
                ];

                $recent_dispatches = Dispatch::with('client')
                    ->orderByDesc('id')
                    ->limit(8)
                    ->get();
                //dd($stats, $recent_dispatches);
                return view('dashboard', compact('stats', 'recent_dispatches'));
    }
}
