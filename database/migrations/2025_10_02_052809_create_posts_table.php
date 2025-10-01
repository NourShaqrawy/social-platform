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
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            $table->foreignId('feeling_id')->nullable()->constrained()->onDelete('set null');

            $table->foreignId('location_id')->nullable()->constrained()->onDelete('set null');

            $table->text('content')->nullable();
            $table->string('media_url')->nullable(); // صورة أو ملف مرفق
            $table->enum('visibility', ['public', 'friends', 'private'])->default('public');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
