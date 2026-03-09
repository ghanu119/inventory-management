<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\FinanceTransaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FinanceFeatureTest extends TestCase
{
    use RefreshDatabase;

    private function createCompany(float $openingCash = 0, float $openingBank = 0): Company
    {
        return Company::query()->create([
            'name' => 'Test Company',
            'opening_cash_balance' => $openingCash,
            'opening_bank_balance' => $openingBank,
        ]);
    }

    public function test_finance_page_requires_authentication(): void
    {
        User::factory()->create();

        $response = $this->get(route('finance.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_finance_page_loads_for_authenticated_users(): void
    {
        $user = User::factory()->create();
        $this->createCompany(1000, 500);

        $response = $this->actingAs($user)->get(route('finance.index'));

        $response->assertOk();
        $response->assertSee('Finance');
        $response->assertSee('Available Cash');
    }

    public function test_transaction_type_validation_rules_are_enforced(): void
    {
        $user = User::factory()->create();
        $this->createCompany();

        $expenseInvalid = $this->actingAs($user)->post(route('finance.transactions.store'), [
            'date' => now()->toDateString(),
            'type' => 'expense',
            'amount' => 100,
            'account_to' => 'cash',
        ]);
        $expenseInvalid->assertSessionHasErrors(['account_from', 'account_to']);

        $transferInvalid = $this->actingAs($user)->post(route('finance.transactions.store'), [
            'date' => now()->toDateString(),
            'type' => 'transfer',
            'amount' => 100,
            'account_from' => 'cash',
            'account_to' => 'cash',
        ]);
        $transferInvalid->assertSessionHasErrors(['account_to']);
    }

    public function test_balances_and_period_stats_are_computed_correctly(): void
    {
        $user = User::factory()->create();
        $this->createCompany(1000, 2000);

        FinanceTransaction::query()->create([
            'date' => now()->toDateString(),
            'type' => 'income',
            'amount' => 500,
            'account_to' => 'cash',
            'created_by' => $user->id,
        ]);
        FinanceTransaction::query()->create([
            'date' => now()->toDateString(),
            'type' => 'expense',
            'amount' => 200,
            'account_from' => 'bank',
            'created_by' => $user->id,
        ]);
        FinanceTransaction::query()->create([
            'date' => now()->toDateString(),
            'type' => 'transfer',
            'amount' => 300,
            'account_from' => 'cash',
            'account_to' => 'bank',
            'created_by' => $user->id,
        ]);

        $response = $this->actingAs($user)->get(route('finance.index'));

        $response->assertOk();
        $response->assertSee('₹1,200.00', false); // Cash: 1000 + 500 - 300
        $response->assertSee('₹2,100.00', false); // Bank: 2000 + 300 - 200
        $response->assertSee('₹3,300.00', false); // Total
        $response->assertSee('₹800.00', false); // Inflow: 500 + 300
        $response->assertSee('₹500.00', false); // Outflow: 200 + 300
        $response->assertSee('₹300.00', false); // Net
    }

    public function test_period_filter_limits_statistics_and_listing(): void
    {
        $user = User::factory()->create();
        $this->createCompany();

        FinanceTransaction::query()->create([
            'date' => now()->subMonth()->startOfMonth()->toDateString(),
            'type' => 'income',
            'amount' => 1000,
            'account_to' => 'cash',
            'note' => 'Old entry',
            'created_by' => $user->id,
        ]);
        FinanceTransaction::query()->create([
            'date' => now()->toDateString(),
            'type' => 'income',
            'amount' => 250,
            'account_to' => 'bank',
            'note' => 'Current entry',
            'created_by' => $user->id,
        ]);

        $response = $this->actingAs($user)->get(route('finance.index', [
            'from_date' => now()->startOfMonth()->toDateString(),
            'to_date' => now()->endOfMonth()->toDateString(),
        ]));

        $response->assertOk();
        $response->assertSee('Current entry');
        $response->assertDontSee('Old entry');
        $response->assertSee('₹250.00', false);
        $response->assertDontSee('₹1,250.00', false);
    }

    public function test_delete_transaction_removes_entry_and_updates_balance(): void
    {
        $user = User::factory()->create();
        $this->createCompany(500, 0);

        $transaction = FinanceTransaction::query()->create([
            'date' => now()->toDateString(),
            'type' => 'income',
            'amount' => 100,
            'account_to' => 'cash',
            'created_by' => $user->id,
        ]);

        $this->actingAs($user)->delete(route('finance.transactions.destroy', $transaction), [
            'confirm' => 1,
        ])->assertRedirect(route('finance.index'));

        $this->assertDatabaseMissing('finance_transactions', ['id' => $transaction->id]);

        $response = $this->actingAs($user)->get(route('finance.index'));
        $response->assertSee('₹500.00', false);
    }

    public function test_clear_all_deletes_transactions_and_resets_opening_balances(): void
    {
        $user = User::factory()->create();
        $company = $this->createCompany(700, 900);

        FinanceTransaction::query()->create([
            'date' => now()->toDateString(),
            'type' => 'income',
            'amount' => 100,
            'account_to' => 'cash',
            'created_by' => $user->id,
        ]);
        FinanceTransaction::query()->create([
            'date' => now()->toDateString(),
            'type' => 'expense',
            'amount' => 50,
            'account_from' => 'bank',
            'created_by' => $user->id,
        ]);

        $this->actingAs($user)->post(route('finance.clear-all'), [
            'confirm' => 1,
        ])->assertRedirect(route('finance.index'));

        $this->assertDatabaseCount('finance_transactions', 0);
        $this->assertDatabaseHas('companies', [
            'id' => $company->id,
            'opening_cash_balance' => '0.00',
            'opening_bank_balance' => '0.00',
        ]);
    }

    public function test_dashboard_finance_cards_match_finance_balances(): void
    {
        $user = User::factory()->create();
        $this->createCompany(100, 400);

        FinanceTransaction::query()->create([
            'date' => now()->toDateString(),
            'type' => 'income',
            'amount' => 50,
            'account_to' => 'cash',
            'created_by' => $user->id,
        ]);
        FinanceTransaction::query()->create([
            'date' => now()->toDateString(),
            'type' => 'expense',
            'amount' => 25,
            'account_from' => 'bank',
            'created_by' => $user->id,
        ]);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertOk();
        $response->assertSee('Available Cash');
        $response->assertSee('₹150.00', false);
        $response->assertSee('₹375.00', false);
        $response->assertSee('₹525.00', false);
    }
}

