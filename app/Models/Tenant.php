<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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

    protected $model;
    protected $text;

    public function __construct($model, $text)
    {
        $this->model = $model;
        $this->text = $text;
    }

    public function handle(EmbeddingService $service)
    {
        if ($tenant->isDirty(['notes', 'name', 'address'])) {
            $embedding = $service->generate($this->text);

            if ($embedding) {
                $this->model->embedding = json_encode($embedding);
                $this->model->save();
            }
        }
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
}
