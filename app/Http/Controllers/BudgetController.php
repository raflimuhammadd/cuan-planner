<?php

namespace App\Http\Controllers;

use App\Enums\BudgetType;
use App\Enums\MessageType;
use App\Enums\MonthEnum;
use App\Http\Requests\BudgetRequest;
use App\Http\Resources\BudgetResource;
use App\Models\Budget;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;
use Inertia\Response;
use Throwable;

class BudgetController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('auth'),
        ];
    }

        public function index(): Response
    {
        $budgets = Budget::query()
            ->select([
                'id',
                'user_id',
                'detail',
                'nominal',
                'month',
                'year',
                'type',
                'created_at',
            ])

            ->where('user_id', Auth::id())
            ->filter(request()->only(['search', 'month', 'type', 'year']))
            ->sorting(request()->only(['field', 'direction']))
            ->paginate(request()->load ?? 10);

        return inertia('Budgets/Index', [
            'pageSettings' => fn() => [
                'title' => 'Anggaran',
                'subtitle' => 'Menampilkan semua data anggaran yang tersedia pada akun anda.',
            ],
            'budgets' => fn() => BudgetResource::collection($budgets)->additional([
                'meta' => [
                    'has_pages' => $budgets->hasPages(),
                ],
            ]),

            'state' => fn() => [
                'page' => request()->page ?? 1,
                'search' => request()->search ?? '',
                'load' => 10,
                'month' => request()->month ?? MonthEnum::month(now()->month)->value,
                'type' => request()->type ?? '',
                'year' => request()->year ?? now()->year,
            ],

            'items' => fn() => [
                ['label' => 'Cuan+', 'href' => route('dashboard')],
                ['label' => 'Anggaran'],
            ],
            'months' => fn() => MonthEnum::options(),
            'types' => fn() => BudgetType::options(),
            'years' => fn() => range(date('2020'), end: now()->year),

        ]);
    }

    // method create
    public function create(): Response
    {
        return inertia('Budgets/Create', [
            'pageSettings' => fn() => [
                'title' => 'Tambah Anggaran',
                'subtitle' => 'Buat anggaran baru disini. Klik simpan setelah selesai.',
                'method' => 'POST',
                'action' => route('budgets.store'),
            ],
            'items' => fn() => [
                ['label' => 'Cuan+', 'href' => route('dashboard')],
                ['label' => 'Anggaran', 'href' => route('budgets.index')],
                ['label' => 'Tambah Anggaran'],
            ],
            'months' => fn() => MonthEnum::options(),
            'types' => fn() => BudgetType::options(),
            'years' => fn() => range(2020, end: now()->year),
        ]);
    }


    // method store
    public function store(BudgetRequest $request): RedirectResponse
    {
        try {
            Budget::create([
                'user_id' => Auth::id(),
                'detail' => $request->detail,
                'nominal' => $request->nominal,
                'month' => $request->month,
                'year' => $request->year,
                'type' => $request->type,
            ]);

            flashMessage(MessageType::CREATED->message('Anggaran'));
            return to_route('budgets.index');
        } catch (Throwable $e) {
            flashMessage(MessageType::ERROR->message(error: $e->getMessage()), 'error');
            return to_route('budgets.index');
        }
    }

}
