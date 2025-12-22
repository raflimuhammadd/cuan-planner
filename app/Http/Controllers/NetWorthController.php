<?php

namespace App\Http\Controllers;

use App\Enums\AssetType;
use App\Enums\MessageType;
use App\Http\Requests\NetWorthRequest;
use App\Http\Resources\NetWorthResource;
use App\Models\Asset;
use App\Models\NetWorth;
use App\Models\NetWorthAsset;
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
            new Middleware('can:view,netWorth', only: ['show']),
            new Middleware('can:update,netWorth', only: ['edit', 'update']),
            new Middleware('can:delete,netWorth', only: ['destroy'])
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
    public function show(NetWorth $netWorth):Response
    {

        $netWorthAssets = $this->getNetWorthAssets($netWorth);
        $netWorthAssetSummaries = $this->getNetWorthAssetSummaries($netWorth);

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
            'assetSum' => fn() => $this->getAssetSummaries($netWorth),
            'netWorthAssets' => fn() => $netWorthAssets,
            'netWorthAssetSummaries' => fn() => $netWorthAssetSummaries,
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

    // method private function
    private function getAssetNominalSum(NetWorth $netWorth, AssetType $assetType)
    {
        return $netWorth->assets()
            ->where([
                ['net_worth_id', $netWorth->id],
                ['user_id', Auth::id()],
                ['type', $assetType->value],
            ])
            ->with('netWorthAssets')
            ->get()
            ->pluck('netWorthAssets')
            ->flatten()
            ->sum('nominal');
    }

    private function getAssetSummaries(NetWorth $netWorth)
    {
        return [
            'assetCashNominalSum' => $this->getAssetNominalSum($netWorth, AssetType::CASH),
            'assetPersonalNominalSum' => $this->getAssetNominalSum($netWorth, AssetType::PERSONAL),
            'assetShortTermNominalSum' => $this->getAssetNominalSum($netWorth, AssetType::SHORTTERM),
            'assetMidTermNominalSum' => $this->getAssetNominalSum($netWorth, AssetType::MIDTERM),
            'assetLongTermNominalSum' => $this->getAssetNominalSum($netWorth, AssetType::LONGTERM),
        ];
    }

    private function getAssetTransactions(Asset $asset)
    {
        return NetWorthAsset::query()
            ->where('asset_id', $asset->id)
            ->orderBy('transaction_date')
            ->get();
    }

    private function prepareTransactionData($transactions, $transaction_per_month = 1)
    {
        $transactionData = $transactions->map(function ($transaction) {
            return [
                'transaction_date' => $transaction->transaction_date,
                'nominal' => $transaction->nominal ?? null,
            ];
        })->toArray();

        $transactionCount = $transaction_per_month * 12;
        if (count($transactionData) < $transactionCount) {
            $transactionData = array_merge($transactionData, array_fill(
                0,
                $transactionCount - count($transactionData),
                [
                    'transaction_date' => null,
                    'nominal' => null,
                ]
            ));
        }
        return $transactionData;
    }

    private function accumulateTransactionSummaries($transactionData, $netWorthAssetSummaries, $assetType)
    {
        foreach ($transactionData as $index => $transaction) {
            if ($transaction['nominal'] !== null) {
                if (!isset($netWorthAssetSummaries[$assetType][$index])) {
                    $netWorthAssetSummaries[$assetType][$index] = 0;
                }
                $netWorthAssetSummaries[$assetType][$index] += [$transaction['nominal']];
            }
        }
    }


    private function getNetWorthAssets(NetWorth $netWorth)
    {
        $assetTypes = AssetType::cases();
        $netWorthAssets = [];

        foreach ($assetTypes as $assetType) {
            $assets = Asset::query()
                ->where([
                    ['net_worth_id', $netWorth->id],
                    ['user_id', Auth::id()],
                    ['type', $assetType->value]
                ])
                ->get();

            $assetData = [];
            foreach ($assets as $asset) {
                $transactions = $this->getAssetTransactions($asset);
                $transactionData = $this->prepareTransactionData(
                    $transactions,
                    $asset->netWorth->transaction_per_month
                );

                $assetData[] = [
                    'detail' => $asset->detail,
                    'goal' => $asset->goal,
                    'transactions' => $transactionData,
                ];
            }

            $netWorthAssets[$assetType->value] = $assetData;
        }
        return $netWorthAssets;
    }

    private function getNetWorthAssetSummaries(NetWorth $netWorth)
    {
        $assetTypes = AssetType::cases();
        $netWorthAssetSummaries = [];

        foreach ($assetTypes as $assetType) {
            $assets = Asset::query()
                ->where([
                    ['net_worth_id', $netWorth->id],
                    ['user_id', Auth::id()],
                    ['type', $assetType->value]
                ])
                ->get();

            foreach ($assets as $asset) {
                $transactions = $this->getAssetTransactions($asset);
                $transactionData = $this->prepareTransactionData(
                    $transactions,
                    $asset->netWorth->transaction_per_month
                );

                $this->accumulateTransactionSummaries(
                    $transactionData,
                    $netWorthAssetSummaries,
                    $assetType->value
                );
            }
        }

        return $netWorthAssetSummaries;
    }
}
