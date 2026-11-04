@extends('layouts.admin')

@section('title', 'Daftar Berita')
@section('page_title', 'Kelola Berita & Artikel')

@section('admin_content')
    <div class="admin-card">
        <div class="admin-card-header" style="flex-wrap: wrap; gap: 1rem;">
            <h4 class="admin-card-title">Semua Publikasi Artikel</h4>
            <div style="display: flex; align-items: center; gap: 1rem; margin-left: auto;">
                <form action="" method="GET" style="display: inline-flex; align-items: center; gap: 0.5rem; margin: 0;">
                    <span style="font-size: 0.85rem; color: var(--text-admin-secondary);">Tampilkan:</span>
                    <select name="per_page" onchange="this.form.submit()" class="form-control" style="width: auto; padding: 0.25rem 0.5rem; font-size: 0.85rem; background-color: var(--bg-admin-card); border-color: var(--border-color); color: var(--text-primary); border-radius: 4px; height: auto; cursor: pointer;">
                        <option value="15" {{ request('per_page') == 15 ? 'selected' : '' }}>15</option>
                        <option value="30" {{ request('per_page') == 30 ? 'selected' : '' }}>30</option>
                        <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                    </select>
                </form>
                <a href="{{ route('admin.posts.create') }}" class="btn-admin btn-admin-primary btn-admin-sm" style="margin: 0;">Tulis Berita Baru</a>
            </div>
        </div>
        
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th style="width: 50px; text-align: center;">No</th>
                        <th style="width: 80px;">Cover</th>
                        <th>Judul Berita</th>
                        <th>Kategori</th>
                        <th>Tanggal Terbit</th>
                        <th style="width: 80px; text-align: center;">Dilihat</th>
                        <th style="width: 150px; text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($posts as $post)
                        <tr>
                            <td style="text-align: center; font-weight: 700; color: var(--text-admin-secondary); font-size: 0.85rem;">
                                {{ $loop->iteration + ($posts->currentPage() - 1) * $posts->perPage() }}
                            </td>
                            <td>
                                <div class="table-image">
                                    @if($post->image)
                                        <img src="{{ asset('storage/' . $post->image) }}" alt="{{ $post->title }}">
                                    @else
                                        <span>✍️</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div style="font-weight: 700; font-size: 0.95rem; margin-bottom: 0.25rem;">
                                    {{ $post->title }}
                                </div>
                                <span style="font-size: 0.75rem; color: var(--text-admin-muted);">
                                    /news/{{ $post->slug }}
                                </span>
                            </td>
                            <td>
                                <div style="display: flex; flex-direction: column; gap: 4px;">
                                    @foreach($post->categories as $cat)
                                        <span class="badge-admin badge-blue" style="font-size: 0.7rem; padding: 2px 6px; font-weight: 600; border-radius: 4px; display: inline-block; width: max-content;">{{ $cat->name }}</span>
                                    @endforeach
                                </div>
                            </td>
                            <td>
                                <div style="margin-bottom: 2px;">
                                    @if($post->published_at && $post->published_at->isFuture())
                                        <span class="badge-admin" style="background: rgba(245, 158, 11, 0.1); color: #F59E0B; border: 1px solid rgba(245, 158, 11, 0.2); font-size: 0.65rem; padding: 2px 6px; border-radius: 4px; font-weight: 600;">Dijadwalkan</span>
                                    @else
                                        <span class="badge-admin" style="background: rgba(16, 185, 129, 0.1); color: #10B981; border: 1px solid rgba(16, 185, 129, 0.2); font-size: 0.65rem; padding: 2px 6px; border-radius: 4px; font-weight: 600;">Diterbitkan</span>
                                    @endif
                                </div>
                                <div style="font-size: 0.75rem; color: var(--text-admin-secondary);">
                                    {{ ($post->published_at ?? $post->created_at)->format('d M Y, H:i') }}
                                </div>
                            </td>
                            <td style="text-align: center;">
                                <span class="badge-admin badge-gray" style="font-size: 0.75rem;">
                                    <svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 2px; vertical-align: middle;"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                    {{ $post->views_count }}
                                </span>
                            </td>
                            <td class="actions-cell" style="justify-content: flex-end;">
                                <a href="{{ route('admin.posts.edit', $post->id) }}" class="btn-admin btn-admin-secondary btn-admin-sm">Edit</a>
                                <form action="{{ route('admin.posts.destroy', $post->id) }}" method="POST" 
                                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus artikel berita ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-admin btn-admin-danger btn-admin-sm">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; color: var(--text-admin-secondary); padding: 3rem;">
                                Belum ada berita atau artikel yang diterbitkan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($posts->hasPages() || $posts->total() > 0)
            <div class="admin-pagination-wrapper" style="margin-top: 1.5rem; display: flex; flex-direction: column; align-items: center; gap: 0.75rem;">
                {{ $posts->links('pagination::bootstrap-4') }}
                <div style="font-size: 0.8rem; color: var(--text-admin-secondary); font-weight: 500;">
                    Menampilkan {{ $posts->firstItem() ?? 0 }} sampai {{ $posts->lastItem() ?? 0 }} dari {{ $posts->total() }} data
                </div>
            </div>
        @endif
    </div>
@endsection
