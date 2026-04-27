<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Property;
use App\Models\Location;
use App\Models\Room;
use App\Models\Unit;
use Inertia\Inertia;

class PropertyController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $properties = Property::query()
            ->with('location')
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('properties/index', [
            'properties' => $properties,
            'filters' => [
                'search' => $search,
            ],
        ]);
    }

    public function create()
    {
        return Inertia::render("properties/create", [
            "locations" => Location::all()
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            "name" => "required",
            "currency" => "required",
            "owner_name" => "required",
            "owner_country_code" => "required",
            "owner_mobile" => "required",
            "manager_name" => "required",
            "manager_country_code" => "required",
            "manager_mobile" => "required",
            "account_owner" => "nullable",
            "account_bank" => "nullable",
            "account_number" => "nullable",
            "notes" => "nullable",
        ]);

        $property = Property::create($request->except("location"));

        Location::create($request->location + ["property_id" => $property->id]);

        return redirect()->route("properties.index");
    }

    public function edit(Property $property)
    {
        return Inertia::render("properties/edit", [
            "property" => $property->load(['location', 'rules', 'units.room']),
            "locations" => Location::all(),
            "rooms" => Room::all(),
        ]);
    }

    public function update(Request $request, Property $property)
    {
        $request->validate([
            "name" => "required",
            "currency" => "required",
            "owner_name" => "required",
            "owner_country_code" => "required",
            "owner_mobile" => "required",
            "manager_name" => "required",
            "manager_country_code" => "required",
            "manager_mobile" => "required",
            "location" => "required",
            "account_owner" => "nullable",
            "account_bank" => "nullable",
            "account_number" => "nullable",
            "notes" => "nullable",
        ]);

        $property->update($request->except("location"));

        Location::updateOrCreate(
            ["property_id" => $property->id],
            $request->location
        );

        // Update Rules if provided
        if (isset($request->rules)) {
            $ruleIds = collect($request->rules)->pluck('id')->filter()->toArray();
            $property->rules()->whereNotIn('id', $ruleIds)->delete();

            foreach ($request->rules as $rule) {
                if (!empty($rule['title'])) {
                    if (isset($rule['id'])) {
                        $property->rules()->where('id', $rule['id'])->update($rule);
                    } else {
                        $property->rules()->create($rule);
                    }
                }
            }
        }

        if (isset($request->units)) {
            $unitIds = collect($request->units)->pluck('id')->filter()->toArray();
            $property->units()->whereNotIn('id', $unitIds)->delete();

            foreach($request->units as $unit) {
                if (!isset($unit['id'])) {
                    Unit::create($unit + ['property_id' => $property->id]);
                } else {
                    unset($unit['room']);
                    Unit::where('id', $unit['id'])->update($unit);
                }
            }
        }

        return redirect()->route("properties.index");
    }

    public function destroy(Property $property)
    {
        $property->delete();

        return redirect()->route("properties.index");
    }
}
