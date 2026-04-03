<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\EmbeddingService;
use Illuminate\Support\Facades\Http;

class AIInsightController extends Controller
{

    public function index()
    {
        return inertia('AI/Insights');
    }

    public function generate(Request $request, EmbeddingService $embeddingService)
    {
        $request->validate([
            'query' => 'required|string',
            'type' => 'required|in:tenants,expenses,properties,rules'
        ]);

        // Step 1 — Generate embedding
        $embedding = $embeddingService->generate($request->query);

        if (!$embedding) {
            return back()->withErrors(['query' => 'Embedding failed']);
        }

        $vector = '[' . implode(',', $embedding) . ']';
        $table = $request->type;

        // Step 2 — Retrieve relevant records
        $records = DB::table($table)
            ->select('*', DB::raw("embedding <-> '{$vector}' as distance"))
            ->whereNotNull('embedding')
            ->orderBy('distance')
            ->limit(3)
            ->get();

        // Step 3 — Build context
        $context = $this->buildContext($records, $table);

        // Step 4 — Call Gemini (Vertex AI)
        $response = $this->callGemini("Analyze and provide insights: " . $request->query, $context);

        return inertia('AI/Insights', [
            'query' => $request->query,
            'type' => $request->type,
            'context_used' => $records,
            'insight' => $response
        ]);
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

    private function callGemini($query, $context)
    {
        $prompt = "
You are an expert property management analyst.

Your role is to analyze tenant, financial, and property data and provide business-level insights.

USER QUESTION:
{$query}

DATA CONTEXT:
{$context}

INSTRUCTIONS:

1. Identify key patterns (payment behavior, complaints, risks)
2. Highlight important issues or anomalies
3. Provide clear, actionable recommendations
4. Be concise but specific
5. Base your answer ONLY on the provided data

OUTPUT FORMAT:

Summary:
- Brief explanation of the situation

Key Risks:
- Bullet points of potential risks

Recommendations:
- Actionable steps the property owner should take

Optional Insights:
- Any additional useful observations

IMPORTANT:
- Do not make up data
- Do not generalize beyond the context
- Focus on practical decisions
";

        $response = Http::post('https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=' . config('services.gemini.key'), [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt]
                    ]
                ]
            ],
            "generationConfig" => [
                "temperature" => 0.2
            ]
        ]);

        return $response->json()['candidates'][0]['content']['parts'][0]['text'] ?? 'No response';
    }
}