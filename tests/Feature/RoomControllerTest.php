<?php

namespace Tests\Feature;

use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class RoomControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    private function validRoomData(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Standard Room',
            'area' => 25,
            'daily_price' => 200000,
            'weekly_price' => 1200000,
            'monthly_price' => 4500000,
            'annual_price' => 50000000,
        ], $overrides);
    }

    // ── Index ────────────────────────────────────────────────

    public function test_index_requires_authentication(): void
    {
        $this->get(route('rooms.index'))->assertRedirect(route('login'));
    }

    public function test_index_renders_rooms_page(): void
    {
        Room::create($this->validRoomData());

        $this->actingAs($this->user)
            ->get(route('rooms.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('rooms/index')
                ->has('rooms.data', 1)
                ->has('filters')
            );
    }

    public function test_index_filters_by_search(): void
    {
        Room::create($this->validRoomData());

        $this->actingAs($this->user)
            ->get(route('rooms.index', ['search' => 'Nonexistent']))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->has('rooms.data', 0)
            );
    }

    // ── Create ───────────────────────────────────────────────

    public function test_create_renders_form(): void
    {
        $this->actingAs($this->user)
            ->get(route('rooms.create'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('rooms/create')
                ->has('properties')
            );
    }

    // ── Store ────────────────────────────────────────────────

    public function test_store_creates_room(): void
    {
        $this->actingAs($this->user)
            ->post(route('rooms.store'), $this->validRoomData())
            ->assertRedirect(route('rooms.index'));

        $this->assertDatabaseHas('rooms', ['name' => 'Standard Room']);
    }

    public function test_store_validates_required_fields(): void
    {
        $this->actingAs($this->user)
            ->post(route('rooms.store'), [])
            ->assertSessionHasErrors(['name', 'area', 'daily_price', 'weekly_price', 'monthly_price', 'annual_price']);
    }

    // ── Edit ─────────────────────────────────────────────────

    public function test_edit_renders_form_with_room(): void
    {
        $room = Room::create($this->validRoomData());

        $this->actingAs($this->user)
            ->get(route('rooms.edit', $room))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('rooms/edit')
                ->has('room')
                ->has('properties')
            );
    }

    // ── Update ───────────────────────────────────────────────

    public function test_update_modifies_room(): void
    {
        $room = Room::create($this->validRoomData());

        $this->actingAs($this->user)
            ->put(route('rooms.update', $room), $this->validRoomData([
                'name' => 'Deluxe Room',
            ]))
            ->assertRedirect(route('rooms.index'));

        $this->assertDatabaseHas('rooms', ['name' => 'Deluxe Room']);
    }

    public function test_update_validates_required_fields(): void
    {
        $room = Room::create($this->validRoomData());

        $this->actingAs($this->user)
            ->put(route('rooms.update', $room), [])
            ->assertSessionHasErrors(['name', 'area']);
    }

    // ── Destroy ──────────────────────────────────────────────

    public function test_destroy_deletes_room(): void
    {
        $room = Room::create($this->validRoomData());

        $this->actingAs($this->user)
            ->delete(route('rooms.destroy', $room))
            ->assertRedirect(route('rooms.index'));

        $this->assertDatabaseMissing('rooms', ['id' => $room->id]);
    }
}
