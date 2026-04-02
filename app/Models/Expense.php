<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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

    public function property()
    {
        return $this->belongsTo(Property::class);
    }
}
