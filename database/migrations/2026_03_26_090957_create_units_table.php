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
        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->foreignId("property_id")->constrained("properties")->cascadeOnDelete();
            $table->string("name");
            $table->integer("floor");
            $table->foreignId("room_id")->constrained("rooms")->cascadeOnDelete();
            $table->boolean("available")->default(true);
            $table->string("condition")->default("clean");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('units');
    }
};
