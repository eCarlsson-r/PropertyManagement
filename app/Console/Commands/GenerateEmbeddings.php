<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Config;

class GenerateEmbeddings extends Command
{
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
        }
    }

    private function getEmbedding($text)
    {
        // Pass the key in the URL query string
        $apiKey = config('services.gemini.key');
        $url = "https://generativelanguage.googleapis.com/v1beta/models/text-embedding-004:embedContent?key={$apiKey}";

        $response = Http::post($url, [
            'model' => 'models/text-embedding-004', // Upgraded model
            'content' => [
                'parts' => [
                    ['text' => $text]
                ]
            ]
        ]);

        // Add some basic logging to catch errors in your console
        if ($response->failed()) {
            $this->error("API Error: " . $response->body());
            return [];
        }

        return $response->json()['embedding']['values'] ?? [];
    }
}