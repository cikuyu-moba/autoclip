<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->string('type'); // 'pre_test' or 'post_test'
            $table->text('question_text');
            $table->text('options'); // JSON array of options: A, B, C, D
            $table->integer('correct_option'); // index of correct option (0, 1, 2, 3)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
