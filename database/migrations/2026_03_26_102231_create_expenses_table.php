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
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->date("date");
            $table->foreignId("property_id")->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->string("title");
            $table->decimal("total", 10, 2);
            $table->enum("category", ["marketing", "grocery", "electricity", "water", "salary", "fixing", "supplies", "others"]);
            $table->text("notes")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
