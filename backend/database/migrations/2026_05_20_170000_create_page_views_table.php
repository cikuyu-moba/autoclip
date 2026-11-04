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
        Schema::create('page_views', function (Blueprint $table): void {
            $table->id();
            $table->string('ip_address', 45);
            $table->morphs('viewable'); // creates viewable_id (bigint) and viewable_type (varchar)
            $table->date('date');
            $table->timestamps();
            
            // Ensure unique IP address per content item per day
            $table->unique(['ip_address', 'viewable_id', 'viewable_type', 'date'], 'unique_page_view_per_day');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('page_views');
    }
};
