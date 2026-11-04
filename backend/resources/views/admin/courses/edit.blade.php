@extends('layouts.admin')

@section('title', 'Edit Kursus')
@section('page_title', 'Edit Materi Kursus')

@section('admin_content')
    <div class="admin-card" style="max-width: 800px; margin: 0 auto;">
        <div class="admin-card-header" style="margin-bottom: 2rem;">
            <h4 class="admin-card-title">Form Edit Kursus: {{ $course->title }}</h4>
        </div>

        <form action="{{ route('admin.courses.update', $course->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group" style="margin-bottom: 1.5rem;">
                <label for="title" class="form-label">Judul Kursus</label>
                <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror" 
                       value="{{ old('title', $course->title) }}" required>
                @error('title')
                    <span class="error-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group" style="margin-bottom: 1.5rem;">
                <label for="description" class="form-label">Deskripsi Singkat</label>
                <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror" 
                          rows="3">{{ old('description', $course->description) }}</textarea>
                @error('description')
                    <span class="error-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group" style="margin-bottom: 2rem;">
                <label for="materials" class="form-label">Isi Materi Pembelajaran (Mendukung HTML)</label>
                <textarea name="materials" id="materials" class="form-control @error('materials') is-invalid @enderror" 
                          rows="15" required>{{ old('materials', $course->materials) }}</textarea>
                @error('materials')
                    <span class="error-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div style="display: flex; gap: 0.75rem;">
                <button type="submit" class="btn-admin btn-admin-primary">Perbarui Materi</button>
                <a href="{{ route('admin.courses.index') }}" class="btn-admin btn-admin-secondary">Batal</a>
            </div>
        </form>
    </div>
@endsection
