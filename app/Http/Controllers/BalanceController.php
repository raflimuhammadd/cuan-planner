<?php

namespace App\Http\Controllers;

use App\Http\Resources\BalanceResource;
use App\Models\Balance;
use App\Models\Goal;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;
use Inertia\Response;

class BalanceController extends Controller
{
    public static function middleware(): array
    {
        return [
            new Middleware('auth'),
        ];
    }

    public function index(Goal $goal): Response
    {
        $balances = Balance::query()
            ->select(['id', 'user_id', 'goal_id', 'amount', 'created_at'])
            ->where('user_id', Auth::id())
            ->where('goal_id', $goal->id)
            ->filter(request()->only(['search']))
            ->sorting(request()->only(['field', 'direction']))
            ->paginate(request()->load ?? 10);

            return inertia('Savings/Balance/Index', [
                'pageSettings' => fn()=> [
                    'title' => 'Saldo Anda',
                    'subtitle' => "Menampilkan semua tabungan anda pada tujuan {$goal->name}",
                ],

                'balances' => fn() => BalanceResource::collection($balances)->additional([
                    'meta' => [
                        'has_pages' => $balances->hasPages(),
                    ],
                ]),

                'goal' => fn() => $goal,
                'state' => fn() => [
                    'page' => request()->page ?? 1,
                    'search' => request()->search ?? '',
                    'load' => 10,
                ],

                'items' => fn() => [
                    ['label'=> 'Cuan+', 'href' => route('dashboard')],
                    ['label'=> 'Tabungan', 'href' => route('goals.index')],
                    ['label'=> $goal->id, 'href' => route('goals.index')],
                    ['label'=> 'Saldo'],

                ]
            ]);
            
    }
}
