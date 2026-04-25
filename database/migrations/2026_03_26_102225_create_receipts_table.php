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
            $table->foreignId("tenant_id")->constrained("tenants")->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId("unit_id")->constrained("units")->cascadeOnUpdate()->cascadeOnDelete();
            $table->string("receipt_cycle");
            $table->date("start_date");
            $table->date("end_date");
            $table->string("discount_type");
            $table->decimal("discount_amount", 10, 2)->default(0);
            $table->decimal("discount_percent", 3, 2)->default(0);
            $table->decimal("tax", 10, 2)->default(0);
            $table->decimal("total", 10, 2)->default(0);
            $table->decimal("down_payment", 10, 2)->default(0);
            $table->decimal("remaining", 10, 2)->default(0);
            $table->boolean("fully_paid")->default(false);
            $table->text("notes")->nullable();
            $table->integer("adults")->default(0);
            $table->integer("children")->default(0);
            $table->integer("babies")->default(0);
            $table->integer("pets")->default(0);
            $table->string("reminder")->nullable();
            $table->timestamps();
            $table->index('fully_paid');
            $table->index('remaining');
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
