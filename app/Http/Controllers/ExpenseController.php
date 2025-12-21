<?php

namespace App\Http\Controllers;

use App\Enums\BudgetType;
use App\Enums\MessageType;
use App\Enums\MonthEnum;
use App\Http\Requests\ExpenseRequest;
use App\Http\Resources\ExpenseResource;
use App\Models\Budget;
use App\Models\Expense;
use App\Models\Payment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;
use Inertia\Response;
use Throwable;

use function Laravel\Prompts\select;
use function PHPSTORM_META\map;

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

    // method create
    public function create(): Response
    {
        return inertia('Expenses/Create', [
            'pageSettings' => fn() => [
                'title' => 'Tambah Pengeluaran',
                'subtitle' => 'Buat pengeluaran baru disini. Klik simpan setelah selesai.',
                'method' => 'POST',
                'action' => route('expenses.store'),
            ],
            'items' => fn() => [
                ['label' => 'Cuan+', 'href' => route('dashboard')],
                ['label' => 'Pengeluaran', 'href' => route('expenses.index')],
                ['label' => 'Tambah Pengeluaran'],
            ],
            'months' => fn() => MonthEnum::options(),
            'years' => fn() => range(start: 2020, end: now()->year),
            'types' => fn() => BudgetType::options(['INCOME']),
            'payments' => fn() => Payment::query()
                ->select(['id', 'name'])
                ->where('user_id', Auth::id())
                ->get()
                ->map(fn($item) => [
                    'value' => $item->id,
                    'label' => $item->name,
                ]),
                'budgets' => fn() => request()->type
                ? Budget::query()
                    ->select([
                        'id', 
                        'user_id', 
                        'type', 
                        'detail', 
                        'month', 
                        'year'
                    ])
                    ->where('user_id', Auth::id())
                    ->where('type', request()->type)

                    ->get()
                    ->map(fn($item) => [
                        'value' => $item->id,
                        'label' => $item->detail,
                        'month' => $item->month,
                        'year' => $item->year,

                    ])
                : [],

                'state' => fn()=> [
                    'type' => request()->type ?? '',
                ]

        ]);
    }


    // method store
    public function store(ExpenseRequest $request): RedirectResponse
    {
        try {
            Expense::create([
                'user_id' => Auth::id(),
                'date' => $request->date,
                'description' => $request->description,
                'nominal' => $request->nominal,
                'type' => $request->type,
                'type_detail_id' => $request->type_detail_id,
                'payment_id' => $request->payment_id,
                'notes' => $request->notes,
                'month' => $request->month,
                'year' => $request->year,
            ]);

            flashMessage(MessageType::CREATED->message('Pengeluaran'));
            return to_route('expenses.index');
        } catch (Throwable $e) {
            flashMessage(MessageType::ERROR->message(error: $e->getMessage()), 'error');
            return to_route('expenses.index');
        }
    }

}
