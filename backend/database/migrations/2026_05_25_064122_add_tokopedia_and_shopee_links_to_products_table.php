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
        Schema::table('products', function (Blueprint $table) {
            $table->string('tokopedia_link')->nullable()->after('price');
            $table->string('shopee_link')->nullable()->after('tokopedia_link');
            $table->string('shop_link')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['tokopedia_link', 'shopee_link']);
            $table->string('shop_link')->nullable(false)->change();
        });
    }
};
