<?php

declare(strict_types=1);

$app = require __DIR__ . '/laravel_boot.php';

use App\Models\Category;
use App\Models\Post;

$categories = Category::orderBy('id')->get(['id', 'name', 'slug']);
$latestPosts = Post::orderByDesc('id')->limit(20)->get(['id', 'title', 'slug', 'category_id', 'created_at']);

echo json_encode([
    'categories' => $categories,
    'latest_posts' => $latestPosts,
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . PHP_EOL;

