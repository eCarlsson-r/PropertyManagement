<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tenant;
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
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('mobile', 'like', "%{$search}%");
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
                    $query->orderBy('unit_id', 'asc');
                } elseif ($option === 'za-unit') {
                    $query->orderBy('unit_id', 'desc');
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
        return Inertia::render("tenants/create");
    }

    public function store(Request $request)
    {
        $request->validate([
            "name" => "required",
            "email" => "",
            "phone" => "",
            "mobile" => "required",
            "unit_id" => "required",
            "cycle" => "required",
            "deposit" => "required",
            "notes" => "",
        ]);

        Tenant::create($request->all());

        return redirect()->route("tenants.index");
    }

    public function edit(Tenant $tenant)
    {
        return Inertia::render("tenants/edit", [
            "tenant" => $tenant,
        ]);
    }

    public function update(Request $request, Tenant $tenant)
    {
        $request->validate([
            "name" => "required",
            "email" => "",
            "phone" => "",
            "mobile" => "required",
            "unit_id" => "required",
            "cycle" => "required",
            "deposit" => "required",
            "notes" => "",
        ]);

        $tenant->update($request->all());

        return redirect()->route("tenants.index");
    }

    public function destroy(Tenant $tenant)
    {
        $tenant->delete();

        return redirect()->route("tenants.index");
    }
}
