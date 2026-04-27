<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->string("country_code");
            $table->string("mobile");
            $table->string("email")->nullable();
            $table->foreignId("unit_id")->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->string("cycle");
            $table->date("birth_date")->nullable();
            $table->string("gender")->nullable();
            $table->string("marital_status")->nullable();
            $table->string("address")->nullable();
            $table->string("account_bank")->nullable();
            $table->string("account_number")->nullable();
            $table->string("account_owner")->nullable();
            $table->string("currency")->nullable();
            $table->text("notes")->nullable();
            $table->string("emergency_contact_name");
            $table->string("emergency_contact_relationship");
            $table->string("emergency_contact_country_code");
            $table->string("emergency_contact_mobile");
            $table->integer("deposit");
            $table->string("deposit_bank")->nullable();
            $table->string("deposit_number")->nullable();
            if (DB::getDriverName() === 'pgsql') $table->vector('embedding', 768)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tenants');
    }
};
