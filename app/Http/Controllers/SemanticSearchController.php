<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\EmbeddingService;
use App\Http\Resources\SemanticResultResource;

class SemanticSearchController extends Controller
{
    public function search(Request $request, EmbeddingService $embeddingService)
    {
        $request->validate([
            'query' => 'required|string',
            'type' => 'required|in:tenants,expenses,properties,rules'
        ]);

        // Step 1: Convert query → embedding
        $embedding = $embeddingService->generate($request->query);

        if (!$embedding) {
            return response()->json([
                'error' => 'Failed to generate embedding'
            ], 500);
        }

        // Convert to PostgreSQL vector format
        $vector = '[' . implode(',', $embedding) . ']';

        // Step 2: Dynamic table selection
        $table = $request->type;

        // Step 3: Perform similarity search
        $results = DB::table($table)
            ->select('*', DB::raw("embedding <-> '{$vector}' as distance"))
            ->whereNotNull('embedding')
            ->orderBy('distance')
            ->limit(5)
            ->get();

        return SemanticResultResource::collection($results);
    }
}