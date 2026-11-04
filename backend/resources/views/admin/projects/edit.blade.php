@extends('layouts.admin')

@section('title', 'Edit Portofolio')
@section('page_title', 'Perbarui Portofolio Proyek')

@section('admin_content')
    <div class="admin-card" style="max-width: 800px; margin: 0 auto;">
        <div class="admin-card-header">
            <h4 class="admin-card-title">Edit Portofolio: {{ Str::limit($project->title, 35) }}</h4>
            <a href="{{ route('admin.projects.index') }}" class="btn-admin btn-admin-secondary btn-admin-sm">Kembali</a>
        </div>
        
        <form action="{{ route('admin.projects.update', $project->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="form-group">
                <label for="title" class="form-label">Judul Proyek / Aplikasi</label>
                <input type="text" name="title" id="title" class="form-control" placeholder="Tulis judul proyek..." value="{{ old('title', $project->title) }}" required>
                @error('title')
                    <span class="error-feedback">{{ $message }}</span>
                @enderror
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="technologies" class="form-label">Teknologi yang Digunakan (Pisahkan dengan koma)</label>
                    <input type="text" name="technologies" id="technologies" class="form-control" placeholder="Contoh: Laravel, React, Tailwind CSS" value="{{ old('technologies', $project->technologies) }}" required>
                    @error('technologies')
                        <span class="error-feedback">{{ $message }}</span>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label for="project_link" class="form-label">Tautan Demo / Aplikasi (Opsional)</label>
                    <input type="url" name="project_link" id="project_link" class="form-control" placeholder="https://github.com/akun/nama-repo" value="{{ old('project_link', $project->project_link) }}">
                    @error('project_link')
                        <span class="error-feedback">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            
            <div class="form-group">
                <label for="imageUpload" class="form-label">Gambar Sampul / Screenshot Proyek</label>
                <div class="image-upload-wrapper">
                    <div class="image-preview-box">
                        @if($project->image)
                            <img id="imagePreview" src="{{ asset('storage/' . $project->image) }}" alt="Preview Gambar">
                            <svg id="uploadPlaceholder" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display: none;"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
                        @else
                            <img id="imagePreview" src="" alt="Preview Gambar" style="display: none;">
                            <svg id="uploadPlaceholder" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
                        @endif
                    </div>
                    <div>
                        <input type="file" name="image" id="imageUpload" accept="image/*" 
                               onchange="previewImageUpload(this, 'imagePreview', 'uploadPlaceholder')" 
                               style="font-size: 0.85rem; color: var(--text-admin-secondary);">
                        <p style="font-size: 0.75rem; color: var(--text-admin-muted); margin-top: 0.5rem;">Biarkan kosong jika tidak ingin mengubah gambar sampul. Maksimal 2MB.</p>
                    </div>
                </div>
                @error('image')
                    <span class="error-feedback">{{ $message }}</span>
                @enderror
            </div>
            
            <div class="form-group" style="margin-bottom: 2.5rem;">
                <label for="description" class="form-label">Deskripsi / Detail Proyek</label>
                <textarea name="description" id="description" class="form-control" style="min-height: 150px;" required>{{ old('description', $project->description) }}</textarea>
                @error('description')
                    <span class="error-feedback">{{ $message }}</span>
                @enderror
            </div>
            
            <button type="submit" class="btn-admin btn-admin-primary" style="width: 100%; padding: 0.85rem;">Perbarui Portofolio</button>
        </form>
    </div>
@endsection
