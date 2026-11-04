<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Category;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount(['posts', 'products', 'softwares'])->get();
        return view('admin.categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'type' => 'required|string|in:news,shop,software',
        ]);

        $exists = Category::where('name', $request->name)->where('type', $request->type)->exists();
        if ($exists) {
            return back()->withErrors(['name' => 'Kategori dengan nama dan tipe tersebut sudah terdaftar.'])->withInput();
        }

        Category::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'type' => $request->type,
        ]);

        return redirect()->route('admin.categories.index')->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'type' => 'required|string|in:news,shop,software',
        ]);

        $exists = Category::where('name', $request->name)
            ->where('type', $request->type)
            ->where('id', '!=', $category->id)
            ->exists();
        if ($exists) {
            return back()->withErrors(['name' => 'Kategori dengan nama dan tipe tersebut sudah terdaftar.'])->withInput();
        }

        $category->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'type' => $request->type,
        ]);

        return redirect()->route('admin.categories.index')->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy(Category $category)
    {
        $category->delete();
        return redirect()->route('admin.categories.index')->with('success', 'Kategori berhasil dihapus.');
    }
}
