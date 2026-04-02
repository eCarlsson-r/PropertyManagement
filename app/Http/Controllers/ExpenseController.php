<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Property;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $date = $request->input('date');

        $expenses = Expense::query()
            ->with('property')
            ->when($search, function ($query, $search) {
                $query->where('title', 'like', "%{$search}%");
            })
            ->when($date, function ($query, $date) {
                $query->where('date', 'like', "%{$date}%");
            })
            ->paginate(10)
            ->withQueryString();

        return inertia("expenses/index", [
            "expenses" => $expenses,
            'filters' => [
                'search' => $search,
            ],
        ]);
    }

    public function create()
    {
        return inertia("expenses/create", [
            "properties" => Property::all(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            "date" => "required",
            "property_id" => "required",
            "title" => "required",
            "amount" => "required",
            "category" => "required"
        ]);

        Expense::create($request->all());

        return redirect()->route("expenses.index");
    }

    public function edit(Expense $expense)
    {
        return inertia("expenses/edit", [
            "expense" => $expense,
            "properties" => Property::all(),
        ]);
    }

    public function update(Request $request, Expense $expense)
    {
        $request->validate([
            "date" => "required",
            "property_id" => "required",
            "title" => "required",
            "amount" => "required",
            "category" => "required"
        ]);

        $expense->update($request->all());

        return redirect()->route("expenses.index");
    }

    public function destroy(Expense $expense)
    {
        $expense->delete();

        return redirect()->route("expenses.index");
    }
}
