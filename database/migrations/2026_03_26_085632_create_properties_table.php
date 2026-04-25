<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->string("currency");
            $table->string("owner_name");
            $table->string("owner_country_code");
            $table->string("owner_mobile");
            $table->string("manager_name");
            $table->string("manager_country_code");
            $table->string("manager_mobile");
            $table->text("notes")->nullable();
            if (DB::getDriverName() === 'pgsql') $table->vector('embedding', 768)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
