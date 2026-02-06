<?php

namespace App\Services;

use App\Models\Postcode;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PostcodeCsvService
{
    private const CSV_HEADERS = ['suburb', 'postcode', 'state'];

    public function downloadAsCsvFile(): string
    {
        $postcodes = Postcode::orderBy('suburb')->get();
        $path = 'temp/postcode-export/postcodes-' . now()->format('Y-m-d-His') . '.csv';
        $fullPath = Storage::disk(config('filesystems.default'))->path($path);

        Storage::disk(config('filesystems.default'))->makeDirectory(dirname($path));

        $handle = fopen($fullPath, 'w');
        fputcsv($handle, self::CSV_HEADERS);

        foreach ($postcodes as $p) {
            fputcsv($handle, [$p->suburb, $p->postcode, $p->state]);
        }

        fclose($handle);

        return $fullPath;
    }

    public function replaceFromCsv(string $filePath): array
    {
        $handle = fopen($filePath, 'r');
        $headers = fgetcsv($handle);

        if (!$headers || !$this->validHeaders($headers)) {
            fclose($handle);

            return ['success' => false, 'message' => 'Invalid CSV. Expected headers: suburb, postcode, state'];
        }

        $suburbIdx = array_search('suburb', $headers);
        $postcodeIdx = array_search('postcode', $headers);
        $stateIdx = array_search('state', $headers);

        if ($suburbIdx === false || $postcodeIdx === false) {
            fclose($handle);

            return ['success' => false, 'message' => 'CSV must contain suburb and postcode columns'];
        }

        $rows = [];
        $line = 2;

        while (($row = fgetcsv($handle)) !== false) {
            $suburb = trim($row[$suburbIdx] ?? '');
            $postcode = trim($row[$postcodeIdx] ?? '');

            if ($suburb === '' && $postcode === '') {
                continue;
            }

            if ($suburb === '' || $postcode === '') {
                fclose($handle);

                return ['success' => false, 'message' => "Row {$line}: suburb and postcode are required"];
            }

            $now = now();
            $rows[] = [
                'suburb' => $suburb,
                'postcode' => $postcode,
                'state' => $stateIdx !== false ? trim($row[$stateIdx] ?? 'VIC') : 'VIC',
                'sort_order' => count($rows),
                'created_at' => $now,
                'updated_at' => $now,
            ];
            $line++;
        }

        fclose($handle);

        DB::transaction(function () use ($rows) {
            Postcode::query()->delete();

            foreach (array_chunk($rows, 100) as $chunk) {
                Postcode::insert($chunk);
            }
        });

        return ['success' => true, 'message' => count($rows) . ' postcodes imported'];
    }

    private function validHeaders(array $headers): bool
    {
        $normalized = array_map(fn ($h) => strtolower(trim($h)), $headers);

        return in_array('suburb', $normalized) && in_array('postcode', $normalized);
    }
}
