<?php

namespace App\Http\Controllers;

use App\Models\AdditionalServiceSubmission;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdditionalServiceSubmissionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function csv(Request $request): StreamedResponse
    {
        if (! $request->user()?->isAdmin()) {
            abort(403);
        }

        $submissions = AdditionalServiceSubmission::with('lead')
            ->orderByDesc('submitted_at')
            ->get();

        $filename = 'additional-service-submissions-' . now()->format('Y-m-d-His') . '.csv';

        return response()->streamDownload(function () use ($submissions) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Email', 'Submitted at', 'Services selected']);
            foreach ($submissions as $s) {
                $email = $s->lead?->email ?? '';
                $submittedAt = $s->submitted_at?->toIso8601String() ?? '';
                $services = implode(', ', array_values($s->selected_service_names));
                fputcsv($out, [$email, $submittedAt, $services]);
            }
            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}
