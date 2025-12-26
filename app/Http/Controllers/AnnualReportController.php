<?php

namespace App\Http\Controllers;

use App\Enums\MonthEnum;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class AnnualReportController extends Controller implements HasMiddleware
{
    public static function middleware():array
    {
        return [
            new Middleware('auth'),
            new Middleware('password.confirm'),
        ];
    }

    private function calculateByMonth(Collection $annuals)
    {
        return collect(MonthEnum::cases())->map(function($monthEnum) use ($annuals) {
            $dataFormMonth = $annuals->filter(fn($item) => $item->month->value === $monthEnum->value);

            return [
                'month' => $monthEnum->value,
                'plan' => $dataFormMonth->sum('plan'),
                'actual' => $dataFormMonth->sum('actual'),
            ];
        });
    }

    private function getAnnualDataGroupByMonth(Collection $annualIncomes, Collection $annualSavings, 
    Collection $annualDebts, Collection $annualBills, Collection $annualShoppings)
    {
        return collect(MonthEnum::cases())->mapWithKeys(function($monthEnum)
        use($annualIncomes, $annualSavings, $annualDebts, $annualBills, $annualShoppings) {
            $monthName = $monthEnum->value;

            $categories = [
                'Penghasilan' => [
                    'plan' => $annualIncomes->filter(fn($item) => $item->month->value === $monthName)->sum('plan'),
                    'actual' => $annualIncomes->filter(fn($item) => $item->month->value === $monthName)->sum('actual'),
                ],
                'Tabungan dan Investasi' => [
                    'plan' => $annualSavings->filter(fn($item) => $item->month->value === $monthName)->sum('plan'),
                    'actual' => $annualSavings->filter(fn($item) => $item->month->value === $monthName)->sum('actual'),
                ],
                'Cicilan Hutang' => [
                    'plan' => $annualDebts->filter(fn($item) => $item->month->value === $monthName)->sum('plan'),
                    'actual' => $annualDebts->filter(fn($item) => $item->month->value === $monthName)->sum('actual'),
                ],
                'Tagihan' => [
                    'plan' => $annualBills->filter(fn($item) => $item->month->value === $monthName)->sum('plan'),
                    'actual' => $annualBills->filter(fn($item) => $item->month->value === $monthName)->sum('actual'),
                ],
                'Belanja' => [
                    'plan' => $annualShoppings->filter(fn($item) => $item->month->value === $monthName)->sum('plan'),
                    'actual' => $annualShoppings->filter(fn($item) => $item->month->value === $monthName)->sum('actual'),
                ],
            ];

            
        });
    }
}
