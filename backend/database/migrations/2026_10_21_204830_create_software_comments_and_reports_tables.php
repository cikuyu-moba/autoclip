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
        // 1. Software Comments & Requests Table
        Schema::create('software_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('software_id')->constrained('softwares')->onDelete('cascade');
            $table->foreignId('parent_id')->nullable()->constrained('software_comments')->onDelete('cascade');
            $table->string('username');
            $table->string('type', 20)->default('comment'); // 'comment', 'request'
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->text('content');
            $table->boolean('is_approved')->default(true);
            $table->timestamps();
        });

        // 2. Software Broken Link Reports Table
        Schema::create('software_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('software_id')->constrained('softwares')->onDelete('cascade');
            $table->string('reason'); // 'Link 404 / Rusak', 'File Corrupt / Error', 'Versi Lama', 'Lainnya'
            $table->text('note')->nullable();
            $table->string('reporter_ip', 45)->nullable();
            $table->string('status', 20)->default('pending'); // 'pending', 'resolved'
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('software_reports');
        Schema::dropIfExists('software_comments');
    }
};
