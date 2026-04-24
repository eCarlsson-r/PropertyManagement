<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        if (DB::getDriverName() === 'pgsql') {
            DB::statement("CREATE INDEX tenants_embedding_idx ON tenants USING hnsw(embedding vector_cosine_ops);");
            DB::statement("CREATE INDEX expenses_embedding_idx ON expenses USING hnsw(embedding vector_cosine_ops);");
            DB::statement("CREATE INDEX properties_embedding_idx ON properties USING hnsw(embedding vector_cosine_ops);");
            DB::statement("CREATE INDEX rules_embedding_idx ON rules USING hnsw(embedding vector_cosine_ops);");
            DB::statement("CREATE INDEX receipts_embedding_idx ON receipts USING hnsw(embedding vector_cosine_ops);");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('DROP INDEX IF EXISTS tenants_embedding_idx;');
            DB::statement('DROP INDEX IF EXISTS expenses_embedding_idx;');
            DB::statement('DROP INDEX IF EXISTS properties_embedding_idx;');
            DB::statement('DROP INDEX IF EXISTS rules_embedding_idx;');
            DB::statement('DROP INDEX IF EXISTS receipts_embedding_idx;');
        }
    }
};