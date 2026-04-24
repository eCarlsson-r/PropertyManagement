<?php
namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
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
                        'name' => 'search_receipts',
                        'description' => 'Check payment history and outstanding balances. Use this when analyzing financial risk or tenant stability.',
                        'parameters' => [
                            'type' => 'object',
                            'properties' => [
                                'query' => ['type' => 'string', 'description' => 'e.g., "Marcus Aurelius payment history"'],
                                'tenant_id' => ['type' => 'integer', 'description' => 'Optional tenant ID for direct lookup']
                            ]
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
        // Add a System Instruction as the first message if not present
        $systemInstruction = [
            'role' => 'user', 
            'parts' => [['text' => "You are the UrusProperti Lead Analyst. 
                Rules:
                1. If searching for expenses, prioritize 'Emergency' or 'Overhaul' titles and high COST_AMOUNTS.
                2. If a tenant has notes about late payments, YOU MUST explicitly call 'search_receipts' with their tenant_id to find the exact REMAINING_BALANCE.
                3. DO NOT say 'amounts could not be retrieved'. If you see a REMAINING_BALANCE in the data, you MUST report the exact IDR figure.
                4. When a property has a massive expense (like an elevator repair) AND a tenant owes money, emphasize that cash-flow is critical.
                5. Recommended Action: Always suggest a specific next step including the tenant's name and the amount they owe.
                6. If you are analyzing a tenant's risk, you ARE PROHIBITED from saying 'balance could not be retrieved' until you have explicitly called 'search_receipts' using the tenant's name or ID.
                7. CRITICAL: Before finalizing a Recommended Action, search the 'rules' table to see if any policies apply to the current financial or tenant situation. If a policy exists, cite it by name."]]
        ];

        array_unshift($messages, $systemInstruction);

        $response = $this->postToVertex('gemini-2.5-flash:generateContent', [
            'contents' => $messages,
            'tools' => $this->getToolsDefinitions(),
            'generationConfig' => [
                'temperature' => 0.0 // Set to 0 for consistent financial reasoning
            ]
        ]);
        
        return $response->json();
    }

    private function buildContext($records, $table)
    {
        $context = "";

        foreach ($records as $r) {
            if ($table === 'tenants') {
                $property = DB::table('properties')
                    ->join('units', 'properties.id', '=', 'units.property_id')
                    ->where('units.id', $r->unit_id)
                    ->select('properties.name')->first();
                
                $pName = $property ? $property->name : 'Unknown Property';
                $context .= "TENANT_RECORD: Name: {$r->name} | Property: {$pName} | History: {$r->notes}\n";

            } elseif ($table === 'receipts') {
                // Fetch property/unit info for the receipt context
                $info = DB::table('receipts')
                    ->join('units', 'receipts.unit_id', '=', 'units.id')
                    ->join('properties', 'units.property_id', '=', 'properties.id')
                    ->where('receipts.id', $r->id)
                    ->select('properties.name as p_name', 'units.name as u_name')
                    ->first();

                $status = $r->fully_paid ? 'PAID_IN_FULL' : 'UNPAID_DEBT';
                $context .= "FINANCIAL_RECEIPT: Property: " . ($info->p_name ?? 'Unknown') . 
                            " | Unit: " . ($info->u_name ?? 'N/A') . 
                            " | Status: {$status} | TOTAL_DUE: " . number_format($r->total, 2) . 
                            " | REMAINING_BALANCE: " . number_format($r->remaining, 2) . 
                            " | Cycle: {$r->receipt_cycle}\n";

            } elseif ($table === 'expenses') {
                $property = DB::table('properties')->where('id', $r->property_id)->first();
                $context .= "PROPERTY_EXPENSE: Property: " . ($property->name ?? 'Unknown') . 
                            " | Title: {$r->title} | COST: " . number_format($r->amount, 2) . 
                            " | Details: {$r->notes}\n";

            } elseif ($table === 'rules') {
                $context .= "LEGAL_RULE: {$r->title}: {$r->description}\n";
            }
        }

        return "CRITICAL PROPERTY DATA FEED:\n" . $context;
    }

    private function broadenQuery($query)
    {
        // A quick, low-temp call to Gemini 2.5 Flash
        $response = $this->postToVertex('gemini-2.5-flash:generateContent', [
            'contents' => [[
                'parts' => [['text' => "Rewrite this search query to be more descriptive for a vector database search in a property management system. Original: '$query'"]]
            ]],
            'generationConfig' => ['temperature' => 0.0]
        ]);

        return $response->json()['candidates'][0]['content']['parts'][0]['text'] ?? $query;
    }

    /**
     * Main entry point for the sub-agents
     */
    public function executeSubAgent($functionName, $args, EmbeddingService $embeddingService)
    {
        $table = str_replace('search_', '', $functionName);
        $query = $args['query'] ?? '';

        // TOP 5 REFINEMENT: Receipts are numerical/ID-heavy. 
        // Vector search often misses these, so we bypass directly to Keyword Search.
        if ($table === 'receipts') {
            return $this->performKeywordSearch($table, $query);
        }

        // Attempt the "Smart" Vector Search first
        $context = $this->performSmartSearch($table, $query, $embeddingService);

        // If Vector search returns nothing or is too vague, fallback to Keyword search
        if (empty($context) || str_contains($context, "No relevant records")) {
            return $this->performKeywordSearch($table, $query);
        }

        return $context;
    }

    /**
     * The "Smart" Search: Uses Vector distance but prioritizes high-cost expenses
     */
    private function performSmartSearch($table, $query, $embeddingService, $limit = 10)
    {
        $embedding = $embeddingService->generate($query);
        if (!$embedding) return "No data found.";

        $vector = '[' . implode(',', $embedding) . ']';
        
        $queryBuilder = DB::table($table)
            ->select('*', DB::raw("embedding <-> '{$vector}' as distance"))
            ->whereNotNull('embedding');

        if ($table === 'expenses') {
            // Ensuring the "Elevator" spike is always seen
            $records = $queryBuilder->orderBy('amount', 'desc')
                ->limit($limit)
                ->get();
        } else {
            $records = $queryBuilder->orderBy('distance')
                ->limit($limit)
                ->get();
        }

        if ($records->isEmpty()) return "No relevant records found.";
        
        return $this->buildContext($records, $table);
    }

    private function performKeywordSearch($table, $query)
    {
        $queryBuilder = DB::table($table);

        if ($table === 'receipts') {
            $queryBuilder->join('tenants', 'receipts.tenant_id', '=', 'tenants.id');

            // Clean query to find the name (e.g., "Marcus")
            $stopWords = ['search', 'for', 'receipts', 'of', 'tenant', 'balance', 'outstanding'];
            $cleanQuery = str_ireplace($stopWords, '', $query);
            $words = array_filter(explode(' ', trim($cleanQuery)));

            $queryBuilder->where(function($q) use ($words) {
                foreach ($words as $word) {
                    if (strlen($word) > 2) {
                        $q->orWhere('tenants.name', 'ILIKE', "%{$word}%");
                    }
                }
            })->select('receipts.*');
        } else {
            // Standard keyword logic for other tables
            $words = explode(' ', $query);
            $keyword = end($words);

            if (Schema::hasColumn($table, 'title')) {
                $queryBuilder->where('title', 'ILIKE', "%{$keyword}%");
            } 
            if (Schema::hasColumn($table, 'name')) {
                $queryBuilder->orWhere('name', 'ILIKE', "%{$keyword}%");
            }
            if (Schema::hasColumn($table, 'notes')) {
                $queryBuilder->orWhere($table . '.notes', 'ILIKE', "%{$keyword}%");
            }
        }

        $records = $queryBuilder->limit(5)->get();

        if ($records->isEmpty()) {
            return "No records found in $table for: $query";
        }

        return $this->buildContext($records, $table);
    }

    private function isResultInsufficient($context)
    {
        return empty($context) || str_contains($context, "No relevant records") || strlen($context) < 50;
    }
}