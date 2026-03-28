<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        DB::statement('ALTER TABLE expenses ADD COLUMN embedding vector(768);');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE expenses DROP COLUMN embedding;');
    }
};