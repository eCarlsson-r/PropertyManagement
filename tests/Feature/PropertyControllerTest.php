<?php

namespace Tests\Feature;

use App\Models\Location;
use App\Models\Property;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class PropertyControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    private function validPropertyData(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Test Property',
            'currency' => 'IDR',
            'owner_name' => 'John Owner',
            'owner_country_code' => '+62',
            'owner_mobile' => '81234567890',
            'manager_name' => 'Jane Manager',
            'manager_country_code' => '+62',
            'manager_mobile' => '81234567891',
            'notes' => 'Test notes',
            'location' => [
                'address' => '123 Main St',
                'country' => 'Indonesia',
                'province' => 'DKI Jakarta',
                'city' => 'Jakarta',
                'district' => 'Central',
                'subdistrict' => 'Menteng',
                'postal' => '10310',
            ],
        ], $overrides);
    }

    private function createProperty(): Property
    {
        $property = Property::create([
            'name' => 'Test Property',
            'currency' => 'IDR',
            'owner_name' => 'John Owner',
            'owner_country_code' => '+62',
            'owner_mobile' => '81234567890',
            'manager_name' => 'Jane Manager',
            'manager_country_code' => '+62',
            'manager_mobile' => '81234567891'
        ]);

        Location::create([
            'property_id' => $property->id,
            'address' => '123 Main St',
            'country' => 'Indonesia',
            'province' => 'DKI Jakarta',
            'city' => 'Jakarta',
            'district' => 'Central',
            'subdistrict' => 'Menteng',
            'postal' => '10310',
        ]);

        return $property;
    }

    // ── Index ────────────────────────────────────────────────

    public function test_index_requires_authentication(): void
    {
        $this->get(route('properties.index'))->assertRedirect(route('login'));
    }

    public function test_index_renders_properties_page(): void
    {
        $this->createProperty();

        $this->actingAs($this->user)
            ->get(route('properties.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('properties/index')
                ->has('properties.data', 1)
                ->has('filters')
            );
    }

    public function test_index_filters_by_search(): void
    {
        $this->createProperty();

        $this->actingAs($this->user)
            ->get(route('properties.index', ['search' => 'Nonexistent']))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->has('properties.data', 0)
            );
    }

    // ── Create ───────────────────────────────────────────────

    public function test_create_renders_form(): void
    {
        $this->actingAs($this->user)
            ->get(route('properties.create'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('properties/create')
                ->has('locations')
            );
    }

    // ── Store ────────────────────────────────────────────────

    public function test_store_creates_property_with_location(): void
    {
        $data = $this->validPropertyData();

        $this->actingAs($this->user)
            ->post(route('properties.store'), $data)
            ->assertRedirect(route('properties.index'));

        $this->assertDatabaseHas('properties', ['name' => 'Test Property']);
        $this->assertDatabaseHas('locations', ['address' => '123 Main St']);
    }

    public function test_store_validates_required_fields(): void
    {
        $this->actingAs($this->user)
            ->post(route('properties.store'), [])
            ->assertSessionHasErrors(['name', 'currency', 'owner_name', 'manager_name']);
    }

    // ── Edit ─────────────────────────────────────────────────

    public function test_edit_renders_form_with_property(): void
    {
        $property = $this->createProperty();

        $this->actingAs($this->user)
            ->get(route('properties.edit', $property))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('properties/edit')
                ->has('property')
                ->has('locations')
                ->has('rooms')
            );
    }

    // ── Update ───────────────────────────────────────────────

    public function test_update_modifies_property(): void
    {
        $property = $this->createProperty();

        $this->actingAs($this->user)
            ->put(route('properties.update', $property), $this->validPropertyData([
                'name' => 'Updated Property',
            ]))
            ->assertRedirect(route('properties.index'));

        $this->assertDatabaseHas('properties', ['name' => 'Updated Property']);
    }

    // ── Destroy ──────────────────────────────────────────────

    public function test_destroy_deletes_property(): void
    {
        $property = $this->createProperty();

        $this->actingAs($this->user)
            ->delete(route('properties.destroy', $property))
            ->assertRedirect(route('properties.index'));

        $this->assertDatabaseMissing('properties', ['id' => $property->id]);
    }
}
