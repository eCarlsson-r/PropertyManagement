<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Location;
use App\Models\Property;
use App\Models\Tenant;
use App\Models\Room;
use App\Models\Unit;
use App\Models\Receipt;
use App\Models\Expense;
use App\Models\Rule;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Admin User
        User::factory()->create([
            'username' => 'admin',
            'name' => 'Administrator',
            'email' => 'admin@urusproperti.com',
            'password' => Hash::make('5050'),
        ]);

        // 2. Define Properties
        $properties = [
            [
                "name" => "The Sudirman Executive",
                "owner_name" => "Budi Santoso",
                "address" => "SCBD Lot 11, Jakarta",
                "city" => "Jakarta",
                "type" => "Apartment",
                "occupancy_rate" => 9,
                "account_bank" => "BCA",
                "account_number" => "1234567890",
                "rooms" => [["name" => "Elite Studio", "daily_price" => 0, "weekly_price" => 0, "monthly_price" => 1800000, "annual_price" => 0, "count" => 5, "area" => 42]],
                "expense_cats" => ['fixing', 'electricity', 'water']
            ],
            [
                "name" => "Urus Residence Gg. D",
                "owner_name" => "Siti Aminah",
                "address" => "Jl. K. H. Syekh Abdul Wahab, Medan",
                "city" => "Medan",
                "type" => "Kos",
                "occupancy_rate" => 7,
                "account_bank" => "BNI",
                "account_number" => "1234567890",
                "rooms" => [["name" => "Kamar AC", "daily_price" => 0, "weekly_price" => 0, "monthly_price" => 2500000, "annual_price" => 0, "count" => 5, "area" => 24]],
                "expense_cats" => ['electricity', 'water']
            ]
        ];

        foreach ($properties as $propData) {
            $property = Property::create([
                "name" => $propData["name"],
                "owner_name" => $propData["owner_name"],
                "currency" => "IDR",
                "owner_country_code" => "+62",
                "owner_mobile" => fake()->phoneNumber(),
                "manager_name" => fake()->name(),
                "manager_country_code" => "+62",
                "manager_mobile" => fake()->phoneNumber(),
                "account_owner" => $propData["owner_name"],
                "account_bank" => $propData["account_bank"],
                "account_number" => $propData["account_number"]
            ]);

            $location = Location::create([
                "property_id" => $property->id,
                "address" => $propData["address"],
                "city" => $propData["city"],
                "country" => "Indonesia"
            ]);

            // --- SEEDING TENANTS WITH "INSIGHT TRIGGERS" ---
            foreach ($propData["rooms"] as $roomData) {
                $room = Room::create([
                    "name" => $roomData["name"], 
                    "area" => $roomData["area"], 
                    "daily_price" => $roomData["daily_price"], 
                    "weekly_price" => $roomData["weekly_price"], 
                    "monthly_price" => $roomData["monthly_price"], 
                    "annual_price" => $roomData["annual_price"]
                ]);

                for ($i = 1; $i <= $roomData["count"]; $i++) {
                    $unit = Unit::create(["property_id" => $property->id, "room_id" => $room->id, "name" => "Unit " . $i, "floor" => 1]);

                    // Create one "Risky Tenant" in Jakarta
                    $isRisky = ($i === 1 && $propData['name'] === "The Sudirman Executive");
                    
                    Tenant::create([
                        "name" => $isRisky ? "Marcus Aurelius" : fake()->name(),
                        "country_code" => "+62",
                        "mobile" => fake()->phoneNumber(),
                        "email" => fake()->email(),
                        "unit_id" => $unit->id,
                        "cycle" => 'monthly',
                        "birth_date" => fake()->date(),
                        "gender" => "male",
                        "marital_status" => "single",
                        "address" => fake()->address(),
                        "account_bank" => "BCA",
                        "account_number" => "1234567890",
                        "account_owner" => $isRisky ? "Marcus Aurelius" : fake()->name(),
                        "currency" => "IDR",
                        "notes" => $isRisky 
                            ? "CRITICAL: This tenant has requested 3 rent extensions this quarter. Payment history shows consistent 10-day delays. Neighbors have complained about late-night noise." 
                            : "Standard tenant with no history of complaints or late payments.",
                        "emergency_contact_name" => fake()->name(),
                        "emergency_contact_relationship" => "Family",
                        "emergency_contact_country_code" => "+62",
                        "emergency_contact_mobile" => fake()->phoneNumber(),
                        "deposit" => $roomData["monthly_price"]
                    ]);
                }
            }

            // --- SEEDING EXPENSES WITH "OUTLIER TRIGGERS" ---
            foreach ($propData["expense_cats"] as $cat) {
                for ($m = 0; $m < 3; $m++) {
                    $date = now()->subMonths($m);
                    
                    // Create one "Unusual Expense" in the current month for Jakarta
                    $isSpike = ($m === 0 && $cat === 'fixing' && $propData['name'] === "The Sudirman Executive");

                    Expense::create([
                        "date" => $date->toDateString(),
                        "title" => $isSpike ? "Emergency Elevator Shaft Overhaul" : "Regular " . ucfirst($cat),
                        "property_id" => $property->id,
                        "amount" => $isSpike ? 45000000 : rand(500000, 1500000), 
                        "category" => $cat,
                        "notes" => $isSpike 
                            ? "Major mechanical failure. Emergency parts sourced from Singapore. Cost is 15x higher than monthly budget for maintenance." 
                            : "Routine operational utility bill."
                    ]);
                }
            }
        }
    }
}
