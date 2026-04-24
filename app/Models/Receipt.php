<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Receipt extends Model
{
    protected $fillable = [
        "tenant_id",
        "unit_id",
        "receipt_cycle",
        "start_date",
        "end_date",
        "discount_type",
        "discount_amount",
        "discount_percent",
        "tax",
        "total",
        "down_payment",
        "remaining",
        "fully_paid",
        "notes",
        "adults",
        "children",
        "babies",
        "pets",
        "reminder",
    ];

    protected $guarded = [
        "id",
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function property()
    {
        return $this->unit->property();
    }

    public function room()
    {
        return $this->unit->room();
    }
}
