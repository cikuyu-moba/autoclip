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
        if (!Schema::hasTable('category_post')) {
            Schema::create('category_post', function (Blueprint $table) {
                $table->id();
                $table->foreignId('post_id')->constrained('posts')->onDelete('cascade');
                $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
                $table->timestamps();
            });

            // Migrate existing category_id from posts to category_post
            $posts = Illuminate\Support\Facades\DB::table('posts')->whereNotNull('category_id')->get();
            foreach ($posts as $post) {
                Illuminate\Support\Facades\DB::table('category_post')->insert([
                    'post_id' => $post->id,
                    'category_id' => $post->category_id,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('category_post');
    }
};
