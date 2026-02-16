<?php

namespace App\Http\Controllers;

use App\Models\AdditionalService;
use App\Models\AdditionalServiceSubmission;
use App\Models\Lead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\View\View;

class AdditionalServicesController extends Controller
{
    public function show(Lead $lead): View
    {
        $services = AdditionalService::ordered()->get()->map(fn ($s) => [
            'id' => $s->id,
            'name' => $s->name,
            'thumbnail_url' => $s->thumbnail_url,
        ])->all();

        return view('customer.additional-services', [
            'lead' => $lead,
            'services' => $services,
            'submitUrl' => URL::signedRoute('additional-services.submit', ['lead' => $lead]),
        ]);
    }

    public function submit(Request $request, Lead $lead): View
    {
        $serviceIds = $request->input('services', []);
        $ids = is_array($serviceIds) ? array_map('intval', array_filter($serviceIds)) : [];

        AdditionalServiceSubmission::create([
            'lead_id' => $lead->id,
            'submitted_at' => now(),
            'selected_service_ids' => $ids,
        ]);

        return view('customer.additional-services-thank-you');
    }
}
