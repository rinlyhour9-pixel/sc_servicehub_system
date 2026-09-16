<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $customer = $request->user('client');

        $serviceRequests = $customer->serviceRequests()
            ->with(['category', 'technician', 'invoice.items'])
            ->latest()
            ->get();

        return view('client.dashboard', compact('customer', 'serviceRequests'));
    }
}
