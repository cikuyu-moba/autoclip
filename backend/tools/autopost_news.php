<?php

declare(strict_types=1);

require __DIR__ . '/laravel_boot.php';

use App\Models\Category;
use App\Models\Post;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

/**
 * Reads JSON payload from STDIN:
 * {
 *   "category_slug": "ekonomi",
 *   "title": "...",
 *   "excerpt": "...",
 *   "content_html": "<p>...</p>",
 *   "source_url": "https://...",
 *   "cover": { "theme": "economy" }
 * }
 */

$raw = stream_get_contents(STDIN);
if ($raw === false || trim($raw) === '') {
    fwrite(STDERR, "Expected JSON payload on STDIN.\n");
    exit(2);
}

$payload = json_decode($raw, true);
if (!is_array($payload)) {
    fwrite(STDERR, "Invalid JSON payload.\n");
    exit(2);
}

$categorySlug = (string)($payload['category_slug'] ?? '');
$title = trim((string)($payload['title'] ?? ''));
$excerpt = trim((string)($payload['excerpt'] ?? ''));
$contentHtml = (string)($payload['content_html'] ?? '');
$sourceUrl = trim((string)($payload['source_url'] ?? ''));

if ($categorySlug === '' || $title === '' || $excerpt === '' || $contentHtml === '' || $sourceUrl === '') {
    fwrite(STDERR, "Missing required fields: category_slug, title, excerpt, content_html, source_url.\n");
    exit(2);
}

$category = Category::where('slug', $categorySlug)->first();
if (!$category) {
    fwrite(STDERR, "Category not found for slug: {$categorySlug}\n");
    exit(2);
}

$baseSlug = Str::slug($title);
$slug = $baseSlug;
$suffix = 2;
while (Post::where('slug', $slug)->exists()) {
    $slug = $baseSlug . '-' . $suffix;
    $suffix++;
}

// Generate a simple 1200x630 cover image (GD) and store it under storage/app/public.
$coverRelDir = 'news-covers/' . now()->format('Y/m');
$coverFilename = $slug . '.png';
$coverRelPath = $coverRelDir . '/' . $coverFilename;

$img = imagecreatetruecolor(1200, 630);
if ($img === false) {
    fwrite(STDERR, "Failed to create image canvas.\n");
    exit(2);
}

imagealphablending($img, true);
imagesavealpha($img, true);

// Background gradient.
$top = [13, 38, 76];      // deep blue
$bottom = [0, 140, 255];  // bright sky
for ($y = 0; $y < 630; $y++) {
    $t = $y / 629;
    $r = (int)round($top[0] * (1 - $t) + $bottom[0] * $t);
    $g = (int)round($top[1] * (1 - $t) + $bottom[1] * $t);
    $b = (int)round($top[2] * (1 - $t) + $bottom[2] * $t);
    $col = imagecolorallocate($img, $r, $g, $b);
    imageline($img, 0, $y, 1199, $y, $col);
}

// Decorative circles (suggest "commodities / flow").
$white = imagecolorallocatealpha($img, 255, 255, 255, 80);
$white2 = imagecolorallocatealpha($img, 255, 255, 255, 95);
imagefilledellipse($img, 1050, 120, 520, 520, $white2);
imagefilledellipse($img, 980, 520, 680, 680, $white);
imagefilledellipse($img, 200, 520, 520, 520, $white2);

// Card overlay for readable text.
$card = imagecolorallocatealpha($img, 0, 0, 0, 60);
imagefilledrectangle($img, 70, 80, 1130, 550, $card);

$fontBold = 'C:\\Windows\\Fonts\\arialbd.ttf';
$fontReg = 'C:\\Windows\\Fonts\\arial.ttf';
if (!file_exists($fontBold)) {
    $fontBold = $fontReg;
}

$textWhite = imagecolorallocate($img, 255, 255, 255);
$textSoft = imagecolorallocatealpha($img, 255, 255, 255, 25);
$accent = imagecolorallocate($img, 255, 214, 10);

// Category pill.
$pillBg = imagecolorallocatealpha($img, 0, 0, 0, 35);
imagefilledroundedrectangle($img, 110, 110, 320, 160, 22, $pillBg);
imagettftext($img, 18, 0, 135, 145, $accent, $fontBold, mb_strtoupper($category->name));

// Title (wrapped).
$titleMaxWidth = 920;
$titleFontSize = 44;
$titleX = 110;
$titleY = 235;
$lines = [];
$words = preg_split('/\\s+/', $title) ?: [];
$line = '';
foreach ($words as $w) {
    $test = $line === '' ? $w : ($line . ' ' . $w);
    $box = imagettfbbox($titleFontSize, 0, $fontBold, $test);
    $wPx = $box ? ($box[2] - $box[0]) : 0;
    if ($wPx > $titleMaxWidth && $line !== '') {
        $lines[] = $line;
        $line = $w;
    } else {
        $line = $test;
    }
}
if ($line !== '') {
    $lines[] = $line;
}
$lines = array_slice($lines, 0, 3);
foreach ($lines as $i => $l) {
    imagettftext($img, $titleFontSize, 0, $titleX, $titleY + ($i * 58), $textWhite, $fontBold, $l);
}

// Footer line.
$footer = 'arhana.my.id • ' . now()->format('d M Y');
imagettftext($img, 18, 0, 110, 520, $textSoft, $fontReg, $footer);

// Write to a temp file then store to public disk.
$tmpDir = storage_path('app/tmp');
if (!is_dir($tmpDir)) {
    @mkdir($tmpDir, 0775, true);
}
$tmpFile = $tmpDir . DIRECTORY_SEPARATOR . $coverFilename;
imagepng($img, $tmpFile, 6);
imagedestroy($img);

if (!file_exists($tmpFile) || filesize($tmpFile) < 1000) {
    fwrite(STDERR, "Cover generation failed.\n");
    exit(2);
}

Storage::disk('public')->put($coverRelPath, file_get_contents($tmpFile));

$post = Post::create([
    'category_id' => $category->id,
    'title' => $title,
    'slug' => $slug,
    'excerpt' => $excerpt,
    'content' => $contentHtml . "\n\n" . '<p><strong>Sumber berita:</strong> <a href="' . e($sourceUrl) . '" target=\"_blank\" rel=\"noopener\">' . e($sourceUrl) . '</a></p>',
    'image' => $coverRelPath,
]);

echo json_encode([
    'id' => $post->id,
    'slug' => $post->slug,
    'category' => $category->slug,
    'image' => $post->image,
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . PHP_EOL;

/**
 * Draw a rounded rectangle (GD).
 */
function imagefilledroundedrectangle($image, $x1, $y1, $x2, $y2, $radius, $color): void
{
    imagefilledrectangle($image, $x1 + $radius, $y1, $x2 - $radius, $y2, $color);
    imagefilledrectangle($image, $x1, $y1 + $radius, $x2, $y2 - $radius, $color);

    imagefilledellipse($image, $x1 + $radius, $y1 + $radius, $radius * 2, $radius * 2, $color);
    imagefilledellipse($image, $x2 - $radius, $y1 + $radius, $radius * 2, $radius * 2, $color);
    imagefilledellipse($image, $x1 + $radius, $y2 - $radius, $radius * 2, $radius * 2, $color);
    imagefilledellipse($image, $x2 - $radius, $y2 - $radius, $radius * 2, $radius * 2, $color);
}

