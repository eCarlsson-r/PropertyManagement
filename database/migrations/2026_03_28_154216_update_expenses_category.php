<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        DB::statement("
            ALTER TABLE expenses
            ADD CONSTRAINT expenses_category_check
            CHECK (category IN (
                'marketing','grocery','electricity','water',
                'salary','fixing','supplies','others'
            ));
        ");
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE expenses DROP CONSTRAINT expenses_category_check;');
    }
};