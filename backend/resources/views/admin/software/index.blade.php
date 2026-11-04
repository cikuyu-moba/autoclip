@extends('layouts.admin')

@section('title', 'Daftar Software Gratis')
@section('page_title', 'Kelola Software & Freeware')

@section('admin_content')
    <div class="admin-card">
        <div class="admin-card-header" style="flex-wrap: wrap; gap: 1rem;">
            <div>
                <h4 class="admin-card-title">Katalog Download Software Gratis</h4>
                <p style="font-size: 0.825rem; color: var(--text-admin-secondary); margin: 0.2rem 0 0 0;">
                    Kelola software, versi rilis, link download installer resmi, dan sinkronisasi otomatis.
                </p>
            </div>
            <div style="display: flex; align-items: center; gap: 0.75rem; margin-left: auto; flex-wrap: wrap;">
                <a href="{{ route('admin.software.reports.index') }}" class="btn-admin btn-admin-secondary btn-admin-sm" style="display: flex; align-items: center; gap: 0.4rem; {{ ($pendingReportsCount ?? 0) > 0 ? 'background: rgba(239, 68, 68, 0.15); color: #f87171; border-color: rgba(239, 68, 68, 0.4);' : '' }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 14px; height: 14px;"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
                    Laporan Rusak
                    @if(($pendingReportsCount ?? 0) > 0)
                        <span style="background: var(--accent-red); color: #fff; font-size: 0.65rem; font-weight: 800; padding: 1px 5px; border-radius: 8px;">{{ $pendingReportsCount }}</span>
                    @endif
                </a>

                <form action="{{ route('admin.software.discover') }}" method="POST" onsubmit="return confirm('Pindai repositori rilis dan tambahkan 3 software gratis baru secara otomatis?')">
                    @csrf
                    <input type="hidden" name="limit" value="3">
                    <button type="submit" class="btn-admin btn-admin-secondary btn-admin-sm" style="display: flex; align-items: center; gap: 0.4rem; background: rgba(148, 163, 184, 0.1); color: #cbd5e1; border-color: rgba(148, 163, 184, 0.25);" title="Temukan dan tambahkan software open source baru secara otomatis dari 50+ katalog kandidat">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 14px; height: 14px;"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line><line x1="11" y1="8" x2="11" y2="14"></line><line x1="8" y1="11" x2="14" y2="11"></line></svg>
                        Temukan Software Baru
                    </button>
                </form>

                <button type="button" class="btn-admin btn-admin-secondary btn-admin-sm" onclick="openImportModal()" style="display: flex; align-items: center; gap: 0.4rem; background: rgba(99, 102, 241, 0.1); color: #a5b4fc; border-color: rgba(99, 102, 241, 0.3);" title="Impor otomatis langsung dari URL repositori GitHub">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 14px; height: 14px;"><path d="M9 19c-5 1.5-5-2.5-7-3m14 6v-3.87a3.37 3.37 0 0 0-.94-2.61c3.14-.35 6.44-1.54 6.44-7A5.44 5.44 0 0 0 20 4.77 5.07 5.07 0 0 0 19.91 1S18.73.65 16 2.48a13.38 13.38 0 0 0-7 0C6.27.65 5.09 1 5.09 1A5.07 5.07 0 0 0 5 4.77a5.44 5.44 0 0 0-1.5 3.78c0 5.42 3.3 6.61 6.44 7A3.37 3.37 0 0 0 9 18.13V22"></path></svg>
                    + Import Repo GitHub
                </button>

                <form action="{{ route('admin.software.sync-all') }}" method="POST" onsubmit="return confirm('Jalankan pembaruan versi otomatis untuk seluruh software GitHub?')">
                    @csrf
                    <button type="submit" class="btn-admin btn-admin-secondary btn-admin-sm" style="display: flex; align-items: center; gap: 0.4rem; background: rgba(56, 189, 248, 0.1); color: var(--accent-sky); border-color: rgba(56, 189, 248, 0.3);">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 15px; height: 15px;"><path d="M21.5 2v6h-6M21.34 15.57a10 10 0 1 1-.57-8.38l5.67-5.67"/></svg>
                        Sync Versi Otomatis
                    </button>
                </form>
                <a href="{{ route('admin.software.create') }}" class="btn-admin btn-admin-primary btn-admin-sm">+ Tambah Manual</a>
            </div>
        </div>

        <!-- Filter & Search Bar (Sleek Dark Theme) -->
        <div style="padding: 1rem 1.5rem; background: rgba(15, 23, 42, 0.5); border-bottom: 1px solid var(--border-admin); display: flex; gap: 1rem; flex-wrap: wrap; align-items: center;">
            <form action="{{ route('admin.software.index') }}" method="GET" style="display: flex; gap: 0.75rem; flex: 1; flex-wrap: wrap;">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama software atau developer..." class="form-control" style="max-width: 300px; font-size: 0.85rem; background-color: var(--bg-admin-card); border-color: var(--border-admin); color: var(--text-admin-primary);">
                
                <select name="category_id" class="form-control" style="max-width: 200px; font-size: 0.85rem; background-color: var(--bg-admin-card); border-color: var(--border-admin); color: var(--text-admin-primary);" onchange="this.form.submit()">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>

                <button type="submit" class="btn-admin btn-admin-secondary btn-admin-sm">Filter</button>
                @if(request()->has('search') || request()->has('category_id'))
                    <a href="{{ route('admin.software.index') }}" class="btn-admin btn-admin-secondary btn-admin-sm" style="color: var(--accent-red);">Reset</a>
                @endif
            </form>
            <div style="font-size: 0.825rem; color: var(--text-admin-secondary);">
                Total: <strong>{{ $softwares->total() }}</strong> software
            </div>
        </div>
        
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th style="width: 50px; text-align: center;">No</th>
                        <th style="width: 60px;">Icon</th>
                        <th>Software &amp; Pengembang</th>
                        <th>Kategori</th>
                        <th>Versi &amp; Ukuran</th>
                        <th>Lisensi</th>
                        <th>Sync GitHub</th>
                        <th style="text-align: center;">Statistik</th>
                        <th style="width: 140px; text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($softwares as $soft)
                        <tr>
                            <td style="text-align: center; font-weight: 700; color: var(--text-admin-secondary); font-size: 0.85rem;">
                                {{ $loop->iteration + ($softwares->currentPage() - 1) * $softwares->perPage() }}
                            </td>
                            <td>
                                <div style="width: 42px; height: 42px; border-radius: 8px; border: 1px solid var(--border-admin); background: rgba(255, 255, 255, 0.08); display: flex; align-items: center; justify-content: center; overflow: hidden; padding: 5px;">
                                    <img src="{{ $soft->icon_url }}" alt="{{ $soft->title }}" style="width: 100%; height: 100%; object-fit: contain;">
                                </div>
                            </td>
                            <td>
                                <div style="font-weight: 700; font-size: 0.95rem; margin-bottom: 0.2rem;">
                                    <a href="{{ route('software.show', $soft->slug) }}" target="_blank" style="color: var(--text-admin-primary); text-decoration: none;" title="Lihat Halaman Publik">
                                        {{ $soft->title }}
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 13px; height: 13px; display: inline-block; vertical-align: middle; color: var(--text-admin-muted);"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
                                    </a>
                                    @if($soft->is_featured)
                                        <span style="background: rgba(245, 158, 11, 0.15); color: var(--accent-amber); font-size: 0.65rem; font-weight: 800; padding: 2px 6px; border-radius: 4px; margin-left: 4px;">FEATURED</span>
                                    @endif
                                </div>
                                <div style="font-size: 0.8rem; color: var(--text-admin-secondary);">
                                    {{ $soft->developer ?? 'Komunitas Open Source' }}
                                </div>
                                <div style="display: flex; gap: 4px; align-items: center; margin-top: 3px;">
                                    @foreach($soft->supported_os_list as $osSlug)
                                        @php $meta = \App\Models\Software::getOsMeta($osSlug); @endphp
                                        <span style="font-size: 0.7rem; background: rgba(255, 255, 255, 0.06); border: 1px solid var(--border-admin); padding: 1px 5px; border-radius: 4px;" title="Didukung: {{ $meta['label'] }}">
                                            {{ $meta['icon'] }} {{ $meta['label'] }}
                                        </span>
                                    @endforeach
                                </div>
                            </td>
                            <td>
                                <span class="badge-admin" style="background: rgba(148, 163, 184, 0.1); color: #cbd5e1; font-weight: 500; font-size: 0.75rem; border: 1px solid rgba(148, 163, 184, 0.18); border-radius: 4px; padding: 2px 8px;">
                                    {{ $soft->category?->name ?? 'Umum' }}
                                </span>
                            </td>
                            <td>
                                <div style="font-weight: 700; font-size: 0.85rem; color: var(--accent-sky);">
                                    v{{ $soft->version }}
                                </div>
                                <div style="font-size: 0.75rem; color: var(--text-admin-secondary);">
                                    {{ $soft->file_size ?? '-' }}
                                </div>
                            </td>
                            <td>
                                <span class="badge-admin" style="background: rgba(100, 116, 139, 0.1); color: var(--text-secondary); font-size: 0.725rem;">
                                    {{ $soft->license }}
                                </span>
                            </td>
                            <td>
                                @if($soft->sync_source === 'github' && $soft->github_repo)
                                    <div style="display: flex; align-items: center; gap: 0.4rem;">
                                        <span style="font-size: 0.75rem; font-family: monospace; color: var(--text-primary);">
                                            {{ Str::limit($soft->github_repo, 18) }}
                                        </span>
                                        <form action="{{ route('admin.software.sync-single', $soft->id) }}" method="POST" style="margin: 0;">
                                            @csrf
                                            <button type="submit" title="Sync rilis terbaru sekarang" style="background: none; border: none; cursor: pointer; color: var(--accent-sky); padding: 2px;">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 14px; height: 14px;"><path d="M21.5 2v6h-6M21.34 15.57a10 10 0 1 1-.57-8.38l5.67-5.67"/></svg>
                                            </button>
                                        </form>
                                    </div>
                                    <div style="font-size: 0.7rem; color: var(--text-admin-secondary);">
                                        {{ $soft->last_synced_at ? $soft->last_synced_at->diffForHumans() : 'Belum sync' }}
                                    </div>
                                @else
                                    <span style="font-size: 0.75rem; color: var(--text-admin-secondary); font-style: italic;">Manual</span>
                                @endif
                            </td>
                            <td style="text-align: center; font-size: 0.8rem;">
                                <div title="Total Download">📥 <strong>{{ number_format($soft->downloads_count) }}</strong></div>
                                <div style="font-size: 0.72rem; color: var(--text-admin-secondary);" title="Total Views">👁️ {{ number_format($soft->views_count) }}</div>
                            </td>
                            <td class="actions-cell">
                                <a href="{{ route('admin.software.edit', $soft->id) }}" class="btn-admin btn-admin-secondary btn-admin-sm">Edit</a>
                                <form action="{{ route('admin.software.destroy', $soft->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus software {{ $soft->title }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-admin btn-admin-danger btn-admin-sm">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" style="text-align: center; color: var(--text-admin-secondary); padding: 2rem;">
                                Belum ada software yang terdaftar. Silakan klik tombol <strong>+ Tambah Software</strong> di atas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($softwares->hasPages())
            <div style="padding: 1rem 1.5rem; border-top: 1px solid var(--border-color);">
                {{ $softwares->links() }}
            </div>
        @endif
    </div>

    <!-- Modal Import Repo GitHub -->
    <div id="importRepoModal" style="display: none; position: fixed; inset: 0; background: rgba(0, 0, 0, 0.75); z-index: 9999; align-items: center; justify-content: center; opacity: 0; transition: opacity 0.2s ease;">
        <div style="background: var(--bg-admin-card); border: 1px solid var(--border-admin); border-radius: var(--radius-md); width: 100%; max-width: 520px; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.5); overflow: hidden; transform: scale(0.95); transition: transform 0.2s ease;">
            <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--border-admin); display: flex; justify-content: space-between; align-items: center; background: rgba(255, 255, 255, 0.02);">
                <div style="display: flex; align-items: center; gap: 0.6rem;">
                    <div style="width: 32px; height: 32px; border-radius: 8px; background: rgba(99, 102, 241, 0.15); display: flex; align-items: center; justify-content: center; color: #a5b4fc;">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 16px; height: 16px;"><path d="M9 19c-5 1.5-5-2.5-7-3m14 6v-3.87a3.37 3.37 0 0 0-.94-2.61c3.14-.35 6.44-1.54 6.44-7A5.44 5.44 0 0 0 20 4.77 5.07 5.07 0 0 0 19.91 1S18.73.65 16 2.48a13.38 13.38 0 0 0-7 0C6.27.65 5.09 1 5.09 1A5.07 5.07 0 0 0 5 4.77a5.44 5.44 0 0 0-1.5 3.78c0 5.42 3.3 6.61 6.44 7A3.37 3.37 0 0 0 9 18.13V22"></path></svg>
                    </div>
                    <div>
                        <h4 style="margin: 0; font-size: 1rem; font-weight: 700; color: var(--text-admin-primary);">Import dari GitHub</h4>
                        <p style="margin: 0.15rem 0 0 0; font-size: 0.75rem; color: var(--text-admin-secondary);">Tambah software baru otomatis via URL atau repositori GitHub</p>
                    </div>
                </div>
                <button type="button" onclick="closeImportModal()" style="background: transparent; border: none; color: var(--text-admin-muted); cursor: pointer; padding: 4px;" aria-label="Tutup">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 18px; height: 18px;"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                </button>
            </div>

            <form action="{{ route('admin.software.discover') }}" method="POST" id="importRepoForm" onsubmit="handleImportSubmit(event)">
                @csrf
                <div style="padding: 1.5rem; display: flex; flex-direction: column; gap: 1rem;">
                    <div>
                        <label for="modalRepoInput" class="form-label" style="font-weight: 600; font-size: 0.85rem; margin-bottom: 0.4rem; display: block;">
                            Nama Repositori atau URL GitHub <span style="color: var(--accent-red); font-weight: bold;">*</span>
                        </label>
                        <input type="text" name="repo" id="modalRepoInput" class="form-control" placeholder="Contoh: microsoft/PowerToys atau https://github.com/TeamNewPipe/NewPipe" required style="font-size: 0.85rem; width: 100%;">
                        <span style="font-size: 0.75rem; color: var(--text-admin-muted); display: block; margin-top: 0.4rem;">
                            Sistem akan otomatis mengambil metadata rilis terbaru, link download multi-OS, ukuran file, kategori, dan logo aplikasi.
                        </span>
                    </div>

                    <div style="background: rgba(56, 189, 248, 0.05); border: 1px dashed rgba(56, 189, 248, 0.25); border-radius: var(--radius-sm); padding: 0.75rem 1rem;">
                        <span style="font-size: 0.75rem; color: var(--accent-sky); font-weight: 600; display: block; margin-bottom: 0.25rem;">💡 Rekomendasi Repositori Populer:</span>
                        <div style="display: flex; flex-wrap: wrap; gap: 0.4rem; margin-top: 0.35rem;">
                            <span class="badge" onclick="fillRepo('TeamNewPipe/NewPipe')" style="cursor: pointer; background: rgba(255,255,255,0.06); font-size: 0.72rem; padding: 2px 7px; border-radius: 4px; color: #cbd5e1;">TeamNewPipe/NewPipe</span>
                            <span class="badge" onclick="fillRepo('microsoft/PowerToys')" style="cursor: pointer; background: rgba(255,255,255,0.06); font-size: 0.72rem; padding: 2px 7px; border-radius: 4px; color: #cbd5e1;">microsoft/PowerToys</span>
                            <span class="badge" onclick="fillRepo('termux/termux-app')" style="cursor: pointer; background: rgba(255,255,255,0.06); font-size: 0.72rem; padding: 2px 7px; border-radius: 4px; color: #cbd5e1;">termux/termux-app</span>
                            <span class="badge" onclick="fillRepo('laurent22/joplin')" style="cursor: pointer; background: rgba(255,255,255,0.06); font-size: 0.72rem; padding: 2px 7px; border-radius: 4px; color: #cbd5e1;">laurent22/joplin</span>
                        </div>
                    </div>
                </div>

                <div style="padding: 1rem 1.5rem; background: rgba(0, 0, 0, 0.15); border-top: 1px solid var(--border-admin); display: flex; justify-content: flex-end; gap: 0.75rem;">
                    <button type="button" class="btn-admin btn-admin-secondary" onclick="closeImportModal()">Batal</button>
                    <button type="submit" id="btnImportSubmit" class="btn-admin btn-admin-primary" style="display: flex; align-items: center; gap: 0.4rem;">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 14px; height: 14px;"><polyline points="9 11 12 14 22 4"></polyline><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path></svg>
                        <span>Impor Sekarang</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openImportModal() {
            const modal = document.getElementById('importRepoModal');
            const container = modal.querySelector('div');
            modal.style.display = 'flex';
            setTimeout(() => {
                modal.style.opacity = '1';
                container.style.transform = 'scale(1)';
            }, 10);
            document.getElementById('modalRepoInput').focus();
        }

        function closeImportModal() {
            const modal = document.getElementById('importRepoModal');
            const container = modal.querySelector('div');
            modal.style.opacity = '0';
            container.style.transform = 'scale(0.95)';
            setTimeout(() => {
                modal.style.display = 'none';
            }, 200);
        }

        function fillRepo(repoName) {
            document.getElementById('modalRepoInput').value = repoName;
            document.getElementById('modalRepoInput').focus();
        }

        function handleImportSubmit(e) {
            const btn = document.getElementById('btnImportSubmit');
            btn.disabled = true;
            btn.innerHTML = `<span style="display:inline-block; animation: spin 1s linear infinite;">⏳</span> Memproses...`;
        }

        // Close on ESC
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeImportModal();
            }
        });

        // Close on overlay click
        document.getElementById('importRepoModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeImportModal();
            }
        });
    </script>
@endsection
