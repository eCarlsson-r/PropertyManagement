<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    protected $fillable = [
        "name", 
        "email", 
        "phone", 
        "mobile", 
        "unit_id", 
        "cycle", 
        "deposit",
        "notes"
    ];

    protected $guarded = ["id"];

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
}
