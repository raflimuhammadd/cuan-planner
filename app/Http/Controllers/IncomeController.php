<?php

namespace App\Http\Controllers;

use App\Enums\BudgetType;
use App\Enums\MessageType;
use App\Enums\MonthEnum;
use App\Http\Requests\IncomeRequest;
use App\Http\Resources\IncomeResource;
use App\Models\Budget;
use App\Models\Income;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;
use Inertia\Response;
use Throwable;

class IncomeController extends Controller implements HasMiddleware
{
    public static function middleware():array
    {
        return [
            new Middleware('auth'),
            new Middleware('can:update,income', only:['edit', 'update']),
            new Middleware('can:delete, income', only:['destroy']),
        ];
    }

    // create method index
    public function index(): Response
    {
        $incomes = Income::query()
            ->select([
                'id',
                'user_id',
                'source_id',
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

    // method create
    public function create(): Response
    {
        return inertia('Incomes/Create', [
            'pageSettings' => fn() => [
                'title' => 'Tambah Pemasukan',
                'subtitle' => 'Buat pemasukan baru disini. Klik simpan setelah selesai.',
                'method' => 'POST',
                'action' => route('incomes.store'),
            ],
            'items' => fn() => [
                ['label' => 'Cuan+', 'href' => route('dashboard')],
                ['label' => 'Pemasukan', 'href' => route('incomes.index')],
                ['label' => 'Tambah Pemasukan'],
            ],
            'months' => fn() => MonthEnum::options(),
            'years' => fn() => range(start: 2020, end: now()->year),
            'sources' => fn() => Budget::query()
                ->select(['id', 'detail', 'month', 'year', 'type'])
                ->where([
                    ['user_id', Auth::id()],
                    ['month', MonthEnum::month(now()->month)->value],
                    ['year', now()->year],
                    ['type', BudgetType::INCOME->value],
                ])
                ->orderByDesc('year')
                ->orderByDesc('month')
                ->get()
                ->map(fn($item)=> [
                    'value' => $item->id,
                    'label' => $item->detail. ' - '.$item->type->value. '( '.$item->month->value.' - '
                    .$item->year,
                ])
        ]);
    }


    // method store
    public function store(IncomeRequest $request): RedirectResponse
    {
        try {
            Income::create([
                'user_id' => Auth::id(),
                'source_id' => $request->source_id,
                'date' => $request->date,
                'nominal' => $request->nominal,
                'notes' => $request->notes,
                'month' => $request->month,
                'year' => $request->year,
            ]);

            flashMessage(MessageType::CREATED->message('Pemasukan'));
            return to_route('incomes.index');
        } catch (Throwable $e) {
            flashMessage(MessageType::ERROR->message(error: $e->getMessage()), 'error');
            return to_route('incomes.index');
        }
    }

    // method edit
    public function edit(Income $income): Response
    {
        return inertia('Incomes/Edit', [
            'pageSettings' => fn() => [
                'title' => 'Ubat Pemasukan',
                'subtitle' => 'Ubah pemasukan baru disini. Klik simpan setelah selesai.',
                'method' => 'PUT',
                'action' => route('incomes.update', $income),
            ],
            'items' => fn() => [
                ['label' => 'Cuan+', 'href' => route('dashboard')],
                ['label' => 'Pemasukan', 'href' => route('incomes.index')],
                ['label' => 'Ubah Pemasukan'],
            ],
            'income' => fn() => $income->load('source'),
            'months' => fn() => MonthEnum::options(),
            'years' => fn() => range(start: 2020, end: now()->year),
            'sources' => fn() => Budget::query()
                ->select(['id', 'detail', 'month', 'year', 'type'])
                ->where([
                    ['user_id', Auth::id()],
                    ['month', MonthEnum::month(now()->month)->value],
                    ['year', now()->year],
                    ['type', BudgetType::INCOME->value],
                ])
                ->orderByDesc('year')
                ->orderByDesc('month')
                ->get()
                ->map(fn($item)=> [
                    'value' => $item->id,
                    'label' => $item->detail. ' - '.$item->type->value. '( '.$item->month->value.' - '
                    .$item->year,
                ])
        ]);
    }


    // method update
    public function update(Income $income, IncomeRequest $request): RedirectResponse
    {
        try {
            $income->update([
                'source_id' => $request->source_id,
                'date' => $request->date,
                'nominal' => $request->nominal,
                'notes' => $request->notes,
                'month' => $request->month,
                'year' => $request->year,
            ]);

            flashMessage(MessageType::UPDATED->message('Pemasukan'));
            return to_route('incomes.index');
        } catch (Throwable $e) {
            flashMessage(MessageType::ERROR->message(error: $e->getMessage()), 'error');
            return to_route('incomes.index');
        }
    }

    // method delete
    public function destroy(Income $income): RedirectResponse
    {
        try {
            $income->delete();
            flashMessage(MessageType::DELETED->message('Pemasukan'));
            return to_route('incomes.index');
        } catch (Throwable $e) {
            flashMessage(MessageType::ERROR->message(error: $e->getMessage()), 'error');
            return to_route('incomes.index');
        }
    }
}
