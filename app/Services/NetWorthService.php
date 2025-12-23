<?php

namespace App\Services;

use App\Enums\AssetType;
use App\Models\Asset;
use App\Models\NetWorth;
use App\Models\NetWorthAsset;
use Illuminate\Support\Facades\Auth;

class NetWorthService
{
    /**
     * Get asset nominal sum by type
     */
    public function getAssetNominalSum(NetWorth $netWorth, AssetType $assetType): int
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

    /**
     * Get all asset summaries grouped by type
     */
    public function getAssetSummaries(NetWorth $netWorth): array
    {
        return [
            'assetCashNominalSum' => $this->getAssetNominalSum($netWorth, AssetType::CASH),
            'assetPersonalNominalSum' => $this->getAssetNominalSum($netWorth, AssetType::PERSONAL),
            'assetShortTermNominalSum' => $this->getAssetNominalSum($netWorth, AssetType::SHORTTERM),
            'assetMidTermNominalSum' => $this->getAssetNominalSum($netWorth, AssetType::MIDTERM),
            'assetLongTermNominalSum' => $this->getAssetNominalSum($netWorth, AssetType::LONGTERM),
        ];
    }

    /**
     * Get transactions for a specific asset
     */
    public function getAssetTransactions(Asset $asset)
    {
        return NetWorthAsset::query()
            ->where('asset_id', $asset->id)
            ->orderBy('transaction_date')
            ->get();
    }

    /**
     * Prepare transaction data with padding for empty months
     */
    public function prepareTransactionData($transactions, int $transactionPerMonth = 1): array
    {
        $transactionData = $transactions->map(function ($transaction) {
            return [
                'transaction_date' => $transaction->transaction_date,
                'nominal' => $transaction->nominal ?? null,
            ];
        })->toArray();

        $transactionCount = $transactionPerMonth * 12;
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

    /**
     * Get all net worth assets grouped by type
     */
    public function getNetWorthAssets(NetWorth $netWorth): array
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

    /**
     * Get net worth asset summaries
     */
    public function getNetWorthAssetSummaries(NetWorth $netWorth): array
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

    /**
     * Accumulate transaction summaries
     */
    private function accumulateTransactionSummaries(
        array $transactionData,
        array &$netWorthAssetSummaries,
        string $assetType
    ): void {
        foreach ($transactionData as $index => $transaction) {
            if ($transaction['nominal'] !== null) {
                if (!isset($netWorthAssetSummaries[$assetType][$index])) {
                    $netWorthAssetSummaries[$assetType][$index] = 0;
                }
                $netWorthAssetSummaries[$assetType][$index] += $transaction['nominal'];
            }
        }
    }
}
