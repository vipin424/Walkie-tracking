<?php

namespace App\Http\Controllers;

use App\Models\Dispatch;
use App\Models\Payment;
use App\Models\Client;
use App\Models\Order;
use App\Models\PaymentTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Dispatch Stats
        $dispatchStats = [
            'total' => Dispatch::count(),
            'active' => Dispatch::where('status', Dispatch::STATUS_ACTIVE)->count(),
            'partial' => Dispatch::where('status', Dispatch::STATUS_PARTIAL)->count(),
            'returned' => Dispatch::where('status', Dispatch::STATUS_RETURNED)->count(),
        ];

        // Payment Stats (Dispatch related)
        $dispatchPaymentStats = [
            'unpaid' => Payment::where('payment_status', Payment::STATUS_UNPAID)->count(),
            'advance' => Payment::where('payment_status', Payment::STATUS_ADVANCE)->count(),
        ];

        // Order Stats
        $orderStats = [
            'total' => Order::count(),
            'confirmed' => Order::where('status', 'confirmed')->count(),
            'completed' => Order::where('status', 'completed')->count(),
            'sent' => Order::where('status', 'sent')->count(),
            
            // Event Status
            'upcoming' => Order::where('event_from', '>', now())->count(),
            'running' => Order::whereDate('event_from', '<=', now())
                            ->whereDate('event_to', '>=', now())->count(),
            'past' => Order::where('event_to', '<', now())->count(),
            
            // Payment Status
            'payment_pending' => Order::where('payment_status', 'pending')->count(),
            'payment_partial' => Order::where('payment_status', 'partial')->count(),
            'payment_paid' => Order::where('payment_status', 'paid')->count(),
            
            // Settlement Status
            'settlement_pending' => Order::where('settlement_status', 'pending')->count(),
            'settlement_settled' => Order::where('settlement_status', 'settled')->count(),
        ];

        // Financial Overview - Orders
        $orderFinancials = [
            'total_revenue' => Order::sum('total_amount'),
            'total_pending' => Order::where('payment_status', '!=', 'paid')->sum('final_payable'),
            'total_collected' => PaymentTransaction::sum('amount'),
            'advance_amount' => Order::where('payment_status', 'partial')->sum('final_payable'),
        ];

        // Today's Stats
        $todayStats = [
            'orders_created' => Order::whereDate('created_at', today())->count(),
            'payments_received' => PaymentTransaction::whereDate('created_at', today())->count(),
            'payment_amount_today' => PaymentTransaction::whereDate('created_at', today())->sum('amount'),
        ];

        // This Month Stats
        $thisMonthStats = [
            'orders_created' => Order::whereMonth('created_at', now()->month)
                                   ->whereYear('created_at', now()->year)->count(),
            'revenue' => Order::whereMonth('created_at', now()->month)
                            ->whereYear('created_at', now()->year)->sum('total_amount'),
            'payments_collected' => PaymentTransaction::whereMonth('created_at', now()->month)
                                                     ->whereYear('created_at', now()->year)
                                                     ->sum('amount'),
        ];

        // Recent Orders (Last 5)
        $recentOrders = Order::with('client')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        // Recent Dispatches (Last 5)
        $recentDispatches = Dispatch::with('client')
            ->orderByDesc('id')
            ->limit(5)
            ->get();

        // Upcoming Events (Next 7 days)
        $upcomingEvents = Order::whereBetween('event_from', [now(), now()->addDays(7)])
            ->orderBy('event_from')
            ->limit(5)
            ->get();

        // Orders with Pending Payments (Top 5 highest pending)
        $pendingPayments = Order::where('payment_status', '!=', 'paid')
            ->where('final_payable', '>', 0)
            ->orderByDesc('final_payable')
            ->limit(5)
            ->get();

        // Payment Method Breakdown (for orders)
        $paymentMethodStats = PaymentTransaction::select('payment_method', DB::raw('count(*) as count'), DB::raw('sum(amount) as total'))
            ->groupBy('payment_method')
            ->get();

        // Client Stats
        $clientStats = [
            'total' => Client::count(),
            'with_orders' => Order::distinct('client_phone')->count('client_phone'),
        ];

        // Monthly Revenue Chart Data (Last 6 months)
        $monthlyRevenue = Order::select(
            DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
            DB::raw('SUM(total_amount) as revenue'),
            DB::raw('COUNT(*) as orders')
        )
        ->where('created_at', '>=', now()->subMonths(6))
        ->groupBy('month')
        ->orderBy('month')
        ->get();

        return view('dashboard', compact(
            'dispatchStats',
            'dispatchPaymentStats',
            'orderStats',
            'orderFinancials',
            'todayStats',
            'thisMonthStats',
            'recentOrders',
            'recentDispatches',
            'upcomingEvents',
            'pendingPayments',
            'paymentMethodStats',
            'clientStats',
            'monthlyRevenue'
        ));
    }
}
