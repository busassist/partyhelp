<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\FilesystemException;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MediaServeController extends Controller
{
    public function show(Request $request, string $path): StreamedResponse
    {
        $path = trim($path, '/');

        if (str_contains($path, '..') || str_starts_with($path, '/')) {
            abort(404);
        }

        $disk = Storage::disk(config('filesystems.media_disk', 'spaces'));

        try {
            $stream = $disk->readStream($path);
        } catch (FilesystemException $e) {
            abort(404);
        }

        if (! is_resource($stream)) {
            abort(404);
        }

        $mimeType = $disk->mimeType($path) ?: 'application/octet-stream';

        return response()->streamDownload(
            function () use ($stream) {
                fpassthru($stream);
                fclose($stream);
            },
            basename($path),
            ['Content-Type' => $mimeType],
            'inline'
        );
    }
}
