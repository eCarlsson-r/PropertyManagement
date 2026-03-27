<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    protected $fillable = [
        "property_id",
        "address",
        "country",
        "province",
        "city",
        "district",
        "subdistrict",
        "postal",
    ];

    protected $guarded = ["id"];

    public function property()
    {
        return $this->belongsTo(Property::class);
    }
}
