<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Category;
use App\Models\Post;

class AutoPostTest extends TestCase
{
    use RefreshDatabase;

    protected $secret = 'arhana_secret_auto_post_key_2026';

    protected function setUp(): void
    {
        parent::setUp();
        // Set up the env variable for the test environment
        config(['app.auto_post_secret' => $this->secret]);
        putenv("AUTO_POST_SECRET={$this->secret}");
    }

    public function test_it_blocks_request_without_secret_key()
    {
        $response = $this->postJson(route('api.auto-post'), [
            'title' => 'Test Judul',
            'content' => 'Test Konten',
            'category' => 'Ekonomi',
        ]);

        $response->assertStatus(401);
        $response->assertJsonFragment([
            'success' => false,
            'message' => 'Unauthorized. Invalid or missing API Token/Secret Key.',
        ]);
    }

    public function test_it_blocks_request_with_invalid_secret_key()
    {
        $response = $this->postJson(route('api.auto-post'), [
            'title' => 'Test Judul',
            'content' => 'Test Konten',
            'category' => 'Ekonomi',
        ], [
            'X-API-KEY' => 'salah_token_123',
        ]);

        $response->assertStatus(401);
    }

    public function test_it_returns_validation_errors_for_invalid_inputs()
    {
        $response = $this->postJson(route('api.auto-post'), [
            // missing title and content
            'category' => 'Ekonomi',
        ], [
            'X-API-KEY' => $this->secret,
        ]);

        $response->assertStatus(422);
        $response->assertJsonFragment([
            'success' => false,
            'message' => 'Validation error.',
        ]);
        $response->assertJsonValidationErrors(['title', 'content']);
    }

    public function test_it_successfully_creates_post_and_category_if_it_doesnt_exist()
    {
        $this->assertDatabaseCount('posts', 0);
        $this->assertDatabaseCount('categories', 0);

        $response = $this->postJson(route('api.auto-post'), [
            'title' => 'Tekanan Global Ekonomi Indonesia Tumbuh',
            'content' => 'Ini adalah konten berita yang lengkap untuk pengujian auto post.',
            'category' => 'Ekonomi',
            'excerpt' => 'Kutipan singkat berita.',
        ], [
            'X-API-KEY' => $this->secret,
        ]);

        $response->assertStatus(201);
        $response->assertJsonFragment([
            'success' => true,
            'message' => 'Post successfully created.',
        ]);

        $this->assertDatabaseCount('posts', 1);
        $this->assertDatabaseCount('categories', 1);

        $post = Post::first();
        $this->assertEquals('Tekanan Global Ekonomi Indonesia Tumbuh', $post->title);
        $this->assertEquals('Kutipan singkat berita.', $post->excerpt);
        $this->assertEquals('Ini adalah konten berita yang lengkap untuk pengujian auto post.', $post->content);

        $category = Category::first();
        $this->assertEquals('Ekonomi', $category->name);
        $this->assertEquals('news', $category->type);

        $this->assertTrue($post->categories->contains($category->id));
    }

    public function test_it_uses_existing_category_if_already_exists()
    {
        $existingCategory = Category::create([
            'name' => 'Ekonomi',
            'slug' => 'ekonomi',
            'type' => 'news'
        ]);

        $this->assertDatabaseCount('categories', 1);

        $response = $this->postJson(route('api.auto-post'), [
            'title' => 'Laporan Ekonomi Terbaru',
            'content' => 'Isi berita ekonomi.',
            'category' => 'Ekonomi',
        ], [
            'X-API-KEY' => $this->secret,
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseCount('categories', 1); // No new category should be created
        
        $post = Post::latest()->first();
        $this->assertTrue($post->categories->contains($existingCategory->id));
    }
}
