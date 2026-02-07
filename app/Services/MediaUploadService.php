<?php

namespace App\Services;

use App\Models\Media;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;

class MediaUploadService
{
    public function upload(UploadedFile $file, ?int $venueId): Media
    {
        $diskName = config('filesystems.media_disk', 'spaces');
        $maxWidth = config('partyhelp.media.max_width', 1920);
        $format = config('partyhelp.media.output_format', 'webp');

        $image = Image::read($file->getRealPath());

        // Only reduce width if larger than max – scaleDown never upscales
        $image->scaleDown(width: $maxWidth);

        $encoded = match (strtolower($format)) {
            'webp' => $image->toWebp(),
            'jpg', 'jpeg' => $image->toJpeg(),
            'png' => $image->toPng(),
            default => $image->toWebp(),
        };

        $ext = strtolower($format) === 'jpeg' ? 'jpg' : strtolower($format);
        $filename = Str::uuid() . '.' . $ext;
        $directory = 'media/' . ($venueId ? "venues/{$venueId}" : 'shared');
        $path = $directory . '/' . $filename;

        Storage::disk($diskName)->put($path, (string) $encoded, [
            'visibility' => 'public',
        ]);

        $mimeTypes = [
            'webp' => 'image/webp',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
        ];

        return Media::create([
            'venue_id' => $venueId,
            'file_path' => $path,
            'file_name' => pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME) . '.' . $ext,
            'mime_type' => $mimeTypes[$ext] ?? 'image/webp',
            'size' => strlen((string) $encoded),
        ]);
    }
}
