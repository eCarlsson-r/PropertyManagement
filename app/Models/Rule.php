<?php

namespace App\Models;

use App\Services\EmbeddingService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Rule extends Model
{
    protected $fillable = [
        'property_id',
        'title',
        'description',
    ];

    protected static function booted()
    {
        static::saved(function ($rule) {
            if (DB::getDriverName() === 'pgsql' && ($rule->wasRecentlyCreated || $rule->isDirty(['title', 'description']))) {
                $text = "Business Rule - {$rule->title}: {$rule->description}";
                
                $embedding = app(EmbeddingService::class)->generate($text);

                if ($embedding) {
                    DB::table('rules')
                        ->where('id', $rule->id)
                        ->update(['embedding' => DB::raw("'[" . implode(',', $embedding) . "]'")]);
                }
            }
        });
    }

    public function property()
    {
        return $this->belongsTo(Property::class);
    }
}
