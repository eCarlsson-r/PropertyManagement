<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
