<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Enable ltree extension for PostgreSQL
        if (DB::connection()->getDriverName() === 'pgsql') {
            DB::statement('create extension if not exists ltree');
        }

        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            
            // Use ltree for PostgreSQL, text for SQLite/MySQL
            if (DB::connection()->getDriverName() === 'pgsql') {
                $table->ltree('path')->nullable()->spatialIndex();
            } else {
                $table->text('path')->nullable()->index();
            }

            $table->foreignId('parent_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index('parent_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
