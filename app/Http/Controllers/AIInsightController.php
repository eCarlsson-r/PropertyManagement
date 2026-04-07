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

        for ($i = 0; $i < 3; $i++) {
            // Step 1: Call Gemini with Tool definitions
            $response = $agent->callGemini($messages);
            if (isset($response['candidates'])) {
                $candidate = $response['candidates'][0]['content']['parts'][0];

                // Check if Gemini wants to call a Sub-Agent (Function Call)
                if (isset($candidate['functionCall'])) {
                    $call = $candidate['functionCall'];
                    $functionName = $call['name'];
                    $args = $call['args'];

                    // Step 2: Execute the "Sub-Agent" (Your vector search logic)
                    // We determine the table based on the function name Gemini chose
                    $data = $agent->executeSubAgent($functionName, $args, $embeddingService);

                    // Step 3: Feedback the data to the Primary Agent
                    $messages[] = $response['candidates'][0]['content']; // AI's request
                    $messages[] = [
                        'role' => 'tool',
                        'parts' => [
                            [
                                'toolResponse' => [
                                    'name' => $functionName,
                                    'response' => ['content' => $data]
                                ]
                            ]
                        ]
                    ];
                    continue; // Go back to top to let Gemini analyze the new data
                }

                // Final Result: If no function call, Gemini has finished the task
                return inertia('AI/Insights', [
                    'insight' => $candidate['text'] ?? 'Done',
                    'steps_taken' => $messages // This MUST match your Vue prop name
                ]);
            } else {
                return inertia('AI/Insights', [
                    'insight' => $response['error']['message'],
                    'steps_taken' => $messages // This MUST match your Vue prop name
                ]);
            }
        }
    }
}