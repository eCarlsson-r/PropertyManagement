<?php

namespace Tests\Feature;

use App\Models\Expense;
use App\Models\Property;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class ExpenseControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Property $property;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();

        $this->property = Property::create([
            'name' => 'Test Property',
            'currency' => 'IDR',
            'owner_name' => 'Owner',
            'owner_country_code' => '+62',
            'owner_mobile' => '81234567890',
            'manager_name' => 'Manager',
            'manager_country_code' => '+62',
            'manager_mobile' => '81234567891'
        ]);
    }

    private function validExpenseData(array $overrides = []): array
    {
        return array_merge([
            'date' => '2026-04-01',
            'property_id' => $this->property->id,
            'title' => 'Plumbing Repair',
            'amount' => 500000,
            'category' => 'fixing',
        ], $overrides);
    }

    private function createExpense(): Expense
    {
        return Expense::create([
            'date' => '2026-04-01',
            'property_id' => $this->property->id,
            'title' => 'Existing Expense',
            'amount' => 500000,
            'category' => 'supplies',
        ]);
    }

    // ── Index ────────────────────────────────────────────────

    public function test_index_requires_authentication(): void
    {
        $this->get(route('expenses.index'))->assertRedirect(route('login'));
    }

    public function test_index_renders_expenses_page(): void
    {
        $this->createExpense();

        $this->actingAs($this->user)
            ->get(route('expenses.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('expenses/index')
                ->has('expenses.data', 1)
                ->has('filters')
            );
    }

    public function test_index_filters_by_search(): void
    {
        $this->createExpense();

        $this->actingAs($this->user)
            ->get(route('expenses.index', ['search' => 'Nonexistent']))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->has('expenses.data', 0)
            );
    }

    public function test_index_filters_by_date(): void
    {
        $this->createExpense();

        $this->actingAs($this->user)
            ->get(route('expenses.index', ['date' => '2026-04-01']))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->has('expenses.data', 1)
            );
    }

    // ── Create ───────────────────────────────────────────────

    public function test_create_renders_form(): void
    {
        $this->actingAs($this->user)
            ->get(route('expenses.create'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('expenses/create')
                ->has('properties')
            );
    }

    // ── Store ────────────────────────────────────────────────

    public function test_store_creates_expense(): void
    {
        $this->actingAs($this->user)
            ->post(route('expenses.store'), $this->validExpenseData())
            ->assertRedirect(route('expenses.index'));

        $this->assertDatabaseHas('expenses', [
            'title' => 'Plumbing Repair',
            'property_id' => $this->property->id,
        ]);
    }

    public function test_store_validates_required_fields(): void
    {
        $this->actingAs($this->user)
            ->post(route('expenses.store'), [])
            ->assertSessionHasErrors(['date', 'property_id', 'title', 'amount', 'category']);
    }

    // ── Edit ─────────────────────────────────────────────────

    public function test_edit_renders_form_with_expense(): void
    {
        $expense = $this->createExpense();

        $this->actingAs($this->user)
            ->get(route('expenses.edit', $expense))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('expenses/edit')
                ->has('expense')
                ->has('properties')
            );
    }

    // ── Update ───────────────────────────────────────────────

    public function test_update_modifies_expense(): void
    {
        $expense = $this->createExpense();

        $this->actingAs($this->user)
            ->put(route('expenses.update', $expense), $this->validExpenseData([
                'title' => 'Updated Expense',
            ]))
            ->assertRedirect(route('expenses.index'));

        $this->assertDatabaseHas('expenses', ['id' => $expense->id, 'title' => 'Updated Expense']);
    }

    // ── Destroy ──────────────────────────────────────────────

    public function test_destroy_deletes_expense(): void
    {
        $expense = $this->createExpense();

        $this->actingAs($this->user)
            ->delete(route('expenses.destroy', $expense))
            ->assertRedirect(route('expenses.index'));

        $this->assertDatabaseMissing('expenses', ['id' => $expense->id]);
    }
}
