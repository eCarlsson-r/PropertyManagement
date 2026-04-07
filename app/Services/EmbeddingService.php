<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class EmbeddingService
{
    public function generate($text)
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . config('services.gemini.key'),
            'Content-Type' => 'application/json',
        ])->post(
                'https://generativelanguage.googleapis.com/v1beta/models/text-embedding-004:embedContent',
                [
                    'model' => 'models/text-embedding-004',
                    'content' => [
                        'parts' => [
                            ['text' => $text]
                        ]
                    ]
                ]
            );

        return $response->json()['embedding']['values'] ?? null;
    }
}