<?php

namespace App\Services;

use App\Models\Company;
use App\Models\FinanceTransaction;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class FinanceService
{
    public function getFinancePageData(array $filters, int $perPage = 20): array
    {
        [$fromDate, $toDate] = $this->resolvePeriod($filters);

        return [
            'filters' => [
                'from_date' => $fromDate->toDateString(),
                'to_date' => $toDate->toDateString(),
                'type' => $filters['type'] ?? '',
                'account' => $filters['account'] ?? '',
                'search' => $filters['search'] ?? '',
            ],
            'balances' => $this->getCurrentBalances(),
            'periodStats' => $this->getPeriodStats($fromDate, $toDate),
            'filteredSummary' => $this->getFilteredSummary($filters),
            'transactions' => $this->getTransactions($filters, $perPage),
        ];
    }

    public function getCurrentBalances(): array
    {
        $opening = $this->getOpeningBalances();
        $totals = FinanceTransaction::query()
            ->selectRaw("COALESCE(SUM(CASE WHEN account_to = 'cash' THEN amount ELSE 0 END), 0) as inflow_cash")
            ->selectRaw("COALESCE(SUM(CASE WHEN account_from = 'cash' THEN amount ELSE 0 END), 0) as outflow_cash")
            ->selectRaw("COALESCE(SUM(CASE WHEN account_to = 'bank' THEN amount ELSE 0 END), 0) as inflow_bank")
            ->selectRaw("COALESCE(SUM(CASE WHEN account_from = 'bank' THEN amount ELSE 0 END), 0) as outflow_bank")
            ->first();

        $cashBalance = $opening['opening_cash'] + ((float) $totals->inflow_cash - (float) $totals->outflow_cash);
        $bankBalance = $opening['opening_bank'] + ((float) $totals->inflow_bank - (float) $totals->outflow_bank);

        return [
            'opening_cash' => $opening['opening_cash'],
            'opening_bank' => $opening['opening_bank'],
            'cash_balance' => $cashBalance,
            'bank_balance' => $bankBalance,
            'total_balance' => $cashBalance + $bankBalance,
        ];
    }

    public function getPeriodStats(Carbon $fromDate, Carbon $toDate): array
    {
        $totals = FinanceTransaction::query()
            ->whereBetween('date', [$fromDate->toDateString(), $toDate->toDateString()])
            ->selectRaw("COALESCE(SUM(CASE WHEN account_to IS NOT NULL THEN amount ELSE 0 END), 0) as total_inflow")
            ->selectRaw("COALESCE(SUM(CASE WHEN account_from IS NOT NULL THEN amount ELSE 0 END), 0) as total_outflow")
            ->selectRaw("COALESCE(SUM(CASE WHEN account_to = 'cash' THEN amount ELSE 0 END), 0) as inflow_cash")
            ->selectRaw("COALESCE(SUM(CASE WHEN account_from = 'cash' THEN amount ELSE 0 END), 0) as outflow_cash")
            ->selectRaw("COALESCE(SUM(CASE WHEN account_to = 'bank' THEN amount ELSE 0 END), 0) as inflow_bank")
            ->selectRaw("COALESCE(SUM(CASE WHEN account_from = 'bank' THEN amount ELSE 0 END), 0) as outflow_bank")
            ->first();

        $totalInflow = (float) $totals->total_inflow;
        $totalOutflow = (float) $totals->total_outflow;
        $inflowCash = (float) $totals->inflow_cash;
        $outflowCash = (float) $totals->outflow_cash;
        $inflowBank = (float) $totals->inflow_bank;
        $outflowBank = (float) $totals->outflow_bank;

        return [
            'from_date' => $fromDate->toDateString(),
            'to_date' => $toDate->toDateString(),
            'total_inflow' => $totalInflow,
            'total_outflow' => $totalOutflow,
            'net_change' => $totalInflow - $totalOutflow,
            'cash_inflow' => $inflowCash,
            'cash_outflow' => $outflowCash,
            'cash_net' => $inflowCash - $outflowCash,
            'bank_inflow' => $inflowBank,
            'bank_outflow' => $outflowBank,
            'bank_net' => $inflowBank - $outflowBank,
        ];
    }

    public function getTransactions(array $filters, int $perPage = 20): LengthAwarePaginator
    {
        return $this->applyFilters(FinanceTransaction::query()->with('creator'), $filters)
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function getFilteredSummary(array $filters): array
    {
        $totals = $this->applyFilters(FinanceTransaction::query(), $filters)
            ->selectRaw('COUNT(*) as total_rows')
            ->selectRaw("COALESCE(SUM(CASE WHEN account_to IS NOT NULL THEN amount ELSE 0 END), 0) as total_inflow")
            ->selectRaw("COALESCE(SUM(CASE WHEN account_from IS NOT NULL THEN amount ELSE 0 END), 0) as total_outflow")
            ->first();

        $inflow = (float) $totals->total_inflow;
        $outflow = (float) $totals->total_outflow;

        return [
            'rows' => (int) $totals->total_rows,
            'inflow' => $inflow,
            'outflow' => $outflow,
            'net' => $inflow - $outflow,
        ];
    }

    public function clearAllTransactionsAndResetOpeningBalances(): int
    {
        return DB::transaction(function () {
            $deleted = FinanceTransaction::query()->count();
            FinanceTransaction::query()->delete();

            Company::query()->update([
                'opening_cash_balance' => 0,
                'opening_bank_balance' => 0,
            ]);

            return $deleted;
        });
    }

    private function applyFilters(Builder $query, array $filters): Builder
    {
        [$fromDate, $toDate] = $this->resolvePeriod($filters);
        $query->whereBetween('date', [$fromDate->toDateString(), $toDate->toDateString()]);

        if (! empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (! empty($filters['account'])) {
            $query->where(function (Builder $q) use ($filters) {
                $q->where('account_from', $filters['account'])
                    ->orWhere('account_to', $filters['account']);
            });
        }

        if (! empty($filters['search'])) {
            $search = trim((string) $filters['search']);
            $query->where('note', 'like', '%' . $search . '%');
        }

        return $query;
    }

    private function resolvePeriod(array $filters): array
    {
        $fromInput = $filters['from_date'] ?? null;
        $toInput = $filters['to_date'] ?? null;

        $fromDate = $fromInput ? Carbon::parse($fromInput)->startOfDay() : now()->startOfMonth();
        $toDate = $toInput ? Carbon::parse($toInput)->endOfDay() : now()->endOfMonth();

        if ($fromDate->greaterThan($toDate)) {
            [$fromDate, $toDate] = [$toDate->copy()->startOfDay(), $fromDate->copy()->endOfDay()];
        }

        return [$fromDate, $toDate];
    }

    private function getOpeningBalances(): array
    {
        $company = Company::query()->select(['opening_cash_balance', 'opening_bank_balance'])->first();

        return [
            'opening_cash' => (float) ($company?->opening_cash_balance ?? 0),
            'opening_bank' => (float) ($company?->opening_bank_balance ?? 0),
        ];
    }
}
