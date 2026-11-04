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
        Schema::create('api_jobs', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // clipping | edit | export | caption
            $table->string('status')->default('pending'); // pending | processing | completed | failed
            $table->integer('progress')->default(0);
            $table->text('message')->nullable();
            $table->json('data')->nullable();
            $table->json('result')->nullable();
            $table->text('error')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('api_jobs');
    }
};
