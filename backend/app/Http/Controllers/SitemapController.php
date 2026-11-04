<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Product;
use App\Models\Course;
use App\Models\Software;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index()
    {
        $posts = Post::published()->latest()->get();
        $products = Product::latest()->get();
        $courses = Course::latest()->get();
        $softwares = Software::active()->latest()->get();

        return response()->view('sitemap', [
            'posts' => $posts,
            'products' => $products,
            'courses' => $courses,
            'softwares' => $softwares,
        ])->header('Content-Type', 'text/xml');
    }
}
