<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Location;
use Inertia\Inertia;

class LocationController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $locations = Location::query()
            ->when($search, function ($query, $search) {
                $query->where('address', 'like', "%{$search}%")
                    ->orWhere('city', 'like', "%{$search}%")
                    ->orWhere('province', 'like', "%{$search}%")
                    ->orWhere('country', 'like', "%{$search}%");
            })
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('locations/index', [
            'locations' => $locations,
            'filters' => [
                'search' => $search,
            ],
        ]);
    }

    public function create()
    {
        return Inertia::render("locations/create");
    }

    public function store(Request $request)
    {
        $request->validate([
            "address" => "required",
            "country" => "required",
            "province" => "required",
            "city" => "required",
            "district" => "required",
            "subdistrict" => "required",
            "postal" => "required",
        ]);

        Location::create($request->all());

        return redirect()->route("locations.index");
    }

    public function edit(Location $location)
    {
        return Inertia::render("locations/edit", [
            "location" => $location,
        ]);
    }

    public function update(Request $request, Location $location)
    {
        $request->validate([
            "address" => "required",
            "country" => "required",
            "province" => "required",
            "city" => "required",
            "district" => "required",
            "subdistrict" => "required",
            "postal" => "required",
        ]);

        $location->update($request->all());

        return redirect()->route("locations.index");
    }

    public function destroy(Location $location)
    {
        $location->delete();

        return redirect()->route("locations.index");
    }
}
