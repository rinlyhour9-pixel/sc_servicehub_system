<?php

namespace App\Http\Controllers;

use App\Models\ServiceRequest;
use App\Models\Customer;
use App\Models\Technician;
use App\Models\Invoice;
use App\Models\ServiceCategory;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $baseQuery = ServiceRequest::query();

        $counts = [
            'pending' => (clone $baseQuery)->where('status', ServiceRequest::STATUS_PENDING)->count(),
            'assigned' => (clone $baseQuery)->where('status', ServiceRequest::STATUS_ASSIGNED)->count(),
            'in_progress' => (clone $baseQuery)->where('status', ServiceRequest::STATUS_IN_PROGRESS)->count(),
            'completed_this_month' => (clone $baseQuery)->where('status', ServiceRequest::STATUS_COMPLETED)
                ->whereMonth('completed_at', now()->month)->count(),
        ];

        $todaysSchedule = (clone $baseQuery)
            ->whereDate('scheduled_at', now()->toDateString())
            ->whereNotIn('status', [ServiceRequest::STATUS_COMPLETED, ServiceRequest::STATUS_CANCELLED])
            ->with(['customer', 'technician', 'category'])
            ->orderBy('scheduled_at')
            ->get();

        $recentTickets = (clone $baseQuery)
            ->with(['customer', 'technician'])
            ->latest()
            ->take(8)
            ->get();

        $urgentTickets = (clone $baseQuery)
            ->where('priority', ServiceRequest::PRIORITY_URGENT)
            ->with(['customer', 'technician', 'category'])
            ->latest()
            ->take(5)
            ->get();

        $technicianPerformance = Technician::with(['assignedServiceRequests' => function ($query) {
                $query->select('id', 'assigned_technician_id', 'status');
            }])
            ->get()
            ->map(function ($technician) {
                $technician->open_jobs = $technician->assignedServiceRequests->whereIn('status', [
                    ServiceRequest::STATUS_PENDING,
                    ServiceRequest::STATUS_ASSIGNED,
                    ServiceRequest::STATUS_IN_PROGRESS,
                ])->count();
                $technician->completed_jobs = $technician->assignedServiceRequests->where('status', ServiceRequest::STATUS_COMPLETED)->count();
                return $technician;
            })
            ->sortByDesc('open_jobs')
            ->values();

        $categorySummary = ServiceCategory::withCount('serviceRequests')->orderByDesc('service_requests_count')->get();

        $overview = [
            'customers' => Customer::count(),
            'technicians' => Technician::where('is_active', true)->count(),
            'unpaid_invoices' => Invoice::where('status', Invoice::STATUS_UNPAID)->count(),
        ];
        $range = $request->input('range', 'month');
        $start = match ($range) {
            'day' => now()->subDay(),
            'year' => now()->subYear(),
            default => now()->subMonth(),
        };

        $labelFormat = match ($range) {
            'day' => 'M j, g:i A',
            'year' => 'M',
            default => 'M j',
        };

        $incomeTypes = ['sale', 'receivable', 'delivery_payment'];

        $walletRows = WalletTransaction::where('created_at', '>=', $start)->get();

        $chartRows = $walletRows->groupBy(fn ($row) => $row->created_at->format($labelFormat));

        $chart = [
            'labels' => $chartRows->keys()->values(),
            'income' => $chartRows->map(fn ($rows) => $rows->whereIn('type', $incomeTypes)->sum('amount'))->values(),
        ];

        $incomeTransactions = $walletRows->whereIn('type', $incomeTypes)->sortByDesc('created_at')->values();

        return view('dashboard', compact('counts', 'todaysSchedule', 'recentTickets', 'urgentTickets', 'technicianPerformance', 'categorySummary', 'overview', 'range', 'chart', 'incomeTransactions'));
    }
}
