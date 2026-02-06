<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessNewLead;
use App\Models\Lead;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class WebhookController extends Controller
{
    /**
     * Receive lead submissions from Elementor webhook.
     *
     * Expected payload from Elementor form:
     * - first_name, last_name, email, phone
     * - occasion_type, guest_count, preferred_date
     * - suburb, room_styles (comma-separated or array)
     * - budget_range (optional), special_requirements (optional)
     */
    public function handleElementorLead(Request $request): JsonResponse
    {
        Log::info('Elementor webhook received', $request->all());

        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|min:2|max:100',
            'last_name' => 'required|string|min:2|max:100',
            'email' => 'required|email',
            'phone' => 'required|string|min:8|max:20',
            'occasion_type' => 'required|string',
            'guest_count' => 'required|integer|min:10|max:500',
            'preferred_date' => 'required|date|after:today',
            'suburb' => 'required|string',
            'room_styles' => 'required',
            'budget_range' => 'nullable|string',
            'special_requirements' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();
        $roomStyles = $this->parseRoomStyles($data['room_styles']);

        $lead = Lead::create([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'occasion_type' => $data['occasion_type'],
            'guest_count' => $data['guest_count'],
            'preferred_date' => $data['preferred_date'],
            'suburb' => $data['suburb'],
            'room_styles' => $roomStyles,
            'budget_range' => $data['budget_range'] ?? null,
            'special_requirements' => $data['special_requirements'] ?? null,
            'status' => 'new',
            'webhook_payload' => $request->all(),
        ]);

        ProcessNewLead::dispatch($lead);

        return response()->json([
            'status' => 'success',
            'message' => 'Lead received and queued for processing',
            'lead_id' => $lead->id,
        ], 201);
    }

    private function parseRoomStyles(mixed $input): array
    {
        if (is_array($input)) {
            return $input;
        }

        return array_map('trim', explode(',', (string) $input));
    }
}
