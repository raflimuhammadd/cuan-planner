<?php

namespace App\Http\Controllers;

use App\Enums\MonthEnum;
use App\Http\Resources\IncomeResource;
use App\Models\Income;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;
use Inertia\Response;

class IncomeController extends Controller implements HasMiddleware
{
    public static function middleware():array
    {
        return [
            new Middleware('auth'),
        ];
    }

    // create method index
    public function index(): Response
    {
        $incomes = Income::query()
            ->select([
                'id',
                'user_id',
                'search_id',
                'date',
                'nominal',
                'notes',
                'month',
                'year',
                'created_at',
            ])

            ->where('user_id', Auth::id())
            ->filter(request()->only(['search', 'month', 'year']))
            ->sorting(request()->only(['field', 'direction']))
            ->with(['source'])
            ->paginate(request()->load ?? 10);

        return inertia('Incomes/Index', [
            'pageSettings' => fn() => [
                'title' => 'Pemasukan',
                'subtitle' => 'Menampilkan semua data pemasukan yang tersedia pada akun anda.',
            ],
            'incomes' => fn() => IncomeResource::collection($incomes)->additional([
                'meta' => [
                    'has_pages' => $incomes->hasPages(),
                ],
            ]),

            'state' => fn() => [
                'page' => request()->page ?? 1,
                'search' => request()->search ?? '',
                'load' => 10,
                'month' => request()->month ?? MonthEnum::month(now()->month)->value,
                'year' => request()->year ?? now()->year,
            ],

            'items' => fn() => [
                ['label' => 'Cuan+', 'href' => route('dashboard')],
                ['label' => 'Pemasukan'],
            ],
            
            'months' => fn() => MonthEnum::options(),
            'years' => fn() => range(start: 2020, end: now()->year),

        ]);
    }
}
