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

        Rule::create([
            "property_id" => $property->id,
            "title" => "Pembayaran Tepat Waktu",
            "description" => "Semua tenant wajib membayar sewa sebelum tanggal jatuh tempo. Keterlambatan lebih dari 5 hari akan dikenakan denda."
        ]);

        Rule::create([
            "property_id" => $property->id,
            "title" => "Kebersihan",
            "description" => "Tenant wajib menjaga kebersihan kamar dan area bersama."
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
            "country_code" => "62",
            "mobile" => "82276087295",
            "unit_id" => $unit1->id,
            "cycle" => "monthly",
            "notes" => "Tenant is generally polite but has history of paying rent 3-5 days late. Works freelance so income is inconsistent. Requested extension twice in the past.",
            "emergency_contact_name" => "Pak Budi",
            "emergency_contact_relationship" => "Ayah",
            "emergency_contact_country_code" => "62",
            "emergency_contact_mobile" => "81234567890",
            "deposit" => 1000000
        ]);

        $tenant2 = Tenant::create([
            "name" => "Mariani Limbong",
            "country_code" => "62",
            "mobile" => "82164049453",
            "unit_id" => $unit2->id,
            "cycle" => "monthly",
            "notes" => "Tenant has stable job and usually pays on time. Recently reported issues with water supply and complained about maintenance delay.",
            "emergency_contact_name" => "Pak Budi",
            "emergency_contact_relationship" => "Ayah",
            "emergency_contact_country_code" => "62",
            "emergency_contact_mobile" => "81234567890",
            "deposit" => 750000
        ]);

        Tenant::create([
            "name" => "Rizky Pratama",
            "country_code" => "62",
            "mobile" => "81200011122",
            "unit_id" => $unit1->id,
            "cycle" => "monthly",
            "notes" => "Frequently late payments, sometimes up to 10 days overdue. Avoids communication and difficult to contact.",
            "emergency_contact_name" => "Ibu Sari",
            "emergency_contact_relationship" => "Ibu",
            "emergency_contact_country_code" => "62",
            "emergency_contact_mobile" => "81200000001",
            "deposit" => 1000000
        ]);

        Tenant::create([
            "name" => "Dewi Anggraini",
            "country_code" => "62",
            "mobile" => "81399988877",
            "unit_id" => $unit2->id,
            "cycle" => "monthly",
            "notes" => "Very reliable tenant. Always pays early. Keeps room clean and follows all rules.",
            "emergency_contact_name" => "Pak Agus",
            "emergency_contact_relationship" => "Ayah",
            "emergency_contact_country_code" => "62",
            "emergency_contact_mobile" => "81300000002",
            "deposit" => 750000
        ]);

        Tenant::create([
            "name" => "Andi Saputra",
            "country_code" => "62",
            "mobile" => "81233344455",
            "unit_id" => $unit1->id,
            "cycle" => "monthly",
            "notes" => "Occasional late payments. Recently requested discount due to financial issues. Communication is good.",
            "emergency_contact_name" => "Pak Rahmat",
            "emergency_contact_relationship" => "Ayah",
            "emergency_contact_country_code" => "62",
            "emergency_contact_mobile" => "81200000003",
            "deposit" => 1000000
        ]);

        Tenant::create([
            "name" => "Siti Nurhaliza",
            "country_code" => "62",
            "mobile" => "81277766655",
            "unit_id" => $unit2->id,
            "cycle" => "monthly",
            "notes" => "Pays on time but has frequent complaints about noise and neighbors.",
            "emergency_contact_name" => "Ibu Lina",
            "emergency_contact_relationship" => "Ibu",
            "emergency_contact_country_code" => "62",
            "emergency_contact_mobile" => "81200000004",
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
            "adults" => "1",
            "children" => "1",
            "babies" => "0",
            "pets" => "0",
            "notes" => "Tenant has not completed payment and requested extension.",
            "created_at" => "2025-07-25"
        ]);

        Expense::create([
            "date" => "2025-05-10",
            "title" => "Perbaikan Pompa Air",
            "property_id" => $property->id,
            "amount" => 1200000,
            "category" => "fixing",
            "notes" => "Water pump broke down causing complaints from tenants. Urgent repair needed."
        ]);

        Expense::create([
            "date" => "2025-05-15",
            "title" => "Tagihan Listrik Tinggi",
            "property_id" => $property->id,
            "amount" => 1500000,
            "category" => "electricity",
            "notes" => "Electricity usage unusually high this month. Possibly due to AC usage or faulty wiring."
        ]);
    }
}
