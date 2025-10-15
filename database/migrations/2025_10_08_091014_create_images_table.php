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
       Schema::create('images', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->string('image_path'); // مسار الصورة داخل التخزين
    $table->string('thumbnail')->nullable(); // نسخة مصغرة إن وجدت
    $table->integer('width')->nullable(); // العرض بالبكسل
    $table->integer('height')->nullable(); // الطول بالبكسل
    $table->text('description')->nullable(); // وصف الصورة
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('images');
    }
};
