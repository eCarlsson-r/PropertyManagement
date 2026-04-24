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
        $this->info('Generating embeddings with rich context...');

        $this->generateForTable('tenants', function ($row) {
            // INCLUDE NOTES: This is where the "Risk" keywords live!
            return "Tenant: {$row->name}. History and behavior: {$row->notes}";
        });

        $this->generateForTable('expenses', function ($row) {
            // INCLUDE NOTES: This is where "Emergency" and "Outlier" details live!
            return "Expense: {$row->title} ({$row->category}) costing {$row->amount}. Details: {$row->notes}";
        });

        $this->generateForTable('properties', function ($row) {
            // Manually fetch the location for this property
            $location = DB::table('locations')->where('property_id', $row->id)->first();
            
            $address = $location ? $location->address : 'No address';
            $city = $location ? $location->city : 'Unknown city';

            return "Property: {$row->name}. " .
                "Location: {$address}, {$city}. " .
                "Owner: {$row->owner_name}. " .
                "Manager: {$row->manager_name}. " .
                "Details: {$row->notes}";
        });

        $this->generateForTable('rules', function ($row) {
            return "Business Rule - {$row->title}: {$row->description}";
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