@extends('layouts.admin')

@section('title', 'Daftar Portofolio')
@section('page_title', 'Kelola Portofolio Proyek')

@section('admin_content')
    <div class="admin-card">
        <div class="admin-card-header" style="flex-wrap: wrap; gap: 1rem;">
            <h4 class="admin-card-title">Portofolio &amp; Aplikasi yang Pernah Dikerjakan</h4>
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
                <a href="{{ route('admin.projects.create') }}" class="btn-admin btn-admin-primary btn-admin-sm" style="margin: 0;">+ Tambah Portofolio</a>
            </div>
        </div>
        
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th style="width: 50px; text-align: center;">No</th>
                        <th style="width: 80px;">Cover</th>
                        <th>Judul Proyek</th>
                        <th>Teknologi (Tags)</th>
                        <th>Link Demo / Github</th>
                        <th style="width: 80px; text-align: center;">Dilihat</th>
                        <th style="width: 150px; text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($projects as $project)
                        <tr>
                            <td style="text-align: center; font-weight: 700; color: var(--text-admin-secondary); font-size: 0.85rem;">
                                {{ $loop->iteration + ($projects->currentPage() - 1) * $projects->perPage() }}
                            </td>
                            <td>
                                <div class="table-image">
                                    @if($project->image)
                                        <img src="{{ asset('storage/' . $project->image) }}" alt="{{ $project->title }}">
                                    @else
                                        <span>💻</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div style="font-weight: 700; font-size: 0.95rem; margin-bottom: 0.25rem;">
                                    {{ $project->title }}
                                </div>
                                <p style="font-size: 0.75rem; color: var(--text-admin-secondary); max-width: 300px;">
                                    {{ Str::limit($project->description, 60) }}
                                </p>
                            </td>
                            <td>
                                <div style="display: flex; flex-wrap: wrap; gap: 0.35rem;">
                                    @foreach(explode(',', $project->technologies) as $tech)
                                        <span class="badge-admin" style="font-size: 0.7rem; padding: 0.1rem 0.4rem;">{{ trim($tech) }}</span>
                                    @endforeach
                                </div>
                            </td>
                            <td style="font-family: monospace; font-size: 0.8rem; color: var(--accent-blue);">
                                @if($project->project_link)
                                    <a href="{{ $project->project_link }}" target="_blank" rel="noopener noreferrer" style="text-decoration: underline;">
                                        {{ Str::limit($project->project_link, 30) }} ↗
                                    </a>
                                @else
                                    <span style="color: var(--text-admin-muted); font-style: italic;">Tidak ada link</span>
                                @endif
                            </td>
                            <td style="text-align: center;">
                                <span class="badge-admin badge-gray" style="font-size: 0.75rem;">
                                    <svg viewBox="0 0 24 24" width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 2px; vertical-align: middle;"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                    {{ $project->views_count }}
                                </span>
                            </td>
                            <td class="actions-cell" style="justify-content: flex-end;">
                                <a href="{{ route('admin.projects.edit', $project->id) }}" class="btn-admin btn-admin-secondary btn-admin-sm">Edit</a>
                                <form action="{{ route('admin.projects.destroy', $project->id) }}" method="POST" 
                                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus proyek portofolio ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-admin btn-admin-danger btn-admin-sm">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; color: var(--text-admin-secondary); padding: 3rem;">
                                Belum ada portofolio proyek yang ditambahkan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($projects->hasPages() || $projects->total() > 0)
            <div class="admin-pagination-wrapper" style="margin-top: 1.5rem; display: flex; flex-direction: column; align-items: center; gap: 0.75rem;">
                {{ $projects->links('pagination::bootstrap-4') }}
                <div style="font-size: 0.8rem; color: var(--text-admin-secondary); font-weight: 500;">
                    Menampilkan {{ $projects->firstItem() ?? 0 }} sampai {{ $projects->lastItem() ?? 0 }} dari {{ $projects->total() }} data
                </div>
            </div>
        @endif
    </div>
@endsection
