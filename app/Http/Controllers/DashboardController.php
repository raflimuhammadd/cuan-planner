<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Goal;
use App\Models\Income;
use App\Models\NetWorth;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;
use Inertia\Response;

class DashboardController extends Controller implements HasMiddleware
{

    public static function middleware(): array
    {
        return [
            new Middleware('auth'),
        ];
    }


    public function index(): Response
    {
        $incomeSum = Income::query()
            ->where([
                ['user_id', Auth::id()],
                ['year', now()->year],
            ])
            ->sum('nominal');

        $expenseSum = Expense::query()
            ->where([
                ['user_id', Auth::id()],
                ['year', now()->year],
            ])
            ->sum('nominal');

        $balanceSum = Goal::query()
            ->with('balances')
            ->where('user_id', Auth::id())
            ->get()
            ->sum(fn($goal) => $goal->balances->sum('amount') + $goal->beginning_balance);

        $netWorthSum = NetWorth::query()
            ->where([
                ['user_id', Auth::id()],
                ['year', now()->year],
            ])
            ->pluck('amount_left');

        

        return inertia('Dashboard', [
            'sum' => fn() => [
                'incomeSum' => $incomeSum,
                'expenseSum' => $expenseSum,
                'balanceSum' => $balanceSum,
                'netWorthSum' => $netWorthSum,
            ]
        ]);
    }
}
