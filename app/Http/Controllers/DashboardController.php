<?php

namespace App\Http\Controllers;

use App\Constants\ColorConstants;
use App\Enums\BudgetType;
use App\Models\Budget;
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
            ],
            'budgetChart' => fn() => $this->budgetChart(),
        ]);
    }

    private static function getColor(string $type, array $colors): string
    {
        $cases = BudgetType::cases();
        foreach ($cases as $index => $case) {
            if ($case->value === $type) {
                return $colors[$index % count($colors)];
            }
        }
        return $colors[crc32($type) % count($colors)];
    }

    private function budgetChart(): array
    {
        $budgets = Budget::query()
            ->selectRaw('type, SUM(nominal) as total_nominal')
            ->where([
                ['user_id', Auth::id()],
                ['year', now()->year],
            ])
            ->groupBy('type')
            ->get()
            ->map(function ($budget) {
                return [
                    'type' => $budget->type,
                    'nominals' => (int) $budget->total_nominal,
                    'fill' => self::getColor($budget->type->value, ColorConstants::COLORS),
                ];
            });

        $chartConfigBudget = [
            'nominals' => [
                'label' => 'Nominal',
            ],
        ];

        foreach (BudgetType::cases() as $budgetType) {
            $key = strtolower(str_replace(' ', '_', $budgetType->value));
            $chartConfigBudget[$key] = [
                'label' => $budgetType->value,
                'color' => self::getColor($budgetType->value, ColorConstants::COLORS),
            ];
        }

        return [
            'budgets' => $budgets,
            'chartConfigBudget' => $chartConfigBudget,
        ];
    }
}
