<?php

namespace App\Models;

use App\Services\EmbeddingService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Expense extends Model
{
    protected $fillable = [
        "date",
        "property_id",
        "title",
        "amount",
        "category",
        "notes"
    ];

    protected $guarded = ["id"];

    protected static function booted()
    {
        static::saved(function ($expense) {
            if ($expense->wasRecentlyCreated || $expense->isDirty(['title', 'notes', 'amount'])) {
                $text = "Expense: {$expense->title} ({$expense->category}) costing {$expense->amount}. Details: {$expense->notes}";
                
                $embedding = app(EmbeddingService::class)->generate($text);
                if ($embedding) {
                    DB::table('expenses')->where('id', $expense->id)->update(
                        ['embedding' => DB::raw("'[" . implode(',', $embedding) . "]'")]
                    );
                }
            }
        });
    }

    public function property()
    {
        return $this->belongsTo(Property::class);
    }
}
