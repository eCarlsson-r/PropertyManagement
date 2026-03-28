<?php
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SemanticResultResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'summary' => $this->notes ?? $this->title ?? $this->name,
            'distance' => $this->distance ?? null,
        ];
    }
}