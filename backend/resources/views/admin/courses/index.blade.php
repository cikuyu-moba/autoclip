@extends('layouts.admin')

@section('title', 'Materi Kursus')
@section('page_title', 'Kelola Materi Kursus')

@section('admin_content')
    <div class="admin-card">
        <div class="admin-card-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <h4 class="admin-card-title">Daftar Kursus Pelajaran</h4>
            <a href="{{ route('admin.courses.create') }}" class="btn-admin btn-admin-primary">
                + Tambah Materi Kursus
            </a>
        </div>
        
        @if(session('success'))
            <div style="background: rgba(5, 150, 105, 0.08); border: 1px solid rgba(5, 150, 105, 0.2); color: var(--accent-emerald); padding: 0.75rem 1rem; border-radius: var(--radius-sm); font-size: 0.85rem; margin-bottom: 1.5rem;">
                {{ session('success') }}
            </div>
        @endif

        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Judul Kursus</th>
                        <th>Slug</th>
                        <th>Deskripsi Singkat</th>
                        <th>Tanggal Dibuat</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($courses as $course)
                        <tr>
                            <td style="font-weight: 700;">{{ $course->title }}</td>
                            <td style="font-family: monospace; font-size: 0.8rem;">{{ $course->slug }}</td>
                            <td>{{ Str::limit($course->description, 60) }}</td>
                            <td>{{ $course->created_at->format('d M Y H:i') }} WIB</td>
                            <td class="actions-cell">
                                <a href="{{ route('admin.courses.edit', $course->id) }}" class="btn-admin btn-admin-secondary btn-admin-sm">
                                    Edit
                                </a>
                                <form action="{{ route('admin.courses.destroy', $course->id) }}" method="POST" 
                                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus materi kursus ini? Semua soal ujian dan nilai siswa yang terkait dengan kursus ini akan ikut terhapus.')"
                                      style="display: inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-admin btn-admin-danger btn-admin-sm">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align: center; color: var(--text-admin-secondary); padding: 2rem 0;">Belum ada materi kursus yang didaftarkan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
