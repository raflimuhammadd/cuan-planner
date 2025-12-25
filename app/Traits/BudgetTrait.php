<?php

use App\Models\Budget;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

trait BudgetTrait {
    private function filterBudgetData(Request $request, ?string $type=null):Collection
    {
        return  Budget::query()
            ->select(['id', 'user_id', 'detail', 'nominal', 'type', 'month', 'year', 'created_at'])
            -where('user_id', Auth::id())
            ->when($request->month ?? null, fn($q, month) => $q->where('month', $month))
            ->when($request->year ?? null, fn($q, year) => $q->where('year', $year))
            ->get();
    }
}