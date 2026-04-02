<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Property;
use App\Models\Unit;
use App\Models\Tenant;
use App\Models\Room;
use App\Models\Receipt;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Inertia\Testing\AssertableInertia as Assert;

class ReportControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Ensure we have a user for authentication
        $this->user = User::factory()->create();
    }

    public function test_dashboard_can_be_rendered()
    {
        $response = $this->actingAs($this->user)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Dashboard')
            ->has('receipt_list')
        );
    }

    public function test_schedule_report_can_be_rendered()
    {
        $response = $this->actingAs($this->user)->get('/reports/schedule');

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('reports/Schedule')
            ->has('units')
            ->has('properties')
            ->has('filters')
        );
    }

    public function test_occupancy_report_can_be_rendered()
    {
        $response = $this->actingAs($this->user)->get('/reports/occupancy');

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('reports/Occupancy')
            ->has('occupancy')
            ->has('properties')
        );
    }

    public function test_financial_report_can_be_rendered()
    {
        $response = $this->actingAs($this->user)->get('/reports/financial');

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('reports/Financial')
            ->has('properties')
        );
    }

    public function test_stats_returns_json_data()
    {
        $response = $this->actingAs($this->user)->getJson('/reports/stats');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'income',
            'avg_income',
            'expense',
            'avg_expense',
            'debt',
            'avg_debt',
            'occupancy_rate',
            'total_units',
            'occupied_units'
        ]);
    }

    public function test_finance_returns_json_data()
    {
        $response = $this->actingAs($this->user)->getJson('/reports/finance-data');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'income_trend',
            'expense_trend',
            'income_summary',
            'expense_summary',
            'period' => [
                'start',
                'end'
            ]
        ]);
    }

    public function test_profit_loss_returns_json_data()
    {
        $response = $this->actingAs($this->user)->getJson('/reports/profit-loss-data');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'income',
            'expense',
            'total_income',
            'total_expense',
            'net_profit'
        ]);
    }
}
