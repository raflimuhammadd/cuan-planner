<?php

namespace App\Http\Controllers;

use App\Enums\LiabilityType;
use App\Enums\MessageType;
use App\Http\Requests\LiabilityRequest;
use App\Http\Resources\LiabilityResource;
use App\Models\Liability;
use App\Models\NetWorth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;
use Inertia\Response;
use Throwable;

class LiabilityController extends Controller implements HasMiddleware
{
    public static function middleware():array
    {
        return [
            new Middleware('auth'),
            new Middleware('can:viewAny,netWorth', only:['index']),
            new Middleware('can:create,netWorth', only:['create', 'store']),
            new Middleware('can:update,netWorth', only:['edit', 'update']),
            new Middleware('can:delete,netWorth', only:['destroy']),
        ];
    }

        // create method index
    public function index(NetWorth $netWorth): Response
    {
        $liabilities = Liability::query()
            ->select([
                'id',
                'net_worth_id',
                'user_id',
                'detail',
                'goal',
                'type',
                'created_at',
            ])
            ->where('net_worth_id', $netWorth->id)
            ->filter(request()->only(['search', 'type']))
            ->sorting(request()->only(['field', 'direction']))
            ->paginate(request()->load ?? 10);

        return inertia('Liabilities/Index', [
            'pageSettings' => fn() => [
                'title' => 'Kewajiban',
                'subtitle' => 'Menampilkan semua data kewajiban yang tersedia pada akun anda.',
            ],
            'liabilities' => fn() => LiabilityResource::collection($liabilities)->additional([
                'meta' => [
                    'has_pages' => $liabilities->hasPages(),
                ],
            ]),

            'state' => fn() => [
                'page' => request()->page ?? 1,
                'search' => request()->search ?? '',
                'type' => request()->type ?? '',
                'load' => 10,
            ],

            'items' => fn() => [
                ['label' => 'Cuan+', 'href' => route('dashboard')],
                ['label' => 'Kekayaan Bersih', 'href' => route('net-worths.index')],
                ['label' => $netWorth->id, 'href' => route('net-worths.show', $netWorth)],
                ['label' => 'Kewajiban'],
            ],
            'netWorth' => fn() => $netWorth,
        ]);
    }

    // method create
    public function create(NetWorth $netWorth): Response
    {
        return inertia('Liabilities/Create', [
            'pageSettings' => fn() => [
                'title' => 'Tambah Kewajiban',
                'subtitle' => 'Buat kewajiban baru disini. Klik simpan setelah selesai.',
                'method' => 'POST',
                'action' => route('liabilities.store', $netWorth),
            ],
            'items' => fn() => [
                ['label' => 'Cuan+', 'href' => route('dashboard')],
                ['label' => 'Kekayaan Bersih', 'href' => route('net-worths.index')],
                ['label' => $netWorth->id, 'href' => route('net-worths.show', $netWorth)],
                ['label' => 'Kewajiban', 'href' => route('liabilities.index', $netWorth)],
                ['label' => 'Tambah Kewajiban'],
            ],
            'netWorth' => fn() => $netWorth,
            'liabilityTypes' => fn() => LiabilityType::options(),
        ]);
    }

    // method store
    public function store(NetWorth $netWorth, LiabilityRequest $request): RedirectResponse
    {
        try {
            $netWorth->liabilities()->create([
                'user_id' => Auth::id(),
                'detail' => $request->detail,
                'goal' => $request->goal,
                'type' => $request->type,
            ]);

            flashMessage(MessageType::CREATED->message('Kewajiban'));
            return to_route('liabilities.index', $netWorth);
        } catch (Throwable $e) {
            flashMessage(MessageType::ERROR->message(error: $e->getMessage()), 'error');
            return to_route('liabilities.index', $netWorth);
        }
    }

        // method edit
    public function edit(NetWorth $netWorth, Liability $liability): Response
    {
        return inertia('Liabilities/Edit', [
            'pageSettings' => fn() => [
                'title' => 'Ubah Kewajiban',
                'subtitle' => 'Ubah kewajiban baru disini. Klik simpan setelah selesai.',
                'method' => 'PUT',
                'action' => route('liabilities.update', [$netWorth, $liability]),
            ],
            'items' => fn() => [
                ['label' => 'Cuan+', 'href' => route('dashboard')],
                ['label' => 'Kekayaan Bersih', 'href' => route('net-worths.index')],
                ['label' => $netWorth->id, 'href' => route('net-worths.show', $netWorth)],
                ['label' => 'Kewajiban', 'href' => route('liabilities.index', $netWorth) ],
                ['label' => 'Ubah Kewajiban'],
            ],
            'netWorth' => fn() => $netWorth,
            'liabilityTypes' => fn() => LiabilityType::options(),
            'liability' => fn() => $liability,
        ]);
    }

    // method update
    public function update(NetWorth $netWorth, Liability $liability, LiabilityRequest $request): RedirectResponse
    {
        try {
            $liability->update([
                'detail' => $request->detail,
                'goal' => $request->goal,
                'type' => $request->type,
            ]);

            flashMessage(MessageType::UPDATED->message('Kewajiban'));
            return to_route('liabilities.index', $netWorth);
        } catch (Throwable $e) {
            flashMessage(MessageType::ERROR->message(error: $e->getMessage()), 'error');
            return to_route('liabilities.index', $netWorth);
        }
    }

    // method delete
    public function destroy(NetWorth $netWorth, Liability $liability): RedirectResponse
    {
        try {
            $liability->delete();

            flashMessage(MessageType::DELETED->message('Kewajiban'));
            return to_route('liabilities.index', $netWorth);
        } catch (Throwable $e) {
            flashMessage(MessageType::ERROR->message(error: $e->getMessage()), 'error');
            return to_route('liabilities.index', $netWorth);
        }
    }
}
