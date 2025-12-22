<?php

namespace App\Http\Controllers;

use App\Enums\MessageType;
use App\Http\Requests\NetWorthRequest;
use App\Http\Resources\NetWorthResource;
use App\Models\NetWorth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;
use Inertia\Response;
use Throwable;

class NetWorthController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('auth'),
            new Middleware('password.confirm'),
            new Middleware('can:view,netWorth', only:['show']),
            new Middleware('can:update,netWorth', only:['edit', 'update']),
            new Middleware('can:delete,netWorth', only:['destroy'])
        ];

    }

    // create method index
    public function index(): Response
    {
        $netWorths = NetWorth::query()
            ->select([
                'id',
                'user_id',
                'net_worth_goal',
                'current_net_worth',
                'amount_left',
                'transaction_per_month',
                'year',
                'created_at',
            ])
            ->where('user_id', Auth::id())
            ->filter(request()->only(['search']))
            ->sorting(request()->only(['field', 'direction']))
            ->paginate(request()->load ?? 10);

        return inertia('NetWorths/Index', [
            'pageSettings' => fn() => [
                'title' => 'Kekayaan Bersih',
                'subtitle' => 'Menampilkan semua data kekayaan bersih yang tersedia pada akun anda.',
            ],
            'netWorths' => fn() => NetWorthResource::collection($netWorths)->additional([
                'meta' => [
                    'has_pages' => $netWorths->hasPages(),
                ],
            ]),

            'state' => fn() => [
                'page' => request()->page ?? 1,
                'search' => request()->search ?? '',
                'load' => 10,
            ],

            'items' => fn() => [
                ['label' => 'Cuan+', 'href' => route('dashboard')],
                ['label' => 'Kekayaan Bersih'],
            ],


        ]);
    }

    // method create
    public function create(): Response
    {
        return inertia('NetWorths/Create', [
            'pageSettings' => fn() => [
                'title' => 'Tambah Kekayaan Bersih',
                'subtitle' => 'Buat metode pembayaran baru disini. Klik simpan setelah selesai.',
                'method' => 'POST',
                'action' => route('net-worths.store'),
            ],
            'items' => fn() => [
                ['label' => 'Cuan+', 'href' => route('dashboard')],
                ['label' => 'Kekayaan Bersih', 'href' => route('net-worths.index')],
                ['label' => 'Tambah Kekayaan Bersih'],
            ],
            'years' => fn() => range(start: now()->year - 5, end: now()->year + 5),
        ]);
    }

    // method store
    public function store(NetWorthRequest $request): RedirectResponse
    {
        try {
            NetWorth::create([
                'user_id' => Auth::id(),
                'net_worth_goal' => $net_worth_goal = $request->net_worth_goal,
                'current_net_worth' => 0,
                'amount_left' => $net_worth_goal,
                'transaction_per_month' => $request->transaction_per_month,
                'year' => $request->year ?? now()->year,
            ]);

            flashMessage(MessageType::CREATED->message('Kekayaan Bersih'));
            return to_route('net-worths.index');
        } catch (Throwable $e) {
            flashMessage(MessageType::ERROR->message(error: $e->getMessage()), 'error');
            return to_route('net-worths.index');
        }
    }

    // method show
    public function show(NetWorth $netWorth): Response
    {
        return inertia('NetWorths/Show', [
            'pageSettings' => fn() => [
                'title' => 'Detail Kekayaan Bersih',
                'subtitle' => 'Menampilkan kekayaan bersih yang anda miliki.',
            ],
            'items' => fn() => [
                ['label' => 'Cuan+', 'href' => route('dashboard')],
                ['label' => 'Kekayaan Bersih', 'href' => route('net-worths.index')],
                ['label' => $netWorth->id],
            ],
            'netWorth' => fn() => $netWorth,
        ]);
    }

    // method edit
    public function edit(NetWorth $netWorth): Response
    {
        return inertia('NetWorths/Edit', [
            'pageSettings' => fn() => [
                'title' => 'Ubah Kekayaan Bersih',
                'subtitle' => 'Ubah metode pembayaran disini. Klik simpan setelah selesai.',
                'method' => 'PUT',
                'action' => route('net-worths.update', $netWorth),
            ],
            'items' => fn() => [
                ['label' => 'Cuan+', 'href' => route('dashboard')],
                ['label' => 'Kekayaan Bersih', 'href' => route('net-worths.index')],
                ['label' => 'Ubah Kekayaan Bersih'],
            ],
            'years' => fn() => range(start: now()->year - 5, end: now()->year + 5),
            'netWorth' => fn() => $netWorth,
        ]);
    }


    // method update
    public function update(NetWorthRequest $request, NetWorth $netWorth): RedirectResponse
    {
        try {
            $netWorth->update([
                'net_worth_goal' => $net_worth_goal = $request->net_worth_goal,
                'amount_left' => $net_worth_goal,
                'transaction_per_month' => $request->transaction_per_month,
            ]);

            flashMessage(MessageType::UPDATED->message('Kekayaan Bersih'));
            return to_route('net-worths.index');
        } catch (Throwable $e) {
            flashMessage(MessageType::ERROR->message(error: $e->getMessage()), 'error');
            return to_route('net-worths.index');
        }
    }

    // method delete
    public function destroy(NetWorth $netWorth): RedirectResponse
    {
        try {
            $netWorth->delete();

            flashMessage(MessageType::DELETED->message('Kekayaan Bersih'));
            return to_route('net-worths.index');
        } catch (Throwable $e) {
            flashMessage(MessageType::ERROR->message(error: $e->getMessage()), 'error');
            return to_route('net-worths.index');
        }
    }
}
