<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Menu;

class MenuController extends Controller
{
    public function index()
    {
        $menus = Menu::root()->with('submenus')->get();
        $rootMenus = Menu::whereNull('parent_id')->orderBy('order', 'asc')->get();
        return view('admin.menus.index', compact('menus', 'rootMenus'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:100',
            'url' => 'required|string|max:255',
            'order' => 'required|integer',
            'parent_id' => 'nullable|exists:menus,id',
        ]);

        Menu::create([
            'title' => $request->title,
            'url' => $request->url,
            'order' => $request->order,
            'parent_id' => $request->parent_id,
            'is_external' => $request->has('is_external') ? 1 : 0,
        ]);

        return redirect()->route('admin.menus.index')->with('success', 'Menu berhasil ditambahkan.');
    }

    public function update(Request $request, Menu $menu)
    {
        $request->validate([
            'title' => 'required|string|max:100',
            'url' => 'required|string|max:255',
            'order' => 'required|integer',
            'parent_id' => 'nullable|exists:menus,id',
        ]);

        $menu->update([
            'title' => $request->title,
            'url' => $request->url,
            'order' => $request->order,
            'parent_id' => $request->parent_id,
            'is_external' => $request->has('is_external') ? 1 : 0,
        ]);

        return redirect()->route('admin.menus.index')->with('success', 'Menu berhasil diperbarui.');
    }

    public function destroy(Menu $menu)
    {
        $menu->delete();
        return redirect()->route('admin.menus.index')->with('success', 'Menu berhasil dihapus.');
    }
}
