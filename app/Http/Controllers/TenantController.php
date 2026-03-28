<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tenant;
use App\Models\Property;
use App\Models\Room;
use App\Models\Unit;
use Inertia\Inertia;

class TenantController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $option = $request->input('option');

        $tenants = Tenant::query()
            ->with('unit.property', 'unit.room')
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('mobile', 'like', "%{$search}%")
                    ->orWhere('unit.name', 'like', "%{$search}%")
                    ->orWhere('unit.property.name', 'like', "%{$search}%")
                    ->orWhere('unit.room.name', 'like', "%{$search}%");
            })
            ->when($option, function ($query, $option) {
                if ($option === 'least-days') {
                    $query->orderBy('days_left', 'asc');
                } elseif ($option === 'most-days') {
                    $query->orderBy('days_left', 'desc');
                } elseif ($option === 'az') {
                    $query->orderBy('name', 'asc');
                } elseif ($option === 'za') {
                    $query->orderBy('name', 'desc');
                } elseif ($option === 'az-unit') {
                    $query->orderBy('unit.name', 'asc');
                } elseif ($option === 'za-unit') {
                    $query->orderBy('unit.name', 'desc');
                }
            })
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('tenants/index', [
            'tenants' => $tenants,
            'filters' => [
                'search' => $search,
            ],
        ]);
    }

    public function create()
    {
        return Inertia::render("tenants/create", [
            "properties" => Property::all(),
            "units" => Unit::all()
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            "name" => "required",
            "country_code" => "required",
            "mobile" => "required",
            "email" => "nullable",
            "unit.property_id" => "required",
            "unit.room_id" => "required",
            "cycle" => "required",
            "birth_date" => "nullable",
            "gender" => "nullable",
            "marital_status" => "nullable",
            "address" => "nullable",
            "account_bank" => "nullable",
            "account_number" => "nullable",
            "account_owner" => "nullable",
            "currency" => "nullable",
            "notes" => "nullable",
            "emergency_contact_name" => "required",
            "emergency_contact_relationship" => "required",
            "emergency_contact_country_code" => "required",
            "emergency_contact_mobile" => "required",
            "deposit" => "required",
            "deposit_bank" => "nullable",
            "deposit_number" => "nullable"
        ]);

        $unit = Unit::where(
            "property_id",
            $request->unit["property_id"]
        )->where("room_id", $request->unit["room_id"])->first();

        Tenant::create($request->except("unit") + ["unit_id" => $unit->id]);

        return redirect()->route("tenants.index");
    }

    public function edit(Tenant $tenant)
    {
        return Inertia::render("tenants/edit", [
            "tenant" => $tenant->load("unit.property", "unit.room"),
            "properties" => Property::all(),
            "units" => Unit::all(),
        ]);
    }

    public function update(Request $request, Tenant $tenant)
    {
        $request->validate([
            "name" => "required",
            "country_code" => "required",
            "mobile" => "required",
            "email" => "nullable",
            "unit.property_id" => "required",
            "unit.room_id" => "required",
            "cycle" => "required",
            "birth_date" => "nullable",
            "gender" => "nullable",
            "marital_status" => "nullable",
            "address" => "nullable",
            "account_bank" => "nullable",
            "account_number" => "nullable",
            "account_owner" => "nullable",
            "currency" => "nullable",
            "notes" => "nullable",
            "emergency_contact_name" => "required",
            "emergency_contact_relationship" => "required",
            "emergency_contact_country_code" => "required",
            "emergency_contact_mobile" => "required",
            "deposit" => "required",
            "deposit_bank" => "nullable",
            "deposit_number" => "nullable"
        ]);

        $unit = Unit::where(
            "property_id",
            $request->unit["property_id"]
        )->where("room_id", $request->unit["room_id"])->first();

        $tenant->update($request->except("unit") + ["unit_id" => $unit->id]);

        return redirect()->route("tenants.index");
    }

    public function destroy(Tenant $tenant)
    {
        $tenant->delete();

        return redirect()->route("tenants.index");
    }
}
