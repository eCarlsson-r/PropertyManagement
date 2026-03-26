<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index()
    {
        return inertia("expenses/index", [
            "expenses" => Expense::all(),
        ]);
    }

    public function create()
    {
        return inertia("expenses/create");
    }

    public function store(Request $request)
    {
        $request->validate([
            "date" => "required",
            "property_id" => "required",
            "title" => "required",
            "total" => "required",
            "category" => "required"
        ]);

        Expense::create($request->all());

        return redirect()->route("expenses.index");
    }

    public function edit(Expense $expense)
    {
        return inertia("expenses/edit", [
            "expense" => $expense,
        ]);
    }

    public function update(Request $request, Expense $expense)
    {
        $request->validate([
            "date" => "required",
            "property_id" => "required",
            "title" => "required",
            "total" => "required",
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
