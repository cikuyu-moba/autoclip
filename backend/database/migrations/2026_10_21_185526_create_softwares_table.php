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
        Schema::create('softwares', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->string('developer')->nullable();
            $table->string('version')->default('1.0.0');
            $table->string('license')->default('Freeware'); // Freeware, Open Source, Gratis
            $table->string('os_compatibility')->default('Windows 10 / 11');
            $table->string('file_size')->nullable();
            $table->text('download_url');
            $table->string('official_website')->nullable();
            $table->string('icon')->nullable(); // URL or storage path
            $table->text('description')->nullable();
            $table->longText('features')->nullable();
            $table->unsignedBigInteger('views_count')->default(0);
            $table->unsignedBigInteger('downloads_count')->default(0);
            $table->string('github_repo')->nullable(); // e.g. "obsproject/obs-studio"
            $table->string('sync_source')->default('manual'); // 'manual', 'github', 'rss'
            $table->timestamp('last_synced_at')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('softwares');
    }
};
