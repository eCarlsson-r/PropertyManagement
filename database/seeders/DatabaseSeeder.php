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
        User::factory()->create([
            'username' => 'admin',
            'password' => Hash::make('5050'),
        ]);

        $property = Property::create([
            "name" => "Kos Gang D",
            "owner_name" => "Pak Budi",
            "owner_country_code" => "62",
            "owner_mobile" => "81234567890",
            "manager_name" => "Pak Edo",
            "manager_country_code" => "62",
            "manager_mobile" => "81234599870",
            "account_owner" => "Pak Budi",
            "account_bank" => "BCA",
            "account_number" => "1234567890",
            "currency" => "IDR"
        ]);

        Location::create([
            "property_id" => $property->id,
            "address" => "Jl. K. H. Syekh Abdul Wahab Rokan Gg. D No. 1B",
            "country" => "Indonesia",
            "province" => "Sumatera Utara",
            "city" => "Medan",
            "district" => "Durian",
            "subdistrict" => "Medan Timur",
            "postal" => "20235"
        ]);

        Expense::create([
            "date" => "2025-05-01",
            "title" => "Tagihan Listrik Mei",
            "property_id" => $property->id,
            "total" => "500000",
            "category" => "electricity"
        ]);

        $room1 = Room::create([
            "name" => "Kamar AC",
            "area" => "35",
            "daily_price" => "0",
            "weekly_price" => "0",
            "monthly_price" => "1000000",
            "annual_price" => "0"
        ]);
        
        $room2 = Room::create([
            "name" => "Kamar Non AC",
            "area" => "20",
            "daily_price" => "0",
            "weekly_price" => "0",
            "monthly_price" => "750000",
            "annual_price" => "0"
        ]);

        $unit1 = Unit::create([
            "property_id" => $property->id,
            "room_id" => $room1->id,
            "name" => "1A",
            "floor" => "1"
        ]);

        $unit2 = Unit::create([
            "property_id" => $property->id,
            "room_id" => $room2->id,
            "name" => "2A",
            "floor" => "2"
        ]);
            

        $tenant1 = Tenant::create([
            "name" => "Tince Ezra Desema",
            "mobile" => "82276087295",
            "unit_id" => $unit1->id,
            "cycle" => "monthly",
            "deposit" => 1000000
        ]);

        $tenant2 = Tenant::create([
            "name" => "Mariani Limbong",
            "mobile" => "82164049453",
            "unit_id" => $unit2->id,
            "cycle" => "monthly",
            "deposit" => 750000
        ]);

        $receipt1 = Receipt::create([
            "tenant_id" => $tenant1->id,
            "unit_id" => $unit1->id,
            "receipt_cycle" => "monthly",
            "start_date" => "2025-06-05",
            "end_date" => "2025-08-04",
            "discount_type" => "percentage",
            "discount_percent" => "0",
            "discount_amount" => "0",
            "tax" => "0",
            "down_payment" => "0",
            "remaining" => "0",
            "total" => "1000000",
            "fully_paid" => true,
            "reminder" => "1-day-advance",
            "notes" => "",
            "adults" => "1",
            "children" => "0",
            "babies" => "0",
            "pets" => "0",
            "created_at" => "2025-06-15"
        ]);

        $receipt2 = Receipt::create([
            "tenant_id" => $tenant2->id,
            "unit_id" => $unit2->id,
            "receipt_cycle" => "monthly",
            "start_date" => "2025-07-30",
            "end_date" => "2025-10-29",
            "discount_type" => "percentage",
            "discount_percent" => "0",
            "discount_amount" => "0",
            "tax" => "0",
            "down_payment" => "500000",
            "remaining" => "1750000",
            "total" => "2250000",
            "fully_paid" => false,
            "reminder" => "1-day-advance",
            "notes" => "",
            "adults" => "1",
            "children" => "1",
            "babies" => "0",
            "pets" => "0",
            "created_at" => "2025-07-25"
        ]);
    }
}
