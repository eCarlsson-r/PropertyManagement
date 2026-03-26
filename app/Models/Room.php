<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    protected $fillable = [
        "name",
        "area",
        "daily_price",
        "weekly_price",
        "monthly_price",
        "annual_price"
    ];

    protected $guarded = [
        "id",
    ];
}
