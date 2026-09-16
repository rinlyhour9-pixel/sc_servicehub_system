<?php

namespace App\Http\Controllers;

use App\Models\DailyOperation;
use App\Models\Invoice;
use App\Models\ServiceRequest;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $invoices = Invoice::query()
            ->with(['serviceRequest.customer', 'items', 'walletTransaction'])
            ->when($request->status, fn ($q, $status) => $q->where('status', $status))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('invoices.index', compact('invoices'));
    }

    public function create(Request $request)
    {
        $serviceRequests = ServiceRequest::whereDoesntHave('invoice')
            ->with('customer')
            ->orderByDesc('created_at')
            ->get();

        $selectedServiceRequestId = $request->integer('service_request_id') ?: null;

        return view('invoices.create', compact('serviceRequests', 'selectedServiceRequestId'));
    }

    public function store(Request $request)
    {
        // Drop rows the user left blank before validating.
        $items = collect($request->input('items', []))
            ->filter(fn ($item) => filled($item['description'] ?? null))
            ->values()
            ->all();
        $request->merge(['items' => $items]);

        $data = $request->validate([
            'service_request_id' => ['required', 'exists:service_requests,id', 'unique:invoices,service_request_id'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.description' => ['required', 'string', 'max:255'],
            'items.*.quantity' => ['required', 'numeric', 'min:0.01'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
        ]);

        $invoice = Invoice::create([
            'service_request_id' => $data['service_request_id'],
            'status' => Invoice::STATUS_UNPAID,
        ]);

        foreach ($data['items'] as $item) {
            $invoice->items()->create($item);
        }

        return redirect()->route('invoices.show', $invoice)->with('status', 'Invoice created.');
    }

    public function show(Invoice $invoice)
    {
        $invoice->load(['serviceRequest.customer', 'items', 'walletTransaction']);

        return view('invoices.show', compact('invoice'));
    }

    public function recordPayment(Request $request, Invoice $invoice)
    {
        abort_if($invoice->status !== Invoice::STATUS_UNPAID, 422, 'Only unpaid invoices can be marked as paid.');

        $data = $request->validate([
            'payment_method' => ['required', 'in:'.implode(',', WalletTransaction::METHODS)],
        ]);

        $day = DailyOperation::whereDate('business_date', today())->whereNull('closed_at')->first();

        if (! $day) {
            return back()->with('error', "Open today's cash drawer under Open / End Day before recording a payment.");
        }

        $day->walletTransactions()->create([
            'created_by' => $request->user()->id,
            'invoice_id' => $invoice->id,
            'type' => 'sale',
            'payment_method' => $data['payment_method'],
            'amount' => $invoice->total(),
            'description' => 'Payment for invoice '.$invoice->invoice_number,
        ]);

        $invoice->update([
            'status' => Invoice::STATUS_PAID,
            'paid_at' => now(),
        ]);

        return redirect()->route('invoices.show', $invoice)->with('status', 'Payment recorded.');
    }

    public function edit(Invoice $invoice)
    {
        $invoice->load(['serviceRequest.customer', 'items']);

        return view('invoices.edit', compact('invoice'));
    }

    public function update(Request $request, Invoice $invoice)
    {
        // Drop rows the user left blank before validating.
        $items = collect($request->input('items', []))
            ->filter(fn ($item) => filled($item['description'] ?? null))
            ->values()
            ->all();
        $request->merge(['items' => $items]);

        // Paid invoices are only ever set via recordPayment(), so a payment
        // always produces a matching wallet transaction — never just a status flip.
        $allowedStatuses = $invoice->status === Invoice::STATUS_PAID
            ? [Invoice::STATUS_PAID]
            : [Invoice::STATUS_UNPAID, Invoice::STATUS_CANCELLED];

        $data = $request->validate([
            'status' => ['required', 'in:'.implode(',', $allowedStatuses)],
            'items' => ['required', 'array', 'min:1'],
            'items.*.id' => ['nullable', 'exists:invoice_items,id'],
            'items.*.description' => ['required', 'string', 'max:255'],
            'items.*.quantity' => ['required', 'numeric', 'min:0.01'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
        ]);

        $invoice->status = $data['status'];
        $invoice->save();

        $keepIds = [];
        foreach ($data['items'] as $item) {
            if (! empty($item['id'])) {
                $invoice->items()->where('id', $item['id'])->update([
                    'description' => $item['description'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                ]);
                $keepIds[] = $item['id'];
            } else {
                $new = $invoice->items()->create($item);
                $keepIds[] = $new->id;
            }
        }
        $invoice->items()->whereNotIn('id', $keepIds)->delete();

        return redirect()->route('invoices.show', $invoice)->with('status', 'Invoice updated.');
    }

    public function destroy(Invoice $invoice)
    {
        $invoice->delete();

        return redirect()->route('invoices.index')->with('status', 'Invoice deleted.');
    }
}
