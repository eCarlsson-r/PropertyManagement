<?php

namespace Tests\Feature;

use App\Models\Location;
use App\Models\Property;
use App\Models\Room;
use App\Models\Tenant;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class TenantControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Property $property;
    protected Room $room;
    protected Unit $unit;

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
    }

    private function validTenantData(array $overrides = []): array
    {
        return array_merge([
            'name' => 'John Doe',
            'country_code' => '+62',
            'mobile' => '81234567890',
            'email' => 'john@example.com',
            'unit' => [
                'property_id' => $this->property->id,
                'room_id' => $this->room->id,
            ],
            'cycle' => 'monthly',
            'birth_date' => '1990-01-15',
            'gender' => 'male',
            'marital_status' => 'single',
            'address' => '123 Main St',
            'account_bank' => 'BCA',
            'account_number' => '1234567890',
            'account_owner' => 'John Doe',
            'currency' => 'IDR',
            'notes' => 'Good tenant',
            'emergency_contact_name' => 'Jane Doe',
            'emergency_contact_relationship' => 'Sister',
            'emergency_contact_country_code' => '+62',
            'emergency_contact_mobile' => '81234567891',
            'deposit' => 5000000,
            'deposit_bank' => 'BCA',
            'deposit_number' => '1234567890',
        ], $overrides);
    }

    private function createTenant(): Tenant
    {
        return Tenant::create([
            'name' => 'Existing Tenant',
            'country_code' => '+62',
            'mobile' => '81234567890',
            'email' => 'existing@example.com',
            'unit_id' => $this->unit->id,
            'cycle' => 'monthly',
            'emergency_contact_name' => 'EC Name',
            'emergency_contact_relationship' => 'Parent',
            'emergency_contact_country_code' => '+62',
            'emergency_contact_mobile' => '81234567800',
            'deposit' => 5000000,
        ]);
    }

    // ── Index ────────────────────────────────────────────────

    public function test_index_requires_authentication(): void
    {
        $this->get(route('tenants.index'))->assertRedirect(route('login'));
    }

    public function test_index_renders_tenants_page(): void
    {
        $this->createTenant();

        $this->actingAs($this->user)
            ->get(route('tenants.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('tenants/index')
                ->has('tenants.data', 1)
                ->has('filters')
            );
    }

    public function test_index_filters_by_search(): void
    {
        $this->createTenant();

        $this->actingAs($this->user)
            ->get(route('tenants.index', ['search' => 'Nonexistent']))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->has('tenants.data', 0)
            );
    }

    // ── Create ───────────────────────────────────────────────

    public function test_create_renders_form(): void
    {
        $this->actingAs($this->user)
            ->get(route('tenants.create'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('tenants/create')
                ->has('properties')
                ->has('units')
            );
    }

    // ── Store ────────────────────────────────────────────────

    public function test_store_creates_tenant(): void
    {
        $this->actingAs($this->user)
            ->post(route('tenants.store'), $this->validTenantData())
            ->assertRedirect(route('tenants.index'));

        $this->assertDatabaseHas('tenants', ['name' => 'John Doe']);
    }

    public function test_store_validates_required_fields(): void
    {
        $this->actingAs($this->user)
            ->post(route('tenants.store'), [])
            ->assertSessionHasErrors(['name', 'country_code', 'mobile', 'cycle', 'emergency_contact_name', 'deposit']);
    }

    // ── Edit ─────────────────────────────────────────────────

    public function test_edit_renders_form_with_tenant(): void
    {
        $tenant = $this->createTenant();

        $this->actingAs($this->user)
            ->get(route('tenants.edit', $tenant))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('tenants/edit')
                ->has('tenant')
                ->has('properties')
                ->has('units')
            );
    }

    // ── Update ───────────────────────────────────────────────

    public function test_update_modifies_tenant(): void
    {
        $tenant = $this->createTenant();

        $this->actingAs($this->user)
            ->put(route('tenants.update', $tenant), $this->validTenantData([
                'name' => 'Updated Tenant',
            ]))
            ->assertRedirect(route('tenants.index'));

        $this->assertDatabaseHas('tenants', ['name' => 'Updated Tenant']);
    }

    // ── Destroy ──────────────────────────────────────────────

    public function test_destroy_deletes_tenant(): void
    {
        $tenant = $this->createTenant();

        $this->actingAs($this->user)
            ->delete(route('tenants.destroy', $tenant))
            ->assertRedirect(route('tenants.index'));

        $this->assertDatabaseMissing('tenants', ['id' => $tenant->id]);
    }
}
