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
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->string("email")->nullable();
            $table->string("phone")->nullable();
            $table->string("mobile");
            $table->foreignId("unit_id")->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->enum("cycle", ["daily", "weekly", "monthly", "annual"]);
            $table->integer("deposit");
            $table->text("notes")->nullable();
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
