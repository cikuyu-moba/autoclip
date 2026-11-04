<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Project;
use Illuminate\Support\Facades\Storage;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->query('per_page', 15);
        if (!in_array($perPage, [15, 30, 50, 100])) {
            $perPage = 15;
        }

        $projects = Project::withCount('views')->latest()->paginate($perPage)->withQueryString();
        return view('admin.projects.index', compact('projects'));
    }

    public function create()
    {
        return view('admin.projects.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:150',
            'description' => 'nullable|string',
            'technologies' => 'required|string|max:255',
            'project_link' => 'nullable|url',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('projects', 'public');
        }

        Project::create([
            'title' => $request->title,
            'description' => $request->description,
            'technologies' => $request->technologies,
            'project_link' => $request->project_link,
            'image' => $imagePath,
        ]);

        return redirect()->route('admin.projects.index')->with('success', 'Portofolio proyek berhasil ditambahkan.');
    }

    public function edit(Project $project)
    {
        return view('admin.projects.edit', compact('project'));
    }

    public function update(Request $request, Project $project)
    {
        $request->validate([
            'title' => 'required|string|max:150',
            'description' => 'nullable|string',
            'technologies' => 'required|string|max:255',
            'project_link' => 'nullable|url',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $imagePath = $project->image;
        if ($request->hasFile('image')) {
            if ($project->image) {
                Storage::disk('public')->delete($project->image);
            }
            $imagePath = $request->file('image')->store('projects', 'public');
        }

        $project->update([
            'title' => $request->title,
            'description' => $request->description,
            'technologies' => $request->technologies,
            'project_link' => $request->project_link,
            'image' => $imagePath,
        ]);

        return redirect()->route('admin.projects.index')->with('success', 'Portofolio proyek berhasil diperbarui.');
    }

    public function destroy(Project $project)
    {
        if ($project->image) {
            Storage::disk('public')->delete($project->image);
        }

        $project->delete();
        return redirect()->route('admin.projects.index')->with('success', 'Portofolio proyek berhasil dihapus.');
    }
}
