<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Config;
use App\Concerns\InteractWithVertexAI;

class GenerateEmbeddings extends Command
{
    use InteractWithVertexAI; // Import the same trait
    
    protected $signature = 'embeddings:generate';
    protected $description = 'Generate embeddings for tenants, expenses, properties, and rules';

    public function handle()
    {
        $this->info('Generating embeddings...');

        $this->generateForTable('tenants', function ($row) {
            return "{$row->name} tenant with phone {$row->mobile}";
        });

        $this->generateForTable('expenses', function ($row) {
            return "{$row->title} expense of {$row->amount} for {$row->category}";
        });

        $this->generateForTable('properties', function ($row) {
            return "{$row->name} owned by {$row->owner_name}";
        });

        $this->generateForTable('rules', function ($row) {
            return "{$row->title}: {$row->description}";
        });

        $this->info('Done.');
    }

    private function generateForTable($table, $textBuilder)
    {
        $rows = DB::table($table)->get();

        foreach ($rows as $row) {
            $text = $textBuilder($row);
            $embedding = $this->getEmbedding($text);

            if (!empty($embedding)) {
                DB::table($table)
                    ->where('id', $row->id)
                    ->update([
                        'embedding' => DB::raw("'[" . implode(',', $embedding) . "]'")
                    ]);
                $this->info("Updated {$table} ID {$row->id}");
            } else {
                $this->warn("Skipped {$table} ID {$row->id} due to empty embedding.");
            }

            sleep(1);
        }
    }
    
    private function getEmbedding($text)
    {
        if (empty($text)) return [];

        $success = false;
        $attempts = 0;

        while (!$success && $attempts < 10) {
            $response = $this->postToVertex('text-embedding-004:predict', [
                'instances' => [[
                    'task_type' => 'RETRIEVAL_DOCUMENT',
                    'content' => $text
                ]]
            ]);

            if ($response->successful()) {
                return $response->json()['predictions'][0]['embeddings']['values'] ?? [];
            }

            if ($response->status() === 429) {
                $attempts++;
                // Exponentially increase wait time: 2s, 4s, 8s...
                $waitTime = pow(2, $attempts); 
                dump("Rate limited on record. Retrying in {$waitTime}s...");
                sleep($waitTime);
                continue;
            }

            // If it's a 400 or 500 error, log it and stop to prevent infinite loops
            dump("Vertex AI Permanent Error: " . $response->status());
            break;
        }

        return [];
    }
}