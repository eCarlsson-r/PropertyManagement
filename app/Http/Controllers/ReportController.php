<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\Receipt;
use App\Models\Expense;
use App\Models\Unit;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function dashboard(Request $request)
    {
        $receipts = Receipt::with(['tenant', 'unit.property'])->latest()->take(10)->get();
        return Inertia::render('Dashboard', [
            'receipt_list' => $receipts,
        ]);
    }
    public function schedule(Request $request)
    {
        $days = (int) ($request->days ?? 7);
        $propertyId = $request->property_id;

        $startDate = now()->startOfDay();
        $endDate = now()->addDays($days)->endOfDay();

        $units = Unit::query()
            ->with(['property', 'room', 'receipts' => function ($query) use ($startDate, $endDate) {
                $query->where('start_date', '<=', $endDate->format('Y-m-d'))
                    ->where('end_date', '>=', $startDate->format('Y-m-d'))
                    ->with('tenant');
            }])
            ->when($propertyId && $propertyId !== 'all', function ($query) use ($propertyId) {
                $query->where('property_id', $propertyId);
            })
            ->get();

        return Inertia::render('reports/Schedule', [
            'units' => $units,
            'properties' => Property::all(),
            'filters' => [
                'days' => $days,
                'property_id' => $propertyId ?? 'all'
            ]
        ]);
    }

    public function occupancy(Request $request)
    {
        $propertyId = $request->property_id;
        $endDate = now();
        $startDate = now()->subDays(30);

        $occupancy = Unit::query()
            ->join('rooms as rm', 'units.room_id', '=', 'rm.id')
            ->leftJoin('receipts as rc', 'units.id', '=', 'rc.unit_id')
            ->when($propertyId && $propertyId !== 'all', fn($q) => $q->where('units.property_id', $propertyId))
            ->where('rc.start_date', '<=', $startDate->format('Y-m-d'))
            ->where('rc.end_date', '>=', $endDate->format('Y-m-d'))
            ->selectRaw("
                COUNT(DISTINCT rc.id) AS `occupied_room`,
                (COUNT(rm.id) - COUNT(DISTINCT rc.id)) AS `vacant_room`,
                (COUNT(DISTINCT rc.id) / NULLIF(COUNT(rm.id), 0)) * 100 AS `occupancy_rate`,
                AVG(CASE 
                    WHEN rc.receipt_cycle = 'daily' THEN rm.daily_price
                    WHEN rc.receipt_cycle = 'weekly' THEN rm.weekly_price
                    WHEN rc.receipt_cycle = 'monthly' THEN rm.monthly_price
                    ELSE rm.annual_price
                END) AS `average_rate`
            ")
            ->toSql();

        return Inertia::render('reports/Occupancy', [
            "occupancy" => $occupancy,
            "properties" => Property::all(),
        ]);
    }

    public function financial()
    {
        return Inertia::render('reports/Financial', [
            'properties' => Property::all()
        ]);
    }

    /**
     * Overview Statistics for Report Index
     */
    public function stats(Request $request)
    {
        $propertyId = $request->property_id;
        $range = $request->range ?? '30days';

        $endDate = now();
        $startDate = match ($range) {
            'today' => now()->startOfDay(),
            'yesterday' => now()->subDay()->startOfDay(),
            '60days' => now()->subDays(60),
            default => now()->subDays(30),
        };

        // If yesterday, end date should also be yesterday
        if ($range === 'yesterday') {
            $endDate = now()->subDay()->endOfDay();
        }

        // Summary logic for main reports hub
        $incomeStats = Receipt::query()
            ->when($propertyId && $propertyId !== 'all', fn($q) => $q->where('property_id', $propertyId))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('SUM(total) as total_income, AVG(total) as avg_income')
            ->first();

        // Expense Stats
        $expenseStats = Expense::query()
            ->when($propertyId && $propertyId !== 'all', fn($q) => $q->where('property_id', $propertyId))
            ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->selectRaw('SUM(amount) as total_expense, AVG(amount) as avg_expense')
            ->first();

        // Debt Stats (Outstanding Remaining)
        $debtStats = Receipt::query()
            ->when($propertyId && $propertyId !== 'all', fn($q) => $q->where('property_id', $propertyId))
            ->where('remaining', '>', 0)
            ->selectRaw('SUM(remaining) as total_debt, AVG(remaining) as avg_debt')
            ->first();

        // Occupancy Stats
        $totalUnits = Unit::when($propertyId && $propertyId !== 'all', fn($q) => $q->where('property_id', $propertyId))->count();
        $occupiedUnits = Receipt::query()
            ->when($propertyId && $propertyId !== 'all', fn($q) => $q->where('property_id', $propertyId))
            ->where('start_date', '<=', $endDate->format('Y-m-d'))
            ->where('end_date', '>=', $startDate->format('Y-m-d'))
            ->distinct('unit_id')
            ->count();

        return response()->json([
            'income' => $incomeStats->total_income ?? 0,
            'avg_income' => $incomeStats->avg_income ?? 0,
            'expense' => $expenseStats->total_expense ?? 0,
            'avg_expense' => $expenseStats->avg_expense ?? 0,
            'debt' => $debtStats->total_debt ?? 0,
            'avg_debt' => $debtStats->avg_debt ?? 0,
            'occupancy_rate' => $totalUnits > 0 ? ($occupiedUnits / $totalUnits) * 100 : 0,
            'total_units' => $totalUnits,
            'occupied_units' => $occupiedUnits
        ]);
    }

    /**
     * Financial Trends and Breakdown
     */
    public function finance(Request $request)
    {
        $propertyId = $request->property_id;
        $range = $request->range ?? '30days';
        $startDateInput = $request->start_date;
        $endDateInput = $request->end_date;

        $startDate = now()->subDays(30);
        $endDate = now();

        if ($range === 'custom' && $startDateInput && $endDateInput) {
            $startDate = \Carbon\Carbon::parse($startDateInput);
            $endDate = \Carbon\Carbon::parse($endDateInput);
        } else {
            $startDate = match ($range) {
                '7days' => now()->subDays(7),
                '60days' => now()->subDays(60),
                'year' => now()->subYear(),
                default => now()->subDays(30),
            };
        }

        // Daily Income Trend
        $incomeTrend = Receipt::query()
            ->when($propertyId && $propertyId !== 'all', fn($q) => $q->where('property_id', $propertyId))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE(created_at) as date, SUM(total) as amount')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Daily Expense Trend
        $expenseTrend = Expense::query()
            ->when($propertyId && $propertyId !== 'all', fn($q) => $q->where('property_id', $propertyId))
            ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->selectRaw('date, SUM(amount) as amount')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Income Summary by Category (Breakdown)
        // Note: Legacy code just called it "rental", but we can expand if model supports categories.
        $incomeSummary = [
            ['category' => 'Rental', 'amount' => $incomeTrend->sum('amount')]
        ];

        // Expense Summary by Category
        $expenseSummary = Expense::query()
            ->when($propertyId && $propertyId !== 'all', fn($q) => $q->where('property_id', $propertyId))
            ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->selectRaw('category, SUM(amount) as amount')
            ->groupBy('category')
            ->get();

        return response()->json([
            'income_trend' => $incomeTrend,
            'expense_trend' => $expenseTrend,
            'income_summary' => $incomeSummary,
            'expense_summary' => $expenseSummary,
            'period' => [
                'start' => $startDate->toDateString(),
                'end' => $endDate->toDateString()
            ]
        ]);
    }

    /**
     * Detailed Profit and Loss Statement
     */
    public function profitLoss(Request $request)
    {
        $propertyId = $request->property_id;
        $range = $request->range ?? '30days';

        $startDate = now()->subDays(30);
        $endDate = now();

        // In a real app, this would use similar date logic as above
        
        $incomeDetails = Receipt::query()
            ->with(['unit.property', 'tenant'])
            ->when($propertyId && $propertyId !== 'all', fn($q) => $q->where('property_id', $propertyId))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        $expenseDetails = Expense::query()
            ->when($propertyId && $propertyId !== 'all', fn($q) => $q->where('property_id', $propertyId))
            ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->get();

        return response()->json([
            'income' => $incomeDetails,
            'expense' => $expenseDetails,
            'total_income' => $incomeDetails->sum('total'),
            'total_expense' => $expenseDetails->sum('amount'),
            'net_profit' => $incomeDetails->sum('total') - $expenseDetails->sum('amount')
        ]);
    }
}
