<?php

namespace App\Http\Controllers;

use App\Http\Requests\ClearFinanceTransactionsRequest;
use App\Http\Requests\DeleteFinanceTransactionRequest;
use App\Http\Requests\StoreFinanceTransactionRequest;
use App\Models\FinanceTransaction;
use App\Services\FinanceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FinanceController extends Controller
{
    public function __construct(
        private FinanceService $financeService
    ) {}

    public function index(Request $request)
    {
        $filters = $request->validate([
            'from_date' => ['nullable', 'date'],
            'to_date' => ['nullable', 'date'],
            'type' => ['nullable', 'in:income,expense,transfer'],
            'account' => ['nullable', 'in:cash,bank'],
            'search' => ['nullable', 'string', 'max:255'],
        ]);

        $data = $this->financeService->getFinancePageData($filters);

        return view('finance.index', $data);
    }

    public function create()
    {
        return view('finance.create', [
            'transaction' => new FinanceTransaction([
                'date' => now()->toDateString(),
                'type' => 'income',
            ]),
            'formMode' => 'create',
        ]);
    }

    public function store(StoreFinanceTransactionRequest $request)
    {
        $validated = $request->validated();
        $validated['created_by'] = $request->user()?->id;

        FinanceTransaction::query()->create($validated);

        return redirect()->route('finance.index')
            ->with('success', __('Transaction added successfully.'));
    }

    public function edit(FinanceTransaction $transaction)
    {
        return view('finance.edit', [
            'transaction' => $transaction,
            'formMode' => 'edit',
        ]);
    }

    public function update(StoreFinanceTransactionRequest $request, FinanceTransaction $transaction)
    {
        $validated = $request->validated();
        $transaction->update($validated);

        return redirect()->route('finance.index')
            ->with('success', __('Transaction updated successfully.'));
    }

    public function destroy(DeleteFinanceTransactionRequest $request, FinanceTransaction $transaction)
    {
        DB::transaction(function () use ($transaction) {
            $transaction->delete();
        });

        return redirect()->route('finance.index')
            ->with('success', __('Transaction deleted successfully.'));
    }

    public function clearAll(ClearFinanceTransactionsRequest $request)
    {
        $deletedCount = $this->financeService->clearAllTransactionsAndResetOpeningBalances();

        return redirect()->route('finance.index')
            ->with('success', __('Cleared :count transaction(s) and reset opening cash/bank balances to zero.', ['count' => $deletedCount]));
    }
}
