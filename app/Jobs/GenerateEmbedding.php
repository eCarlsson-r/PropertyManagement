<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class GenerateEmbedding implements ShouldQueue
{
    use Queueable;

    protected $model;
    protected $text;

    public function __construct($model, $text)
    {
        $this->model = $model;
        $this->text = $text;
    }

    public function handle(EmbeddingService $service)
    {
        $embedding = $service->generate($this->text);

        if ($embedding) {
            $this->model->embedding = json_encode($embedding);
            $this->model->save();
        }
    }
}
