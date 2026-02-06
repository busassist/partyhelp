<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\Venue;
use App\Services\LeadPurchaseService;
use Illuminate\Http\Request;

class LeadPurchaseController extends Controller
{
    public function show(Request $request, Lead $lead, Venue $venue)
    {
        if (! $lead->isAvailable()) {
            return view('customer.lead-unavailable', [
                'lead' => $lead,
            ]);
        }

        return view('customer.lead-purchase', [
            'lead' => $lead,
            'venue' => $venue,
        ]);
    }
}
