<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Post;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->query('per_page', 15);
        if (!in_array($perPage, [15, 30, 50, 100])) {
            $perPage = 15;
        }

        $posts = Post::with('categories')->withCount('views')->latest()->paginate($perPage)->withQueryString();
        return view('admin.posts.index', compact('posts'));
    }

    public function create()
    {
        $categories = Category::where('type', 'news')->get();
        return view('admin.posts.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_ids' => 'required|array',
            'category_ids.*' => 'exists:categories,id',
            'title' => 'required|string|max:200|unique:posts,title',
            'excerpt' => 'nullable|string',
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'published_at' => 'nullable|date',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('posts', 'public');
        }

        $publishedAt = $request->published_at ? \Carbon\Carbon::parse($request->published_at) : now();

        $post = Post::create([
            'category_id' => $request->category_ids[0], // fallback for database schema NOT NULL constraint
            'title' => $request->title,
            'slug' => Str::slug($request->title),
            'excerpt' => $request->excerpt ?: Str::limit(strip_tags($request->content), 150),
            'content' => $request->content,
            'image' => $imagePath,
            'published_at' => $publishedAt,
        ]);

        $post->categories()->sync($request->category_ids);

        return redirect()->route('admin.posts.index')->with('success', 'Berita berhasil diterbitkan.');

    }

    public function edit(Post $post)
    {
        $categories = Category::where('type', 'news')->get();
        $post->load('categories');
        return view('admin.posts.edit', compact('post', 'categories'));
    }

    public function update(Request $request, Post $post)
    {
        $request->validate([
            'category_ids' => 'required|array',
            'category_ids.*' => 'exists:categories,id',
            'title' => 'required|string|max:200|unique:posts,title,' . $post->id,
            'excerpt' => 'nullable|string',
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'published_at' => 'nullable|date',
        ]);

        $imagePath = $post->image;
        if ($request->hasFile('image')) {
            if ($post->image) {
                Storage::disk('public')->delete($post->image);
            }
            $imagePath = $request->file('image')->store('posts', 'public');
        }

        $publishedAt = $request->published_at ? \Carbon\Carbon::parse($request->published_at) : now();

        $post->update([
            'category_id' => $request->category_ids[0], // fallback for database schema NOT NULL constraint
            'title' => $request->title,
            'slug' => Str::slug($request->title),
            'excerpt' => $request->excerpt ?: Str::limit(strip_tags($request->content), 150),
            'content' => $request->content,
            'image' => $imagePath,
            'published_at' => $publishedAt,
        ]);

        $post->categories()->sync($request->category_ids);

        return redirect()->route('admin.posts.index')->with('success', 'Berita berhasil diperbarui.');
    }

    public function destroy(Post $post)
    {
        if ($post->image) {
            Storage::disk('public')->delete($post->image);
        }
        
        $post->delete();
        return redirect()->route('admin.posts.index')->with('success', 'Berita berhasil dihapus.');
    }

    public function uploadImage(Request $request)
    {
        if ($request->hasFile('upload')) {
            $request->validate([
                'upload' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048'
            ]);

            $path = $request->file('upload')->store('editor', 'public');
            $url = asset('storage/' . $path);

            return response()->json([
                'url' => $url
            ]);
        }

        return response()->json(['error' => ['message' => 'Gagal mengunggah gambar.']], 400);
    }
}
