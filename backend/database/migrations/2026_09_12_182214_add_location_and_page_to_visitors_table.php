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
        Schema::table('visitors', function (Blueprint $table): void {
            $table->string('city')->nullable()->after('ip_address');
            $table->string('region')->nullable()->after('city');
            $table->string('country')->nullable()->after('region');
            $table->string('country_code', 10)->nullable()->after('country');
            $table->string('last_page', 500)->nullable()->after('country_code');
            $table->unsignedInteger('hits')->default(1)->after('last_page');
            $table->text('user_agent')->nullable()->after('hits');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('visitors', function (Blueprint $table): void {
            $table->dropColumn([
                'city',
                'region',
                'country',
                'country_code',
                'last_page',
                'hits',
                'user_agent',
            ]);
        });
    }
};
