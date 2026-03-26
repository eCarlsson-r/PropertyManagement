<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    protected $fillable = [
        "name",
        "currency",
        "mobile",
        "location_id",
        "notes"
    ];

    protected $guarded = ["id"];

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function rooms()
    {
        return $this->hasMany(Room::class);
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }
}
