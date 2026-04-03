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

            DB::table($table)
                ->where('id', $row->id)
                ->update([
                    'embedding' => DB::raw("'[" . implode(',', $embedding) . "]'")
                ]);

            $this->info("Updated {$table} ID {$row->id}");
        }
    }

    private function getEmbedding($text)
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . config('services.gemini.key'),
            'Content-Type' => 'application/json',
        ])->post(
            'https://generativelanguage.googleapis.com/v1beta/models/embedding-001:embedContent',
            [
                'model' => 'models/embedding-001',
                'content' => [
                    'parts' => [
                        ['text' => $text]
                    ]
                ]
            ]
        );

        return $response->json()['embedding']['values'] ?? [];
    }
}