<?php

namespace App\Models;

use App\Services\EmbeddingService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Tenant extends Model
{
    protected $fillable = [
        "name",
        "country_code",
        "mobile",
        "email",
        "unit_id",
        "cycle",
        "birth_date",
        "gender",
        "marital_status",
        "address",
        "account_bank",
        "account_number",
        "account_owner",
        "currency",
        "notes",
        "emergency_contact_name",
        "emergency_contact_relationship",
        "emergency_contact_country_code",
        "emergency_contact_mobile",
        "deposit",
        "deposit_bank",
        "deposit_number"
    ];

    protected $guarded = ["id"];

    protected static function booted()
    {
        static::saved(function ($tenant) {
            // Check if name, notes, or address changed to avoid redundant API calls
            if ($tenant->wasRecentlyCreated || $tenant->isDirty(['name', 'notes', 'address'])) {
                
                // Construct the rich text for the vector database
                $text = "Tenant: {$tenant->name}. History and behavior: {$tenant->notes}. Address: {$tenant->address}";
                
                // Resolve the service and generate embedding
                $service = app(EmbeddingService::class);
                $embedding = $service->generate($text);

                if ($embedding) {
                    // Update the column directly in the DB to avoid re-triggering this 'saved' event
                    DB::table('tenants')
                        ->where('id', $tenant->id)
                        ->update(['embedding' => DB::raw("'[" . implode(',', $embedding) . "]'")]);
                }
            }
        });
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
}
