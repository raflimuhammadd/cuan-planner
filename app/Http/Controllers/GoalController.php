<?php

namespace App\Http\Controllers;

use App\Enums\MessageType;
use App\Http\Requests\GoalRequest;
use App\Http\Resources\GoalResource;
use App\Models\Balance;
use App\Models\Goal;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Inertia\Response;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Throwable;


class GoalController extends Controller implements HasMiddleware
{

    public static function middleware(): array
    {
        return [
            new Middleware('auth'),
            new Middleware('can:update,goal', only: ['edit', 'update']),
            new Middleware('can:delete,goal', only: ['destroy']),
        ];
    }


    // create method index
    public function index(): Response
    {
        $goals = Goal::query()
            ->select([
                'id',
                'user_id',
                'name',
                'percentage',
                'nominal',
                'monthly_saving',
                'deadline',
                'beginning_balance',
                'created_at'
            ])

            ->where('user_id', Auth::id())
            ->filter(request()->only(['search']))
            ->sorting(request()->only(['field', 'direction']))
            ->paginate(request()->load ?? 10);

        return inertia('Savings/Index', [
            'pageSettings' => fn() => [
                'title' => 'Tujuan Menabung',
                'subtitle' => 'Menabung untuk pendidikan, Liburan atau Investasi Masa Depan.',
                'banner' => [
                    'title' => 'Tabungan',
                    'subtitle' => 'Wujudkan Impian dengan menabung. Langkah kecil menuju cita cita
                        yang besar',
                ],
            ],
            'goals' => fn() => GoalResource::collection($goals)->additional([
                'meta' => [
                    'has_pages' => $goals->hasPages(),
                ],
            ]),

            'state' => fn() => [
                'page' => request()->page ?? 1,
                'search' => request()->search ?? '',
                'load' => 10,
            ],

            'items' => fn() => [
                ['label' => 'Cuan+', 'href' => route('dashboard')],
                ['label' => 'Tabungan'],
            ],

            'year' => fn() => Carbon::now()->year,

            'count' => fn() => [
                'countGoal' => fn() => Goal::query()->where(
                    'user_id',
                    Auth::id()
                )->count(),
                'countGoalAchieved' => fn() => Goal::query()->where(
                    'user_id',
                    Auth::id()
                )->where('percentage', 100)->count(),
                'countGoalNotAchieved' => fn() => Goal::query()->where(
                    'user_id',
                    Auth::id()
                )->where('percentage', '<', 100)->count(),
                'countBalance' => fn() => Balance::query()->whereHas('goal', fn($query) => $query->where(
                    'user_id',
                    Auth::id()
                ))->sum('amount') + Goal::query()->where('user_id', Auth::id())->sum('beginning_balance')
                ],

                'productivityCount' => fn() => $this->getProductivityCount(),
                
        ]);
    }


    // method create
    public function create(): Response
    {
        return inertia('Savings/Create', [
            'pageSettings' => fn() => [
                'title' => 'Mulai tetapkan tujuan sekarang',
                'subtitle' => 'Dengan tujuan yang jelas, setiap langkah kecil menabung membawa anda
                lebih dekat ke impian besar anda',
                'method' => 'POST',
                'action' => route('goals.store'),
            ],
            'items' => fn() => [
                ['label' => 'Cuan+', 'href' => route('dashboard')],
                ['label' => 'Tabungan', 'href' => route('goals.index')],
                ['label' => 'Tambah Tujuan Menabung'],
            ],
        ]);
    }


    // method store
    public function store(GoalRequest $request): RedirectResponse
    {
        try {
            Goal::create([
                'user_id' => Auth::id(),
                'name' => $request->name,
                'nominal' => $request->nominal,
                'monthly_saving' => $request->monthly_saving,
                'deadline' => $request->deadline,
                'beginning_balance' => $request->beginning_balance,
            ]);

            flashMessage(MessageType::CREATED->message('Tujuan'));
            return to_route('goals.index');
        } catch (Throwable $e) {
            flashMessage(MessageType::ERROR->message(error: $e->getMessage()), 'error');
            return to_route('goals.index');
        }
    }

    // method edit
    public function edit(Goal $goal): Response
    {
        return inertia('Savings/Edit', [
            'pageSettings' => fn() => [
                'title' => 'Mulai tetapkan tujuan sekarang',
                'subtitle' => 'Dengan tujuan yang jelas, setiap langkah kecil menabung membawa anda
                lebih dekat ke impian besar anda',
                'method' => 'PUT',
                'action' => route('goals.update', $goal),
            ],

            'goal' => fn() => $goal,

            'items' => fn() => [
                ['label' => 'Cuan+', 'href' => route('dashboard')],
                ['label' => 'Tabungan', 'href' => route('goals.index')],
                ['label' => 'Perbarui Tujuan Menabung'],
            ],
        ]);
    }


    // method update
    public function update(Goal $goal, GoalRequest $request): RedirectResponse
    {
        try {
            $goal->update([
                'name' => $request->name,
                'nominal' => $request->nominal,
                'monthly_saving' => $request->monthly_saving,
                'deadline' => $request->deadline,
                'beginning_balance' => $request->beginning_balance,
                'percentage' => $goal->calculatePercentage(
                    $request->beginning_balance,
                    $request->nominal,
                    Auth::id()
                ),
            ]);

            flashMessage(MessageType::UPDATED->message('Tujuan'));
            return to_route('goals.index');
        } catch (Throwable $e) {
            flashMessage(MessageType::ERROR->message(error: $e->getMessage()), 'error');
            return to_route('goals.index');
        }
    }



    // method delete
    public function destroy(Goal $goal): RedirectResponse
    {
        try {
            $goal->delete();

            flashMessage(MessageType::DELETED->message('Tujuan'));
            return to_route('goals.index');
        } catch (Throwable $e) {
            flashMessage(MessageType::ERROR->message(error: $e->getMessage()), 'error');
            return to_route('goals.index');
        }
    }

    // method contributor
    public function getProductivityCount():array
    {
        $startDate = Carbon::create(Carbon::now()->year, 1, 1);
        $endDate = Carbon::create(Carbon::now()->year, 12, 31);


        $balance = Balance::query()
            ->where('user_id', Auth::id())
            ->selectRaw('DATE(created_at) as transaction_date, count(*) as count')
            ->wherebetween('created_at', [$startDate, $endDate])
            ->groupBy('transaction_date')
            ->orderBy('transaction_date', 'asc')
            ->get();

        $dates = [];
        $currentDate = $startDate->copy();

        while($currentDate <= $endDate){
            $dates[] = $currentDate->format('Y-m-d');
            $currentDate->addDay();
        }

        $result = [];

        foreach($dates as $date){
            $transactions = $balance->firstWhere('transaction_date', $date);
            $result[] = [
                'transaction_date' => $date,
                'count' => $transactions ? $transactions->count : 0,
            ];
        }

        return $result;

    }
}
