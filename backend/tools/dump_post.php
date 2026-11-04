<?php

declare(strict_types=1);

require __DIR__ . '/laravel_boot.php';

use App\Models\Post;

$id = (int)($argv[1] ?? 0);
if ($id <= 0) {
    fwrite(STDERR, "Usage: php tools/dump_post.php <id>\n");
    exit(2);
}

$post = Post::findOrFail($id);
echo $post->content . PHP_EOL;

