<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Property;
use App\Models\Location;
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
            "locations" => Location::all(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            "name" => "required",
            "currency" => "required",
            "mobile" => "required",
            "location_id" => "required",
            "notes" => "required",
        ]);

        Property::create($request->all());

        return redirect()->route("properties.index");
    }

    public function edit(Property $property)
    {
        return Inertia::render("properties/edit", [
            "property" => $property->load('location'),
            "locations" => Location::all(),
        ]);
    }

    public function update(Request $request, Property $property)
    {
        $request->validate([
            "name" => "required",
            "currency" => "required",
            "mobile" => "required",
            "location_id" => "required",
            "notes" => "required",
        ]);

        $property->update($request->all());

        return redirect()->route("properties.index");
    }

    public function destroy(Property $property)
    {
        $property->delete();

        return redirect()->route("properties.index");
    }
}
