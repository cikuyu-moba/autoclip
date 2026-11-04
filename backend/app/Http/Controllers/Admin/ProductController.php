<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->query('per_page', 15);
        if (!in_array($perPage, [15, 30, 50, 100])) {
            $perPage = 15;
        }

        $products = Product::with('categories')->withCount('views')->latest()->paginate($perPage)->withQueryString();
        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::where('type', 'shop')->get();
        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:150',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'category_ids' => 'required|array',
            'category_ids.*' => 'exists:categories,id',
            'tokopedia_link' => 'nullable|url',
            'shopee_link' => 'nullable|url',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'image2' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'image3' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'image4' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'image5' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $images = [];
        for ($i = 1; $i <= 5; $i++) {
            $inputName = $i === 1 ? 'image' : "image{$i}";
            $images[$inputName] = null;
            if ($request->hasFile($inputName)) {
                $images[$inputName] = $request->file($inputName)->store('products', 'public');
            }
        }

        $product = Product::create([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'tokopedia_link' => $request->tokopedia_link,
            'shopee_link' => $request->shopee_link,
            'image' => $images['image'],
            'image2' => $images['image2'],
            'image3' => $images['image3'],
            'image4' => $images['image4'],
            'image5' => $images['image5'],
        ]);

        $product->categories()->sync($request->category_ids);

        return redirect()->route('admin.products.index')->with('success', 'Produk berhasil ditambahkan.');
    }

    public function edit(Product $product)
    {
        $categories = Category::where('type', 'shop')->get();
        $product->load('categories');
        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string|max:150',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'category_ids' => 'required|array',
            'category_ids.*' => 'exists:categories,id',
            'tokopedia_link' => 'nullable|url',
            'shopee_link' => 'nullable|url',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'image2' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'image3' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'image4' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'image5' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $images = [];
        for ($i = 1; $i <= 5; $i++) {
            $inputName = $i === 1 ? 'image' : "image{$i}";
            $images[$inputName] = $product->$inputName;
            if ($request->hasFile($inputName)) {
                if ($product->$inputName) {
                    Storage::disk('public')->delete($product->$inputName);
                }
                $images[$inputName] = $request->file($inputName)->store('products', 'public');
            }
        }

        $product->update([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'tokopedia_link' => $request->tokopedia_link,
            'shopee_link' => $request->shopee_link,
            'image' => $images['image'],
            'image2' => $images['image2'],
            'image3' => $images['image3'],
            'image4' => $images['image4'],
            'image5' => $images['image5'],
        ]);

        $product->categories()->sync($request->category_ids);

        return redirect()->route('admin.products.index')->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy(Product $product)
    {
        for ($i = 1; $i <= 5; $i++) {
            $imgName = $i === 1 ? 'image' : "image{$i}";
            if ($product->$imgName) {
                Storage::disk('public')->delete($product->$imgName);
            }
        }

        $product->delete();
        return redirect()->route('admin.products.index')->with('success', 'Produk berhasil dihapus.');
    }
}
