@extends('layouts.admin')

@section('title', 'Laporan Link Rusak Software')
@section('page_title', 'Kelola Laporan Link Rusak')

@section('admin_content')
    <div class="admin-card">
        <div class="admin-card-header" style="flex-wrap: wrap; gap: 1rem;">
            <div>
                <h4 class="admin-card-title">Daftar Laporan Link Rusak / Broken Link</h4>
                <p style="font-size: 0.825rem; color: var(--text-admin-secondary); margin: 0.2rem 0 0 0;">
                    Laporan dari pengunjung website mengenai tautan unduhan yang mati, 404, atau error.
                </p>
            </div>
            <div style="display: flex; align-items: center; gap: 0.75rem; margin-left: auto; flex-wrap: wrap;">
                <a href="{{ route('admin.software.index') }}" class="btn-admin btn-admin-secondary btn-admin-sm" style="display: flex; align-items: center; gap: 0.4rem;">
                    &larr; Kembali ke Katalog Software
                </a>
            </div>
        </div>

        <!-- Filter Tab Bar -->
        <div style="padding: 0.85rem 1.5rem; background: rgba(15, 23, 42, 0.5); border-bottom: 1px solid var(--border-admin); display: flex; gap: 0.5rem; flex-wrap: wrap; align-items: center; justify-content: space-between;">
            <div style="display: flex; gap: 0.5rem;">
                <a href="{{ route('admin.software.reports.index') }}" class="btn-admin btn-admin-sm {{ !request()->has('status') ? 'btn-admin-primary' : 'btn-admin-secondary' }}">
                    Semua ({{ $reports->total() }})
                </a>
                <a href="{{ route('admin.software.reports.index', ['status' => 'pending']) }}" class="btn-admin btn-admin-sm {{ request('status') === 'pending' ? 'btn-admin-primary' : 'btn-admin-secondary' }}" style="{{ $pendingCount > 0 && request('status') !== 'pending' ? 'border-color: rgba(239, 68, 68, 0.4); color: #f87171;' : '' }}">
                    Menunggu Perbaikan ({{ $pendingCount }})
                </a>
                <a href="{{ route('admin.software.reports.index', ['status' => 'resolved']) }}" class="btn-admin btn-admin-sm {{ request('status') === 'resolved' ? 'btn-admin-primary' : 'btn-admin-secondary' }}">
                    Sudah Selesai
                </a>
            </div>
            <div style="font-size: 0.825rem; color: var(--text-admin-secondary);">
                Menampilkan <strong>{{ $reports->count() }}</strong> dari <strong>{{ $reports->total() }}</strong> laporan
            </div>
        </div>
        
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th style="width: 45px; text-align: center;">No</th>
                        <th>Software</th>
                        <th>Alasan &amp; Detail Laporan</th>
                        <th>Tautan Saat Ini</th>
                        <th style="text-align: center; width: 130px;">Status</th>
                        <th style="width: 170px; text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reports as $report)
                        <tr>
                            <td style="text-align: center; font-weight: 700; color: var(--text-admin-secondary); font-size: 0.85rem;">
                                {{ $loop->iteration + ($reports->currentPage() - 1) * $reports->perPage() }}
                            </td>
                            <td>
                                @if($report->software)
                                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                                        <div style="width: 38px; height: 38px; border-radius: 8px; border: 1px solid var(--border-admin); background: rgba(255, 255, 255, 0.08); display: flex; align-items: center; justify-content: center; overflow: hidden; padding: 4px; flex-shrink: 0;">
                                            <img src="{{ $report->software->icon_url }}" alt="{{ $report->software->title }}" style="width: 100%; height: 100%; object-fit: contain;">
                                        </div>
                                        <div>
                                            <div style="font-weight: 700; font-size: 0.95rem;">
                                                <a href="{{ route('software.show', $report->software->slug) }}" target="_blank" style="color: var(--text-admin-primary); text-decoration: none;" title="Buka Halaman Publik">
                                                    {{ $report->software->title }}
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 12px; height: 12px; display: inline-block; vertical-align: middle; color: var(--text-admin-muted);"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
                                                </a>
                                            </div>
                                            <div style="font-size: 0.785rem; color: var(--text-admin-secondary);">
                                                v{{ $report->software->version }} &bull; {{ $report->software->developer ?? 'Komunitas' }}
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <span style="color: var(--text-admin-muted); font-style: italic;">[Software telah dihapus]</span>
                                @endif
                            </td>
                            <td>
                                <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.3rem;">
                                    <span style="background: rgba(239, 68, 68, 0.15); color: #f87171; border: 1px solid rgba(239, 68, 68, 0.3); font-size: 0.75rem; font-weight: 700; padding: 2px 8px; border-radius: 4px;">
                                        ⚠️ {{ $report->reason }}
                                    </span>
                                </div>
                                @if($report->note)
                                    <div style="font-size: 0.85rem; color: var(--text-admin-primary); background: rgba(0, 0, 0, 0.2); padding: 6px 10px; border-radius: 6px; border-left: 3px solid var(--accent-red); margin-bottom: 0.35rem;">
                                        "{{ $report->note }}"
                                    </div>
                                @endif
                                <div style="font-size: 0.75rem; color: var(--text-admin-muted);">
                                    Dilaporkan {{ $report->created_at->diffForHumans() }} &bull; IP: {{ $report->reporter_ip ?? 'Anonymous' }}
                                </div>
                            </td>
                            <td>
                                @if($report->software && $report->software->download_url)
                                    <div style="max-width: 240px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; font-size: 0.775rem; font-family: monospace; color: var(--text-admin-secondary); margin-bottom: 0.3rem;">
                                        {{ $report->software->download_url }}
                                    </div>
                                    <a href="{{ $report->software->download_url }}" target="_blank" rel="noopener noreferrer" class="btn-admin btn-admin-secondary btn-admin-sm" style="font-size: 0.75rem; padding: 2px 8px; display: inline-flex; align-items: center; gap: 3px;">
                                        Tes Tautan Langsung &rarr;
                                    </a>
                                @else
                                    <span style="font-size: 0.8rem; color: var(--text-admin-muted);">-</span>
                                @endif
                            </td>
                            <td style="text-align: center;">
                                @if($report->status === 'pending')
                                    <span style="background: rgba(245, 158, 11, 0.15); color: var(--accent-amber); border: 1px solid rgba(245, 158, 11, 0.3); font-size: 0.75rem; font-weight: 700; padding: 3px 8px; border-radius: 12px; display: inline-block;">
                                        ⏳ Menunggu
                                    </span>
                                @else
                                    <span style="background: rgba(16, 185, 129, 0.15); color: var(--accent-emerald); border: 1px solid rgba(16, 185, 129, 0.3); font-size: 0.75rem; font-weight: 700; padding: 3px 8px; border-radius: 12px; display: inline-block;">
                                        ✓ Selesai
                                    </span>
                                @endif
                            </td>
                            <td style="text-align: right;">
                                <div style="display: flex; gap: 0.4rem; justify-content: flex-end; align-items: center; flex-wrap: wrap;">
                                    @if($report->software)
                                        <a href="{{ route('admin.software.edit', $report->software_id) }}" class="btn-admin btn-admin-secondary btn-admin-sm" style="padding: 4px 8px; font-size: 0.8rem; color: var(--accent-sky);" title="Perbaiki Link / Edit Software">
                                            Perbaiki Link
                                        </a>
                                    @endif

                                    @if($report->status === 'pending')
                                        <form action="{{ route('admin.software.reports.resolve', $report->id) }}" method="POST" style="display: inline-block;">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" class="btn-admin btn-admin-primary btn-admin-sm" style="padding: 4px 8px; font-size: 0.8rem;" title="Tandai sudah diperbaiki">
                                                ✓ Selesai
                                            </button>
                                        </form>
                                    @endif

                                    <form action="{{ route('admin.software.reports.destroy', $report->id) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('Hapus riwayat laporan ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-admin btn-admin-secondary btn-admin-sm" style="padding: 4px 8px; font-size: 0.8rem; color: var(--accent-red);" title="Hapus laporan">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 13px; height: 13px;"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 3rem 1rem; color: var(--text-admin-secondary);">
                                <div style="font-size: 2rem; margin-bottom: 0.5rem;">🎉</div>
                                <div style="font-weight: 700; font-size: 1rem; color: var(--text-admin-primary); margin-bottom: 0.25rem;">Tidak Ada Laporan Tautan Rusak</div>
                                <p style="font-size: 0.85rem; margin: 0;">Semua tautan unduhan software berfungsi normal atau sudah selesai ditindaklanjuti.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($reports->hasPages())
            <div style="padding: 1.25rem 1.5rem; border-top: 1px solid var(--border-admin);">
                {{ $reports->links() }}
            </div>
        @endif
    </div>
@endsection
