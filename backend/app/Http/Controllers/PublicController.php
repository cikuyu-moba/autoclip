<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Project;
use App\Models\Product;
use App\Models\Post;
use App\Models\Category;
use App\Models\Software;
use App\Models\ContactMessage;

class PublicController extends Controller
{
    public function home()
    {
        $projects = Project::latest()->take(12)->get();
        $products = Product::latest()->take(12)->get();
        $posts = Post::with('categories')->published()->latest()->take(12)->get();
        $softwares = Software::with('category')->active()->orderByRaw('COALESCE(last_synced_at, updated_at) DESC')->take(12)->get();

        return view('home', compact('projects', 'products', 'posts', 'softwares'));
    }

    public function portfolio()
    {
        $projects = Project::latest()->get();
        return view('portfolio', compact('projects'));
    }

    public function shop(Request $request)
    {
        $categories = Category::where('type', 'shop')->withCount('products')->get();
        
        $query = Product::with('categories')->latest();
        
        if ($request->has('category') && !empty($request->query('category'))) {
            $categorySlug = $request->query('category');
            $query->whereHas('categories', function ($q) use ($categorySlug) {
                $q->where('slug', $categorySlug);
            });
        }
        
        if ($request->has('search') && !empty($request->query('search'))) {
            $search = $request->query('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%');
            });
        }
        
        $products = $query->paginate(16);
        return view('shop', compact('products', 'categories'));
    }

    public function news(Request $request)
    {
        $categories = Category::where('type', 'news')->withCount('posts')->get();
        
        $query = Post::with('categories')->published()->latest();
        
        if ($request->has('category') && !empty($request->query('category'))) {
            $categorySlug = $request->query('category');
            $query->whereHas('categories', function ($q) use ($categorySlug) {
                $q->where('slug', $categorySlug);
            });
        }

        if ($request->has('search') && !empty($request->query('search'))) {
            $search = $request->query('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                  ->orWhere('content', 'like', '%' . $search . '%')
                  ->orWhere('excerpt', 'like', '%' . $search . '%');
            });
        }
        
        $posts = $query->paginate(16);
        
        return view('blog.index', compact('posts', 'categories'));
    }

    public function newsShow($slug)
    {
        $post = Post::with('categories')->published()->where('slug', $slug)->firstOrFail();
        
        try {
            \App\Models\PageView::insertOrIgnore([
                'ip_address' => request()->ip(),
                'viewable_id' => $post->id,
                'viewable_type' => Post::class,
                'date' => now()->toDateString(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } catch (\Exception $e) {
            report($e);
        }

        $relatedPosts = Post::with('categories')
            ->published()
            ->whereHas('categories', function ($q) use ($post) {
                $q->whereIn('categories.id', $post->categories->pluck('id'));
            })
            ->where('id', '!=', $post->id)
            ->latest()
            ->take(3)
            ->get();
            
        $randomProducts = Product::inRandomOrder()->take(6)->get();
            
        return view('blog.show', compact('post', 'relatedPosts', 'randomProducts'));
    }

    public function shopShow($id, $slug = null)
    {
        $realId = (int) $id;
        $product = Product::with('categories')->findOrFail($realId);

        $correctSlug = \Illuminate\Support\Str::slug($product->name);
        if ($slug !== $correctSlug) {
            return redirect()->route('shop.show', ['id' => $product->id, 'slug' => $correctSlug], 301);
        }

        try {
            \App\Models\PageView::insertOrIgnore([
                'ip_address' => request()->ip(),
                'viewable_id' => $product->id,
                'viewable_type' => Product::class,
                'date' => now()->toDateString(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } catch (\Exception $e) {
            report($e);
        }

        $relatedProducts = Product::with('categories')
            ->where('id', '!=', $product->id)
            ->whereHas('categories', function ($q) use ($product) {
                $q->whereIn('categories.id', $product->categories->pluck('id'));
            })
            ->latest()
            ->take(3)
            ->get();

        if ($relatedProducts->isEmpty()) {
            $relatedProducts = Product::with('categories')
                ->where('id', '!=', $product->id)
                ->latest()
                ->take(3)
                ->get();
        }
            
        return view('shop_show', compact('product', 'relatedProducts'));
    }

    public function visitProject($id)
    {
        $project = Project::findOrFail($id);
        
        try {
            \App\Models\PageView::insertOrIgnore([
                'ip_address' => request()->ip(),
                'viewable_id' => $project->id,
                'viewable_type' => Project::class,
                'date' => now()->toDateString(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } catch (\Exception $e) {
            report($e);
        }
        
        return redirect()->away($project->project_link ?? route('home'));
    }

    public function shopSuggest(Request $request)
    {
        $search = $request->query('search', '');
        if (strlen($search) < 3) {
            return response()->json([]);
        }
        
        $products = Product::where('name', 'like', '%' . $search . '%')
            ->latest()
            ->take(5)
            ->get(['id', 'name']);
            
        return response()->json($products);
    }

    public function newsSuggest(Request $request)
    {
        $search = $request->query('search', '');
        if (strlen($search) < 3) {
            return response()->json([]);
        }
        
        $posts = Post::where('title', 'like', '%' . $search . '%')
            ->latest()
            ->take(5)
            ->get(['slug', 'title']);
            
        return response()->json($posts);
    }

    public function privacy()
    {
        return view('pages.privacy');
    }

    public function disclaimer()
    {
        return view('pages.disclaimer');
    }

    public function about()
    {
        return view('pages.about');
    }

    public function contact()
    {
        return view('pages.contact');
    }

    public function storeContact(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:150',
            'subject' => 'required|string|max:200',
            'message' => 'required|string|min:10|max:5000',
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'subject.required' => 'Subjek pesan wajib dipilih atau diisi.',
            'message.required' => 'Pesan wajib diisi.',
            'message.min' => 'Pesan minimal terdiri dari 10 karakter.',
        ]);

        try {
            ContactMessage::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'subject' => $validated['subject'],
                'message' => $validated['message'],
                'ip_address' => $request->ip(),
                'status' => 'unread',
            ]);

            return redirect()->route('contact')->with('success', 'Terima kasih! Pesan Anda telah berhasil terkirim. Tim Arhana akan segera menanggapi pertanyaan Anda melalui email.');
        } catch (\Exception $e) {
            report($e);
            return redirect()->route('contact')->with('error', 'Terjadi kendala saat mengirim pesan. Silakan coba lagi atau kirimkan langsung via email ke cikuyu.rc@gmail.com.');
        }
    }
}
