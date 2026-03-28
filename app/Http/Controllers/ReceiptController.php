<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\Receipt;
use App\Models\Unit;
use App\Models\Tenant;
use Illuminate\Http\Request;

class ReceiptController extends Controller
{
    public function index(Request $request)
    {
        $dates = $request->input('dates');
        $date = $request->input('date');

        $receipts = Receipt::query()
            ->with('unit.property', 'unit.room')
            ->with('tenant')
            ->when($dates, function ($query, $dates) use ($date) {
                if ($dates === "transaction-date") {
                    $query->where('created_at', 'like', "%{$date}%");
                } elseif ($dates === "rent-start-end-date") {
                    $query->where('start_date', 'like', "%{$date}%")
                        ->orWhere('end_date', 'like', "%{$date}%");
                }
            })
            ->paginate(10)
            ->withQueryString();

        return inertia("receipts/index", [
            "receipts" => $receipts,
            "filters" => [
                "dates" => $dates,
                "date" => $date
            ],
        ]);
    }

    public function create()
    {
        return inertia("receipts/create", [
            "properties" => Property::all(),
            "units" => Unit::all(),
            "tenants" => Tenant::all()
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            "tenant_id" => "required",
            "property_id" => "required",
            "room_id" => "required",
            "unit_id" => "required",
            "receipt_cycle" => "required",
            "start_date" => "required",
            "end_date" => "required",
            "discount_type" => "required",
            "discount_amount" => "required",
            "discount_percent" => "required",
            "tax" => "required",
            "total" => "required",
            "down_payment" => "required",
            "remaining" => "required",
            "fully_paid" => "required",
            "notes" => "required",
            "adults" => "required",
            "children" => "required",
            "babies" => "required",
            "pets" => "required",
            "reminder" => "required"
        ]);

        Receipt::create($request->all());

        return redirect()->route("receipts.index");
    }

    public function edit(Receipt $receipt)
    {
        return inertia("receipts/edit", [
            "receipt" => $receipt->load(['unit', 'tenant']),
            "properties" => Property::all(),
            "units" => Unit::all(),
            "tenants" => Tenant::all()
        ]);
    }

    public function update(Request $request, Receipt $receipt)
    {
        $request->validate([
            "tenant_id" => "required",
            "unit_id" => "required",
            "receipt_cycle" => "required",
            "start_date" => "required",
            "end_date" => "required",
            "discount_type" => "required",
            "discount_amount" => "required",
            "discount_percent" => "required",
            "tax" => "required",
            "total" => "required",
            "down_payment" => "required",
            "remaining" => "required",
            "fully_paid" => "required",
            "notes" => "required",
            "adults" => "required",
            "children" => "required",
            "babies" => "required",
            "pets" => "required",
            "reminder" => "required"
        ]);

        $receipt->update($request->all());

        return redirect()->route("receipts.index");
    }

    public function destroy(Receipt $receipt)
    {
        $receipt->delete();

        return redirect()->route("receipts.index");
    }
}
