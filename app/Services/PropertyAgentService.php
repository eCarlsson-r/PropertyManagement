<?php
namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use App\Services\EmbeddingService;
use App\Concerns\InteractWithVertexAI;
use Google\Protobuf\Value;
use Google\Cloud\AIPlatform\V1\PredictionServiceClient;
use Google\Auth\Credentials\ServiceAccountCredentials;

class PropertyAgentService
{
    use InteractWithVertexAI; // Import the trait

    public function getToolsDefinitions(): array
    {
        return [
            [
                'function_declarations' => [
                    [
                        'name' => 'search_properties',
                        'description' => 'Find property details, maintenance history, and notes using vector search.',
                        'parameters' => [
                            'type' => 'object',
                            'properties' => ['query' => ['type' => 'string']]
                        ]
                    ],
                    [
                        'name' => 'search_tenants',
                        'description' => 'Retrieve tenant details, behavior, and lease issues.',
                        'parameters' => [
                            'type' => 'object',
                            'properties' => ['query' => ['type' => 'string']]
                        ]
                    ],
                    [
                        'name' => 'search_expenses',
                        'description' => 'Find expenses details and notes using vector search.',
                        'parameters' => [
                            'type' => 'object',
                            'properties' => ['query' => ['type' => 'string']]
                        ]
                    ],
                    [
                        'name' => 'search_rules',
                        'description' => 'Retrieve rules details and description using vector search.',
                        'parameters' => [
                            'type' => 'object',
                            'properties' => ['query' => ['type' => 'string']]
                        ]
                    ]
                ]
            ]
        ];
    }

    public function callVertexAI($messages)
    {
        $response = $this->postToVertex('gemini-2.5-flash:generateContent', [
            'contents' => $messages,
            'tools' => $this->getToolsDefinitions(),
            'generationConfig' => [
                'temperature' => 0.1
            ]
        ]);
        
        return $response->json();
    }

    private function buildContext($records, $table)
    {
        $context = "";

        foreach ($records as $r) {
            if ($table === 'tenants') {
                $context .= "Tenant: {$r->name}, Notes: {$r->notes}\n";
            } elseif ($table === 'expenses') {
                $context .= "Expense: {$r->title}, Amount: {$r->amount}, Notes: {$r->notes}\n";
            } elseif ($table === 'properties') {
                $context .= "Property: {$r->name}, Notes: {$r->notes}\n";
            } elseif ($table === 'rules') {
                $context .= "Rule: {$r->title}, Description: {$r->description}\n";
            }
        }

        $context = "Below is structured property management data:\n\n" . $context;
        return $context;
    }

    public function executeSubAgent($functionName, $args, EmbeddingService $embeddingService)
    {
        $table = str_replace('search_', '', $functionName);

        // This logic handles the "Sub-Agent" data retrieval
        $embedding = $embeddingService->generate($args['query']);
        if (!$embedding)
            return "No data found.";

        $vector = '[' . implode(',', $embedding) . ']';
        $records = DB::table($table)
            ->select('*', DB::raw("embedding <-> '{$vector}' as distance"))
            ->whereNotNull('embedding')
            ->orderBy('distance')
            ->get();

        if ($records->isEmpty()) {
            return "The search for {$table} returned no relevant records. Please inform the user that no specific data was found for this query.";
        } else {
            return $this->buildContext($records, $table);
        }
    }
}