<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class AutoPostController extends Controller
{
    /**
     * Store a newly created post from auto post source.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        // 1. Authentication Security Check
        $secretKey = env('AUTO_POST_SECRET', 'arhana_secret_auto_post_key_2026');
        
        $token = $request->header('X-API-KEY') 
            ?? $request->input('api_token') 
            ?? str_replace('Bearer ', '', $request->header('Authorization') ?? '');

        if (empty($token) || $token !== $secretKey) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Invalid or missing API Token/Secret Key.'
            ], 401);
        }

        // 2. Validation
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:200|unique:posts,title',
            'category' => 'required', // Can be category ID (integer) or category name (string)
            'excerpt' => 'nullable|string',
            'content' => 'required|string',
            'image' => 'nullable', // Can be file upload or image URL string
            'published_at' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error.',
                'errors' => $validator->errors()
            ], 422);
        }

        // 3. Resolve Category
        $categoryInput = $request->input('category');
        $category = null;

        if (is_numeric($categoryInput)) {
            $category = Category::where('id', $categoryInput)->first();
        }

        if (!$category) {
            // Find by name or slug
            $category = Category::where('type', 'news')
                ->where(function($query) use ($categoryInput) {
                    $query->where('name', $categoryInput)
                          ->orWhere('slug', Str::slug($categoryInput));
                })->first();
        }

        if (!$category) {
            // Create a new category automatically
            $categoryName = is_numeric($categoryInput) ? 'Kategori Baru' : $categoryInput;
            $category = Category::create([
                'name' => $categoryName,
                'slug' => Str::slug($categoryName),
                'type' => 'news'
            ]);
        }

        // 4. Resolve Image
        $imagePath = null;
        if ($request->hasFile('image')) {
            // Standard file upload
            $imagePath = $request->file('image')->store('posts', 'public');
        } elseif (is_string($request->input('image')) && filter_var($request->input('image'), FILTER_VALIDATE_URL)) {
            // URL download
            try {
                $imageUrl = $request->input('image');
                $response = Http::timeout(15)->get($imageUrl);
                
                if ($response->successful()) {
                    $imageContent = $response->body();
                    
                    // Try to guess extension or use default
                    $extension = 'jpg';
                    $contentType = $response->header('Content-Type');
                    if ($contentType) {
                        if (str_contains($contentType, 'image/png')) {
                            $extension = 'png';
                        } elseif (str_contains($contentType, 'image/webp')) {
                            $extension = 'webp';
                        } elseif (str_contains($contentType, 'image/jpeg')) {
                            $extension = 'jpg';
                        } elseif (str_contains($contentType, 'image/gif')) {
                            $extension = 'gif';
                        }
                    }
                    
                    $filename = 'auto_' . Str::random(20) . '.' . $extension;
                    $imagePath = 'posts/' . $filename;
                    
                    Storage::disk('public')->put($imagePath, $imageContent);
                }
            } catch (\Exception $e) {
                // If download fails, log or ignore, post will be created without image path
            }
        }

        // 5. Save Post
        $publishedAt = $request->input('published_at') 
            ? Carbon::parse($request->input('published_at')) 
            : now();

        $post = Post::create([
            'category_id' => $category->id, // fallback for legacy NOT NULL column
            'title' => $request->input('title'),
            'slug' => Str::slug($request->input('title')),
            'excerpt' => $request->input('excerpt') ?: Str::limit(strip_tags($request->input('content')), 150),
            'content' => $request->input('content'),
            'image' => $imagePath,
            'published_at' => $publishedAt,
        ]);

        // Sync category relations
        $post->categories()->sync([$category->id]);

        return response()->json([
            'success' => true,
            'message' => 'Post successfully created.',
            'post' => $post
        ], 201);

    }
}
