<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\EmbeddingService;
use App\Services\PropertyAgentService;
use Illuminate\Support\Facades\Http;

class AIInsightController extends Controller
{
    public function index()
    {
        return inertia('AI/Insights');
    }
    public function generate(Request $request, EmbeddingService $embeddingService, PropertyAgentService $agent)
    {
        $messages = [['role' => 'user', 'parts' => [['text' => "Analyze: " . $request->input('query')]]]];

        for ($i = 0; $i < 5; $i++) { // Increased to 5 to allow more complex multi-step reasoning
            $response = $agent->callVertexAI($messages);

            // Check if the API returned a valid candidate
            if (isset($response['candidates'][0]['content']['parts'])) {
                $parts = $response['candidates'][0]['content']['parts'];
                
                // Look for a function call in any part of the response
                // Check if the AI wants to call one or more Sub-Agents
                $functionCalls = collect($parts)->where('functionCall')->all();

                if (!empty($functionCalls)) {
                    $feedbackParts = [];

                    foreach ($functionCalls as $part) {
                        $call = $part['functionCall'];
                        $functionName = $call['name'];
                        $args = $call['args'];

                        // Execute the Sub-Agent
                        $data = $agent->executeSubAgent($functionName, $args, $embeddingService);

                        // Build the corresponding response part
                        $feedbackParts[] = [
                            'functionResponse' => [
                                'name' => $functionName,
                                'response' => ['name' => $functionName, 'content' => $data]
                            ]
                        ];
                    }

                    // Step 3: Feedback BOTH the original AI request and your responses
                    $messages[] = $response['candidates'][0]['content']; 
                    $messages[] = [
                        'role' => 'function',
                        'parts' => $feedbackParts // This now contains 1-to-1 responses for every call
                    ];
                    
                    continue; 
                }

                // If we are here, there's no function call. Look for the text part.
                $textPart = collect($parts)->firstWhere('text');
                
                return inertia('AI/Insights', [
                    'insight' => $textPart['text'] ?? 'Analysis complete with no summary text.',
                    'steps_taken' => $messages 
                ]);
            } 

            // Fallback for errors
            return inertia('AI/Insights', [
                'insight' => 'Error: ' . json_encode($response),
                'steps_taken' => $messages 
            ]);
        }
    }
}