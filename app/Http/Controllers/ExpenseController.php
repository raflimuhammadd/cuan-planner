<?php

namespace App\Http\Controllers;

use App\Enums\MonthEnum;
use App\Http\Resources\ExpenseResource;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;
use Inertia\Response;

class ExpenseController extends Controller implements HasMiddleware
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
        $expenses = Expense::query()
            ->select([
                'id',
                'user_id',
                'date',
                'description',
                'nominal',
                'type',
                'type_detail_id',
                'payment_id',
                'notes',
                'month',
                'year',
                'created_at',
            ])

            ->where('user_id', Auth::id())
            ->filter(request()->only(['search', 'month', 'year']))
            ->sorting(request()->only(['field', 'direction']))
            ->with(['typeDetail', 'payment'])
            ->paginate(request()->load ?? 10);

        return inertia('Expenses/Index', [
            'pageSettings' => fn() => [
                'title' => 'Pengeluaran',
                'subtitle' => 'Menampilkan semua data pengeluaran yang tersedia pada akun anda.',
            ],
            'expenses' => fn() => ExpenseResource::collection($expenses)->additional([
                'meta' => [
                    'has_pages' => $expenses->hasPages(),
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
                ['label' => 'Pengeluaran'],
            ],
            
            'months' => fn() => MonthEnum::options(),
            'years' => fn() => range(start: 2020, end: now()->year),
        ]);
    }

}
