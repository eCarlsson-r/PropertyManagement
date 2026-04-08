<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Google\Auth\Credentials\ServiceAccountCredentials;
use App\Concerns\InteractWithVertexAI;

class EmbeddingService
{
    use InteractWithVertexAI; // Import the same trait

    public function generate($text)
    {
        $response = $this->postToVertex('text-embedding-004:predict', [
            'instances' => [[
                'task_type' => 'RETRIEVAL_DOCUMENT',
                'content' => $text
            ]]
        ]);

        return $response->json()['predictions'][0]['embeddings']['values'] ?? [];
    }
}