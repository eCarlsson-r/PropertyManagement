<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    protected $fillable = [
        "property_id",
        "name",
        "floor",
        "room_id",
        "available",
        "condition"
    ];

    protected $guarded = [
        "id",
    ];

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function receipts()
    {
        return $this->hasMany(Receipt::class);
    }
}
