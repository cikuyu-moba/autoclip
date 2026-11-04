<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Menu;
use App\Models\Category;
use App\Models\Post;
use App\Models\Product;
use App\Models\Project;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $menusCount = Menu::count();
        $categoriesCount = Category::count();
        $postsCount = Post::count();
        $productsCount = Product::count();
        $projectsCount = Project::count();
        $usersCount = User::count();
        $softwaresCount = \App\Models\Software::count();
        $totalSoftwareDownloads = (int) \App\Models\Software::sum('downloads_count');

        $todayVisitors = \App\Models\Visitor::whereDate('date', now()->toDateString())->count();
        $weeklyVisitors = \App\Models\Visitor::whereDate('date', '>=', now()->startOfWeek()->toDateString())->count();
        $monthlyVisitors = \App\Models\Visitor::whereDate('date', '>=', now()->startOfMonth()->toDateString())->count();
        $yearlyVisitors = \App\Models\Visitor::whereDate('date', '>=', now()->startOfYear()->toDateString())->count();

        $latestPosts = Post::with('categories')->latest()->take(5)->get();
        $latestProjects = Project::latest()->take(5)->get();

        return view('admin.dashboard', compact(
            'menusCount',
            'categoriesCount',
            'postsCount',
            'productsCount',
            'projectsCount',
            'usersCount',
            'softwaresCount',
            'totalSoftwareDownloads',
            'todayVisitors',
            'weeklyVisitors',
            'monthlyVisitors',
            'yearlyVisitors',
            'latestPosts',
            'latestProjects'
        ));
    }
}

