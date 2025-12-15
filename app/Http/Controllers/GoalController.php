<?php

namespace App\Http\Controllers;

use App\Http\Resources\GoalResource;
use App\Models\Goal;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Inertia\Response;
use Illuminate\Support\Facades\Auth;

class GoalController extends Controller implements HasMiddleware
{
    
    public static function middleware(): array
    {
        return [
            new Middleware('auth'),
        ];
    }

    
    // create method index
    public function index(): Response
    {
        $goals = Goal::query()
            ->select(['id', 'user_id', 'name', 'percentage', 'nominal', 'monthly_saving',
            'deadline', 'beginning_balance', 'created_at'])

            ->where('user_id', Auth::id())
            ->filter(request()->only(['search']))
            ->sorting(request()->only(['field', 'direction']))
            ->paginate(request()->load ?? 10);

            return inertia('Savings/index', [
                'pageSettings' => fn() => [
                    'title' => 'Tujuan Menabung',
                    'subtitle' => 'Menabung untuk pendidikan, Liburan atau Investasi Masa Depan.',
                    'banner'=> [
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

                'state' => fn()=>[
                    'page' => request()->page ?? 1,
                    'search' => request()->serch ?? '',
                    'load' => 10,
                ],

                'items' => fn() => [
                    ['label' => 'Cuan+', 'href' => route('dashboard')],
                    ['label' => 'Tabungan'],
                ],

                'year' => fn() => now()->year,
            ]);
    }
}
