<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    public function index()
    {
        return inertia("units/index", [
            "units" => Unit::all(),
        ]);
    }

    public function create()
    {
        return inertia("units/create");
    }

    public function store(Request $request)
    {
        $request->validate([
            "property_id" => "required",
            "name" => "required",
            "floor" => "required",
            "room_id" => "required",
        ]);

        Unit::create($request->all());

        return redirect()->route("units.index");
    }

    public function edit(Unit $unit)
    {
        return inertia("units/edit", [
            "unit" => $unit,
        ]);
    }

    public function update(Request $request, Unit $unit)
    {
        $request->validate([
            "property_id" => "required",
            "name" => "required",
            "floor" => "required",
            "room_id" => "required",
        ]);

        $unit->update($request->all());

        return redirect()->route("units.index");
    }

    public function destroy(Unit $unit)
    {
        $unit->delete();

        return redirect()->route("units.index");
    }
}
