<?php

namespace App\Models;

use App\Services\EmbeddingService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Property extends Model
{
    protected $fillable = [
        "name",
        "currency",
        "owner_name",
        "owner_country_code",
        "owner_mobile",
        "manager_name",
        "manager_country_code",
        "manager_mobile",
        "location",
        "account_owner",
        "account_bank",
        "account_number",
        "notes"
    ];

    protected $guarded = ["id"];

    protected static function booted()
    {
        static::saved(function ($property) {
            if ($property->wasRecentlyCreated || $property->isDirty(['name', 'notes'])) {
                $location = $property->location; // Join location for rich context
                $text = "Property: {$property->name}. Manager: {$property->manager_name}. Notes: {$property->notes}. City: " . ($location->city ?? 'Unknown');
                
                $embedding = app(EmbeddingService::class)->generate($text);
                if ($embedding) {
                    DB::table('properties')->where('id', $property->id)
                        ->update(['embedding' => DB::raw("'[" . implode(',', $embedding) . "]'")]);
                }
            }
        });
    }

    protected $casts = [
        "location" => "json",
    ];

    public function location()
    {
        return $this->hasOne(Location::class);
    }

    public function rooms()
    {
        return $this->hasMany(Room::class);
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    public function rules()
    {
        return $this->hasMany(Rule::class);
    }

    public function units()
    {
        return $this->hasMany(Unit::class);
    }
}
