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
        Schema::create('lectures', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->binary('file_content'); // تخزين الملف نفسه بصيغة ثنائية
            $table->string('mime_type'); // نوع الملف (PDF, DOCX, ...)
            $table->string('file_name'); // اسم الملف الأصلي
            $table->foreignId('course_id')->constrained('courses');
            $table->foreignId('professor_id')->constrained('professors');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lectures');
    }
};
