<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Url;
use Illuminate\Support\Str;

class UrlController extends Controller
{
    protected $base_url = "http://short.est/";

    public function encode(Request $request)
    {

        $request->validate([
            'url' => 'required|url'
        ]);

        $originalUrl = $request->url;
        //$shortCode = Str::random(6);
        $shortCode = $this->generateUniqueShortCode();
        do {
            $shortCode = Str::random(6);
        } while (Url::where('short_code', $shortCode)->exists());
        $url = Url::create([
            'original_url' => $originalUrl,
            'short_code' => $shortCode,
        ]);

        return response()->json([
            'short_url' => $this->base_url . $shortCode
        ]);
    }


    private function generateUniqueShortCode()
    {
        do {
            // Generate a random 6-character string
            $shortCode = Str::random(6);
        } while (Url::where('short_code', $shortCode)->exists());

        return $shortCode;
    }


    public function decode(Request $request)
    {
        $request->validate([
            'short_url' => 'required|url'
        ]);

        $shortCode = str_replace($this->base_url, '', $request->short_url);
        // dd($shortCode);
        $originalUrl = Url::where('short_code', $shortCode)->first();

        if (!$originalUrl) {
            return response()->json(['error' => 'URL not found'], 404);
        }

        return response()->json([
            'original_url' => $originalUrl
        ]);
    }
}
