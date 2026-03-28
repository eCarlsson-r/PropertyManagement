<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class EmbeddingService
{
    public function generate($text)
    {
        $response = Http::withToken(config('services.vertex.token'))
            ->post('https://us-central1-aiplatform.googleapis.com/v1/projects/YOUR_PROJECT/locations/us-central1/publishers/google/models/textembedding-gecko:predict', [
                'instances' => [
                    ['content' => $text]
                ]
            ]);

        return $response->json()['predictions'][0]['embeddings']['values'] ?? null;
    }
}