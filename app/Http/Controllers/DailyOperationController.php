<?php

namespace App\Http\Controllers;

use App\Models\DailyOperation;
use App\Http\Controllers\WalletController;
use Illuminate\Http\Request;

class DailyOperationController extends Controller
{
    public function index()
    {
        $today = DailyOperation::with(['opener', 'closer'])->whereDate('business_date', today())->first();
        $history = DailyOperation::with(['opener', 'closer'])->latest('business_date')->take(14)->get();
        $summary = app(WalletController::class)->summary($today);
        return view('daily-operations.index', compact('today', 'history', 'summary'));
    }

    public function open(Request $request)
    {
        $data=$request->validate(['opening_cash'=>['required','numeric','min:0'],'opening_notes'=>['nullable','string','max:2000']]);
        DailyOperation::firstOrCreate(['business_date' => today()->toDateString()], [
            'opened_by' => $request->user()->id, 'opened_at' => now(), 'opening_cash'=>$data['opening_cash'], 'opening_notes' => $data['opening_notes'],
        ]);
        return back()->with('status', 'Business day opened.');
    }

    public function close(Request $request)
    {
        $request->validate(['closing_notes' => ['nullable', 'string', 'max:2000'], 'actual_cash'=>['required','numeric','min:0']]);
        $today = DailyOperation::whereDate('business_date', today())->firstOrFail();
        abort_unless($today->isOpen(), 422, 'Today is already closed.');
        $summary=app(WalletController::class)->summary($today); $actual=(float)$request->actual_cash;
        $today->update(['closed_by' => $request->user()->id, 'closed_at' => now(), 'closing_notes' => $request->closing_notes, 'expected_cash'=>$summary['expected_cash'], 'actual_cash'=>$actual, 'cash_difference'=>$actual-$summary['expected_cash']]);
        return back()->with('status', 'Business day closed.');
    }
}
