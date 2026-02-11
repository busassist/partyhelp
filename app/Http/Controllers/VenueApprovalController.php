<?php

namespace App\Http\Controllers;

use App\Models\Venue;
use Illuminate\Http\Request;

class VenueApprovalController extends Controller
{
    public function approve(Request $request, Venue $venue)
    {
        if ($venue->status !== 'pending') {
            return view('admin.venue-already-processed', [
                'venue' => $venue,
            ]);
        }

        $venue->update([
            'status' => 'active',
            'approved_at' => now(),
        ]);

        \App\Jobs\SendVenueRegistrationApprovedEmail::dispatch($venue);

        return view('admin.venue-approved', [
            'venue' => $venue,
        ]);
    }

    public function reject(Request $request, Venue $venue)
    {
        if ($venue->status !== 'pending') {
            return view('admin.venue-already-processed', [
                'venue' => $venue,
            ]);
        }

        $venue->update(['status' => 'inactive']);

        // TODO: Send rejection email to venue

        return view('admin.venue-rejected', [
            'venue' => $venue,
        ]);
    }
}
