<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RoomController extends Controller
{
    public function index(Request $request)
    {
        $rooms = Room::query()
            ->when($request->search, function ($query) use ($request) {
                $query->where('name', 'like', "%{$request->search}%");
            })
            ->paginate(10);

        return inertia("rooms/index", [
            "rooms" => $rooms,
            "filters" => $request->only("search"),
        ]);
    }

    public function create()
    {
        return inertia("rooms/create");
    }

    public function store(Request $request)
    {
        $request->validate([
            "name" => "required",
            "area" => "required",
            "daily_price" => "required",
            "weekly_price" => "required",
            "monthly_price" => "required",
            "annual_price" => "required",
        ]);

        Room::create($request->all());

        return redirect()->route("rooms.index");
    }

    public function edit(Room $room)
    {
        return inertia("rooms/edit", [
            "room" => $room,
        ]);
    }

    public function update(Request $request, Room $room)
    {
        $request->validate([
            "name" => "required",
            "area" => "required",
            "daily_price" => "required",
            "weekly_price" => "required",
            "monthly_price" => "required",
            "annual_price" => "required",
        ]);

        $room->update($request->all());

        return redirect()->route("rooms.index");
    }

    public function destroy(Room $room)
    {
        $room->delete();

        return redirect()->route("rooms.index");
    }
}
