<?php

namespace Tests\Feature;

use App\Models\Property;
use App\Models\Receipt;
use App\Models\Room;
use App\Models\Tenant;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class ReceiptControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Property $property;
    protected Room $room;
    protected Unit $unit;
    protected Tenant $tenant;

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

        $this->room = Room::create([
            'name' => 'Standard',
            'area' => 25,
            'daily_price' => 200000,
            'weekly_price' => 1200000,
            'monthly_price' => 4500000,
            'annual_price' => 50000000,
        ]);

        $this->unit = Unit::create([
            'property_id' => $this->property->id,
            'name' => 'Unit A1',
            'floor' => 1,
            'room_id' => $this->room->id,
        ]);

        $this->tenant = Tenant::create([
            'name' => 'John Doe',
            'country_code' => '+62',
            'mobile' => '81234567890',
            'unit_id' => $this->unit->id,
            'cycle' => 'monthly',
            'emergency_contact_name' => 'Jane',
            'emergency_contact_relationship' => 'Sister',
            'emergency_contact_country_code' => '+62',
            'emergency_contact_mobile' => '81234567891',
            'deposit' => 5000000,
        ]);
    }

    private function validReceiptData(array $overrides = []): array
    {
        return array_merge([
            'tenant_id' => $this->tenant->id,
            'property_id' => $this->property->id,
            'room_id' => $this->room->id,
            'unit_id' => $this->unit->id,
            'receipt_cycle' => 'monthly',
            'start_date' => '2026-04-01',
            'end_date' => '2026-04-30',
            'discount_type' => 'none',
            'discount_amount' => 0,
            'discount_percent' => 0,
            'tax' => 0,
            'total' => 4500000,
            'down_payment' => 4500000,
            'remaining' => 0,
            'fully_paid' => true,
            'notes' => 'Monthly rent',
            'adults' => 1,
            'children' => 0,
            'babies' => 0,
            'pets' => 0,
            'reminder' => 'none',
        ], $overrides);
    }

    private function createReceipt(): Receipt
    {
        return Receipt::create([
            'tenant_id' => $this->tenant->id,
            'unit_id' => $this->unit->id,
            'receipt_cycle' => 'monthly',
            'start_date' => '2026-04-01',
            'end_date' => '2026-04-30',
            'discount_type' => 'none',
            'discount_amount' => 0,
            'discount_percent' => 0,
            'tax' => 0,
            'total' => 4500000,
            'down_payment' => 4500000,
            'remaining' => 0,
            'fully_paid' => true,
            'notes' => 'Monthly rent',
            'adults' => 1,
            'children' => 0,
            'babies' => 0,
            'pets' => 0,
            'reminder' => 'none',
        ]);
    }

    // ── Index ────────────────────────────────────────────────

    public function test_index_requires_authentication(): void
    {
        $this->get(route('receipts.index'))->assertRedirect(route('login'));
    }

    public function test_index_renders_receipts_page(): void
    {
        $this->createReceipt();

        $this->actingAs($this->user)
            ->get(route('receipts.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('receipts/index')
                ->has('receipts.data', 1)
                ->has('filters')
            );
    }

    // ── Create ───────────────────────────────────────────────

    public function test_create_renders_form(): void
    {
        $this->actingAs($this->user)
            ->get(route('receipts.create'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('receipts/create')
                ->has('properties')
                ->has('units')
                ->has('tenants')
            );
    }

    // ── Store ────────────────────────────────────────────────

    public function test_store_creates_receipt(): void
    {
        $this->actingAs($this->user)
            ->post(route('receipts.store'), $this->validReceiptData())
            ->assertRedirect(route('receipts.index'));

        $this->assertDatabaseHas('receipts', [
            'tenant_id' => $this->tenant->id,
            'unit_id' => $this->unit->id,
        ]);
    }

    public function test_store_validates_required_fields(): void
    {
        $this->actingAs($this->user)
            ->post(route('receipts.store'), [])
            ->assertSessionHasErrors([
                'tenant_id', 'property_id', 'unit_id', 'receipt_cycle',
                'start_date', 'end_date', 'total',
            ]);
    }

    // ── Edit ─────────────────────────────────────────────────

    public function test_edit_renders_form_with_receipt(): void
    {
        $receipt = $this->createReceipt();

        $this->actingAs($this->user)
            ->get(route('receipts.edit', $receipt))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('receipts/edit')
                ->has('receipt')
                ->has('properties')
                ->has('units')
                ->has('tenants')
            );
    }

    // ── Update ───────────────────────────────────────────────

    public function test_update_modifies_receipt(): void
    {
        $receipt = $this->createReceipt();

        $this->actingAs($this->user)
            ->put(route('receipts.update', $receipt), $this->validReceiptData([
                'notes' => 'Updated notes',
            ]))
            ->assertRedirect(route('receipts.index'));

        $this->assertDatabaseHas('receipts', ['id' => $receipt->id, 'notes' => 'Updated notes']);
    }

    // ── Destroy ──────────────────────────────────────────────

    public function test_destroy_deletes_receipt(): void
    {
        $receipt = $this->createReceipt();

        $this->actingAs($this->user)
            ->delete(route('receipts.destroy', $receipt))
            ->assertRedirect(route('receipts.index'));

        $this->assertDatabaseMissing('receipts', ['id' => $receipt->id]);
    }
}
