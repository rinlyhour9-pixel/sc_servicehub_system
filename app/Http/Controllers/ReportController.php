<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\DailyOperation;
use App\Models\Invoice;
use App\Models\ServiceRequest;
use App\Models\Technician;
use App\Models\WalletTransaction;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    private const INCOME_TYPES = ['sale', 'receivable', 'delivery_payment'];

    public function index()
    {
        return view('reports.index');
    }

    public function revenue(Request $request)
    {
        [$from, $to] = $this->resolveRange($request);

        $rows = WalletTransaction::whereBetween('created_at', [$from, $to])->get();

        $income = $rows->whereIn('type', self::INCOME_TYPES)->sum('amount');
        $expense = $rows->where('type', 'expense')->sum('amount');
        $refunds = $rows->where('type', 'refund')->sum('amount');
        $cash = $rows->whereIn('type', self::INCOME_TYPES)->where('payment_method', 'cash')->sum('amount');
        $bank = $rows->whereIn('type', self::INCOME_TYPES)->where('payment_method', 'bank_qr')->sum('amount');

        $daily = $rows->groupBy(fn ($r) => $r->created_at->format('Y-m-d'))
            ->map(fn ($dayRows, $date) => [
                'date' => $date,
                'income' => $dayRows->whereIn('type', self::INCOME_TYPES)->sum('amount'),
                'expense' => $dayRows->where('type', 'expense')->sum('amount'),
            ])
            ->sortKeys()
            ->values();

        return view('reports.revenue', compact('from', 'to', 'income', 'expense', 'refunds', 'cash', 'bank', 'daily'));
    }

    public function serviceRequests(Request $request)
    {
        [$from, $to] = $this->resolveRange($request);

        $requests = ServiceRequest::whereBetween('created_at', [$from, $to])->with('category')->get();

        $byStatus = $requests->groupBy('status')->map->count();
        $byPriority = $requests->groupBy('priority')->map->count();
        $byCategory = $requests->groupBy(fn ($r) => $r->category->name ?? 'Uncategorized')->map->count()->sortDesc();

        $completed = $requests->where('status', ServiceRequest::STATUS_COMPLETED)->whereNotNull('completed_at');
        $avgHours = $completed->isNotEmpty()
            ? round($completed->avg(fn ($r) => abs($r->created_at->diffInHours($r->completed_at))), 1)
            : null;

        return view('reports.service-requests', compact('from', 'to', 'requests', 'byStatus', 'byPriority', 'byCategory', 'avgHours'));
    }

    public function technicians(Request $request)
    {
        [$from, $to] = $this->resolveRange($request);

        $technicians = Technician::with(['assignedServiceRequests' => function ($q) use ($from, $to) {
            $q->whereBetween('created_at', [$from, $to]);
        }])->get()->map(function ($tech) {
            $jobs = $tech->assignedServiceRequests;
            $completedCount = $jobs->where('status', ServiceRequest::STATUS_COMPLETED)->count();
            $tech->total_jobs = $jobs->count();
            $tech->completed_jobs = $completedCount;
            $tech->open_jobs = $jobs->count() - $completedCount;
            $tech->completion_rate = $jobs->count() ? round($completedCount / $jobs->count() * 100) : 0;
            return $tech;
        })->sortByDesc('completed_jobs')->values();

        return view('reports.technicians', compact('from', 'to', 'technicians'));
    }

    public function customers(Request $request)
    {
        [$from, $to] = $this->resolveRange($request);

        $customers = Customer::with(['serviceRequests' => function ($q) use ($from, $to) {
            $q->whereBetween('created_at', [$from, $to])->with('invoice.items');
        }])->get()->map(function ($customer) {
            $requests = $customer->serviceRequests;
            $customer->request_count = $requests->count();
            $customer->completed_count = $requests->where('status', ServiceRequest::STATUS_COMPLETED)->count();
            $customer->revenue = $requests->sum(fn ($r) => $r->invoice ? $r->invoice->total() : 0);
            $customer->last_service_at = $requests->max('created_at');
            return $customer;
        })->filter(fn ($c) => $c->request_count > 0)->sortByDesc('revenue')->values();

        return view('reports.customers', compact('from', 'to', 'customers'));
    }

    public function invoices(Request $request)
    {
        [$from, $to] = $this->resolveRange($request);

        $invoices = Invoice::whereBetween('issued_at', [$from, $to])
            ->with(['items', 'serviceRequest.customer'])
            ->latest('issued_at')
            ->get();

        $paid = $invoices->where('status', Invoice::STATUS_PAID);
        $unpaid = $invoices->where('status', Invoice::STATUS_UNPAID);

        $totalInvoiced = $invoices->sum(fn ($i) => $i->total());
        $totalPaid = $paid->sum(fn ($i) => $i->total());
        $totalOutstanding = $unpaid->sum(fn ($i) => $i->total());

        $aging = ['0-7 days' => 0.0, '8-30 days' => 0.0, '30+ days' => 0.0];
        foreach ($unpaid as $invoice) {
            $days = $invoice->issued_at->diffInDays(now());
            $bucket = $days <= 7 ? '0-7 days' : ($days <= 30 ? '8-30 days' : '30+ days');
            $aging[$bucket] += $invoice->total();
        }

        return view('reports.invoices', compact('from', 'to', 'invoices', 'totalInvoiced', 'totalPaid', 'totalOutstanding', 'aging'));
    }

    public function cash(Request $request)
    {
        [$from, $to] = $this->resolveRange($request);

        $days = DailyOperation::whereBetween('business_date', [$from->toDateString(), $to->toDateString()])
            ->with(['opener', 'closer'])
            ->orderByDesc('business_date')
            ->get();

        $totalDiff = $days->sum('cash_difference');
        $daysWithDiscrepancy = $days->filter(fn ($d) => ! is_null($d->cash_difference) && (float) $d->cash_difference !== 0.0)->count();

        return view('reports.cash', compact('from', 'to', 'days', 'totalDiff', 'daysWithDiscrepancy'));
    }

    private function resolveRange(Request $request): array
    {
        $from = $request->filled('from') ? Carbon::parse($request->input('from'))->startOfDay() : now()->subDays(29)->startOfDay();
        $to = $request->filled('to') ? Carbon::parse($request->input('to'))->endOfDay() : now()->endOfDay();

        return [$from, $to];
    }
}
