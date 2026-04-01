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
        // 1. Fixed Admin User
        User::factory()->create([
            'username' => 'admin',
            'name' => 'Administrator',
            'email' => 'admin@urusproperti.com',
            'password' => Hash::make('5050'),
        ]);

        // 2. Define Properties with diverse profiles
        $properties = [
            [
                "name" => "Urus Residence Gg. D",
                "address" => "Jl. K. H. Syekh Abdul Wahab Rokan Gg. D No. 1B",
                "city" => "Medan",
                "type" => "Kos",
                "occupancy_rate" => 7, // 70%
                "rooms" => [
                    ["name" => "Kamar AC Emerald", "price" => 1800000, "count" => 12, "area" => 24],
                    ["name" => "Kamar Non AC Sapphire", "price" => 950000, "count" => 18, "area" => 18]
                ],
                "expense_cats" => ['electricity', 'water', 'cleaning', 'garbage']
            ],
            [
                "name" => "The Sudirman Executive",
                "address" => "SCBD Lot 11, Jl. Jend. Sudirman",
                "city" => "Jakarta",
                "type" => "Apartment",
                "occupancy_rate" => 9, // 90%
                "rooms" => [
                    ["name" => "Elite Studio", "price" => 6500000, "count" => 10, "area" => 42],
                    ["name" => "Royal Two Bedroom", "price" => 12000000, "count" => 5, "area" => 85],
                    ["name" => "Presidential Suite", "price" => 25000000, "count" => 2, "area" => 150]
                ],
                "expense_cats" => ['security', 'elevator', 'internet', 'maintenance', 'insurance']
            ],
            [
                "name" => "Urus Villa Bali Eco",
                "address" => "Jl. Raya Sanggingan No. 88X",
                "city" => "Gianyar",
                "type" => "Villa",
                "occupancy_rate" => 10, // 100% (High Season)
                "rooms" => [
                    ["name" => "Infinity Pool Villa", "price" => 3500000, "count" => 6, "area" => 220, "is_daily" => true]
                ],
                "expense_cats" => ['pool_service', 'garden', 'staff_salary', 'guest_supplies']
            ]
        ];

        foreach ($properties as $propData) {
            $property = Property::create([
                "name" => $propData["name"],
                "owner_name" => fake()->name(),
                "owner_country_code" => "62",
                "owner_mobile" => "812" . rand(10000000, 99999999),
                "manager_name" => fake()->name(),
                "manager_country_code" => "62",
                "manager_mobile" => "812" . rand(10000000, 99999999),
                "account_owner" => "PT Urus Properti Indonesia",
                "account_bank" => "BCA / Mandiri",
                "account_number" => rand(100000000, 999999999),
                "currency" => "IDR"
            ]);

            Location::create([
                "property_id" => $property->id,
                "address" => $propData["address"],
                "country" => "Indonesia",
                "province" => $propData["city"] == "Bali" ? "Bali" : "Daerah Khusus",
                "city" => $propData["city"],
                "district" => "Central Hub",
                "subdistrict" => "Premium Area",
                "postal" => rand(10000, 99999)
            ]);

            Rule::create(["property_id" => $property->id, "title" => "Strictly No Smoking", "description" => "A Rp 2.000.000 fine applies for violations."]);
            Rule::create(["property_id" => $property->id, "title" => "No Pets", "description" => "Pets are not allowed in the premises."]);

            foreach ($propData["rooms"] as $roomData) {
                $isDaily = $roomData["is_daily"] ?? false;
                $room = Room::create([
                    "name" => $roomData["name"],
                    "area" => $roomData["area"],
                    "daily_price" => $isDaily ? $roomData["price"] : 0,
                    "weekly_price" => $isDaily ? $roomData["price"] * 6 : 0,
                    "monthly_price" => $isDaily ? $roomData["price"] * 25 : $roomData["price"],
                    "annual_price" => $isDaily ? $roomData["price"] * 300 : $roomData["price"] * 11
                ]);

                for ($i = 1; $i <= $roomData["count"]; $i++) {
                    $unit = Unit::create([
                        "property_id" => $property->id,
                        "room_id" => $room->id,
                        "name" => ($i < 10 ? '0' : '') . $i . (chr(64 + rand(1, 4))),
                        "floor" => rand(1, 10)
                    ]);

                    if (rand(1, 10) <= $propData["occupancy_rate"]) {
                        $tenant = Tenant::create([
                            "name" => fake()->name(),
                            "country_code" => "62",
                            "mobile" => "8" . rand(100000000, 999999999),
                            "unit_id" => $unit->id,
                            "cycle" => $isDaily ? "daily" : "monthly",
                            "notes" => fake()->sentence(),
                            "emergency_contact_name" => fake()->name(),
                            "emergency_contact_relationship" => "Family",
                            "emergency_contact_country_code" => "62",
                            "emergency_contact_mobile" => "8" . rand(100000000, 999999999),
                            "deposit" => $isDaily ? $roomData["price"] * 2 : $roomData["price"]
                        ]);

                        // Seed financial history for last 12 months for better charts
                        for ($m = 0; $m < 12; $m++) {
                            $date = now()->subMonths($m);
                            $price = $isDaily ? $roomData["price"] * rand(3, 7) : $roomData["price"];
                            $tax = $price * 0.1; // 10% tax
                            $total = $price + $tax;
                            $paid = (rand(0, 10) > 2) ? $total : ($total * 0.5); // 20% chance of partial payment
                            
                            Receipt::create([
                                "tenant_id" => $tenant->id,
                                "unit_id" => $unit->id,
                                "receipt_cycle" => $isDaily ? "daily" : "monthly",
                                "start_date" => $date->startOfMonth()->toDateString(),
                                "end_date" => $date->endOfMonth()->toDateString(),
                                "discount_type" => rand(1, 10) > 8 ? "nominal" : "none",
                                "discount_percent" => 0,
                                "discount_amount" => rand(1, 10) > 8 ? 50000 : 0,
                                "tax" => $tax,
                                "down_payment" => 0,
                                "remaining" => $total - $paid,
                                "total" => $total,
                                "fully_paid" => $paid >= $total,
                                "reminder" => "3-days-advance",
                                "adults" => rand(1, 2),
                                "children" => rand(0, 1),
                                "babies" => 0,
                                "pets" => 0,
                                "created_at" => $date->toDateString()
                            ]);
                        }
                    }
                }
            }

            // Seed seasonal Expenses for last 12 months
            foreach ($propData["expense_cats"] as $cat) {
                for ($m = 0; $m < 12; $m++) {
                    $date = now()->subMonths($m);
                    Expense::create([
                        "date" => $date->toDateString(),
                        "title" => "Monthly " . str_replace('_', ' ', ucfirst($cat)),
                        "property_id" => $property->id,
                        "amount" => rand(1000000, 12000000) * (1 + (rand(-20, 20) / 100)), // Varied amounts
                        "category" => $cat,
                        "notes" => "Standard operational overhead."
                    ]);
                }
            }
        }
    }
}
