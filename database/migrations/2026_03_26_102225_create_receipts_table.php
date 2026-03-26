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
        Schema::create('receipts', function (Blueprint $table) {
            $table->id();
            $table->foreignId("tenant_id")->constrained("tenants");
            $table->foreignId("unit_id")->constrained("units");
            $table->enum("receipt_cycle", ["daily", "weekly", "monthly", "annual"]);
            $table->date("start_date");
            $table->date("end_date");
            $table->enum("discount_type", ["percentage", "amount"]);
            $table->decimal("discount_amount", 10, 2)->default(0);
            $table->decimal("discount_percent", 3, 2)->default(0);
            $table->decimal("tax", 3, 2)->default(0);
            $table->decimal("total", 10, 2)->default(0);
            $table->decimal("down_payment", 10, 2)->default(0);
            $table->decimal("remaining", 10, 2)->default(0);
            $table->boolean("fully_paid")->default(false);
            $table->text("notes")->nullable();
            $table->integer("adults")->default(0);
            $table->integer("children")->default(0);
            $table->integer("babies")->default(0);
            $table->integer("pets")->default(0);
            $table->enum("reminder", ["1-day-advance", "2-day-advance", "3-day-advance", "7-day-advance", "14-day-advance"]);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('receipts');
    }
};
