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
        if (!Schema::hasTable('comments') || Schema::hasColumn('comments', 'post_id')) {
            return;
        }

        Schema::table('comments', function (Blueprint $table) {
            $table->foreignId('post_id')->nullable()->after('user_id')->constrained()->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('comments') || !Schema::hasColumn('comments', 'post_id')) {
            return;
        }

        Schema::table('comments', function (Blueprint $table) {
            $table->dropConstrainedForeignId('post_id');
        });
    }
};
