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
    private const FIELD_ALIASES = [
        'first_name' => ['First_Name', 'firstName', 'firstname'],
        'last_name' => ['Last_Name', 'lastName', 'lastname'],
        'email' => ['Email'],
        'phone' => ['Phone'],
        'occasion_type' => ['Select_the_type_of_occasion', 'occasion', 'Occasion_Type'],
        'guest_count' => ['Number_of_Guests', 'guest_count', 'guests'],
        'preferred_date' => ['Preferred_Date', 'preferred_date', 'date'],
        'suburb' => ['Select_preferred_location', 'suburb', 'Suburb', 'location', 'locations'],
        'room_styles' => ['room_styles', 'Room_Styles', 'room_styles_preference'],
        'budget_range' => ['Estimated_Budget', 'budget_range', 'Budget_Range', 'budget'],
        'special_requirements' => ['Other_details_about_the_party:', 'Other_details_about_the_party', 'special_requirements', 'Special_Requirements', 'notes', 'message'],
    ];

    private const OCCASION_MAP = [
        '21st' => '21st_birthday', '30th' => '30th_birthday', '40th' => '40th_birthday',
        '50th' => '50th_birthday', '60th' => '60th_birthday',
        'engagement' => 'engagement_party', 'wedding' => 'wedding_reception',
        'corporate' => 'corporate_function', 'christmas' => 'christmas_party',
        'farewell' => 'farewell_party', 'baby shower' => 'baby_shower',
    ];

    /**
     * Receive lead submissions from Elementor webhook.
     * Accepts multiple field name variations for Elementor form compatibility.
     */
    public function handleElementorLead(Request $request): JsonResponse
    {
        $start = microtime(true);
        $raw = $request->all();

        Log::info('Elementor webhook received', [
            'payload_keys' => array_keys($raw),
            'payload' => $raw,
        ]);

        try {
            $data = $this->normalizePayload($raw);

            Log::info('Elementor webhook normalized', [
                'normalized_keys' => array_keys($data),
                'normalized' => $data,
            ]);

            $validator = Validator::make($data, [
                'first_name' => 'required|string|min:2|max:100',
                'last_name' => 'required|string|min:2|max:100',
                'email' => 'required|email',
                'phone' => 'required|string|min:8|max:20',
                'occasion_type' => 'required|string',
                'guest_count' => 'required|integer|min:10|max:500',
                'preferred_date' => 'required|date|after:today',
                'suburb' => 'required|string',
                'room_styles' => 'nullable',
                'budget_range' => 'nullable|string',
                'special_requirements' => 'nullable|string|max:500',
            ]);

            if ($validator->fails()) {
                $errors = $validator->errors()->toArray();
                Log::warning('Elementor webhook validation failed', [
                    'failed_fields' => array_keys($errors),
                    'errors' => $errors,
                    'normalized' => $data,
                    'raw' => $raw,
                    'elapsed_ms' => round((microtime(true) - $start) * 1000),
                ]);

                $response = response()->json([
                    'success' => false,
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                ], 422);

                Log::info('Elementor webhook response', [
                    'http_status' => 422,
                    'elapsed_ms' => round((microtime(true) - $start) * 1000),
                ]);

                return $response;
            }

            $data = $validator->validated();
            $roomStyles = $this->parseRoomStyles($data['room_styles'] ?? ['function_room']);

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
                'webhook_payload' => $raw,
            ]);

            ProcessNewLead::dispatch($lead);

            $elapsedMs = round((microtime(true) - $start) * 1000);

            Log::info('Elementor webhook success', [
                'lead_id' => $lead->id,
                'email' => $lead->email,
                'elapsed_ms' => $elapsedMs,
            ]);

            $response = response()->json([
                'success' => true,
                'status' => 'success',
                'message' => 'Lead received and queued for processing',
                'lead_id' => $lead->id,
            ], 200);

            Log::info('Elementor webhook response', [
                'http_status' => 200,
                'lead_id' => $lead->id,
                'elapsed_ms' => $elapsedMs,
            ]);

            return $response;
        } catch (\Throwable $e) {
            Log::error('Elementor webhook exception', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'elapsed_ms' => round((microtime(true) - $start) * 1000),
                'raw' => $raw,
            ]);

            return response()->json([
                'success' => false,
                'status' => 'error',
                'message' => 'An error occurred processing the lead',
            ], 500);
        }
    }

    private function normalizePayload(array $raw): array
    {
        $data = [];
        foreach (self::FIELD_ALIASES as $canonical => $aliases) {
            $value = $raw[$canonical] ?? null;
            foreach ($aliases as $alias) {
                if ($value === null && isset($raw[$alias])) {
                    $value = $raw[$alias];
                    break;
                }
            }
            if ($value !== null && $value !== '') {
                $data[$canonical] = $value;
            }
        }

        $data = $this->transformValues($data);

        $styles = $data['room_styles'] ?? null;
        if (empty($styles) || (is_array($styles) && empty($styles))) {
            $data['room_styles'] = ['function_room'];
        }

        return $data;
    }

    private function transformValues(array $data): array
    {
        if (isset($data['guest_count']) && ! is_numeric($data['guest_count'])) {
            $data['guest_count'] = $this->parseGuestCount($data['guest_count']);
        }

        if (isset($data['occasion_type'])) {
            $data['occasion_type'] = $this->mapOccasionType($data['occasion_type']);
        }

        if (isset($data['suburb'])) {
            $suburb = $data['suburb'];
            if (is_array($suburb)) {
                $suburb = $suburb[0] ?? '';
            }
            $suburb = (string) $suburb;
            if (str_contains($suburb, ' - ')) {
                $parts = explode(' - ', $suburb, 2);
                $suburb = trim($parts[1] ?? $parts[0]);
                $comma = strpos($suburb, ',');
                if ($comma !== false) {
                    $suburb = trim(substr($suburb, 0, $comma));
                }
            }
            $data['suburb'] = $suburb;
        }

        return $data;
    }

    private function parseGuestCount(mixed $input): int
    {
        $s = (string) $input;
        if (preg_match('/(\d+)\s*-\s*(\d+)/', $s, $m)) {
            return (int) round(((int) $m[1] + (int) $m[2]) / 2);
        }
        if (preg_match('/(\d+)/', $s, $m)) {
            return (int) $m[1];
        }

        return 50;
    }

    private function mapOccasionType(string $value): string
    {
        $v = strtolower(trim($value));
        foreach (self::OCCASION_MAP as $key => $mapped) {
            if (str_contains($v, $key) || $v === $key) {
                return $mapped;
            }
        }
        if (in_array($v, array_values(self::OCCASION_MAP))) {
            return $v;
        }

        return str_replace(' ', '_', $v) ?: 'other';
    }

    private function parseRoomStyles(mixed $input): array
    {
        if (is_array($input)) {
            return $input;
        }

        return array_map('trim', explode(',', (string) $input));
    }
}
