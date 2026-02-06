<?php

namespace App\Http\Controllers;

use App\Models\FunctionPack;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class FunctionPackController extends Controller
{
    public function show(string $token)
    {
        $pack = FunctionPack::where('download_token', $token)
            ->where('is_active', true)
            ->with('venue')
            ->firstOrFail();

        return view('customer.function-pack', [
            'pack' => $pack,
            'venue' => $pack->venue,
        ]);
    }

    public function download(string $token)
    {
        $pack = FunctionPack::where('download_token', $token)
            ->where('is_active', true)
            ->firstOrFail();

        if (! Storage::exists($pack->file_path)) {
            abort(404, 'File not found');
        }

        return Storage::download(
            $pack->file_path,
            $pack->file_name,
            ['Content-Type' => $pack->mime_type]
        );
    }
}
