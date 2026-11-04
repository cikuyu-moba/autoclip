@extends('layouts.admin')

@section('title', 'Edit Software: ' . $software->title)
@section('page_title', 'Edit Software: ' . $software->title)

@section('admin_content')
    <div class="admin-card" style="max-width: 900px; margin: 0 auto;">
        <div class="admin-card-header">
            <h4 class="admin-card-title">Edit Data Software</h4>
            <div style="display: flex; gap: 0.5rem;">
                @if($software->sync_source === 'github' && $software->github_repo)
                    <form action="{{ route('admin.software.sync-single', $software->id) }}" method="POST" style="margin: 0;">
                        @csrf
                        <button type="submit" class="btn-admin btn-admin-secondary btn-admin-sm">Sync GitHub Sekarang</button>
                    </form>
                @endif
                <a href="{{ route('admin.software.index') }}" class="btn-admin btn-admin-secondary btn-admin-sm">Kembali</a>
            </div>
        </div>
        
        <form action="{{ route('admin.software.update', $software->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="form-row">
                <div class="form-group" style="flex: 2;">
                    <label for="title" class="form-label">Nama Software <span style="color: var(--accent-danger);">*</span></label>
                    <input type="text" name="title" id="title" class="form-control" value="{{ old('title', $software->title) }}" required>
                    @error('title')
                        <span class="error-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group" style="flex: 1;">
                    <label for="category_id" class="form-label">Kategori</label>
                    <select name="category_id" id="category_id" class="form-control">
                        <option value="">Pilih Kategori...</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id', $software->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="slug" class="form-label">Slug URL</label>
                    <input type="text" name="slug" id="slug" class="form-control" value="{{ old('slug', $software->slug) }}">
                </div>

                <div class="form-group">
                    <label for="developer" class="form-label">Nama Pengembang / Developer</label>
                    <input type="text" name="developer" id="developer" class="form-control" value="{{ old('developer', $software->developer) }}">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="version" class="form-label">Versi <span style="color: var(--accent-danger);">*</span></label>
                    <input type="text" name="version" id="version" class="form-control" value="{{ old('version', $software->version) }}" required>
                </div>

                <div class="form-group">
                    <label for="license" class="form-label">Jenis Lisensi</label>
                    <input type="text" name="license" id="license" class="form-control" value="{{ old('license', $software->license) }}">
                </div>

                <div class="form-group">
                    <label for="os_compatibility" class="form-label">Kompatibilitas OS</label>
                    <input type="text" name="os_compatibility" id="os_compatibility" class="form-control" value="{{ old('os_compatibility', $software->os_compatibility) }}">
                </div>

                <div class="form-group">
                    <label for="file_size" class="form-label">Ukuran File</label>
                    <input type="text" name="file_size" id="file_size" class="form-control" value="{{ old('file_size', $software->file_size) }}">
                </div>
            </div>

            <div class="form-group">
                <label for="download_url" class="form-label">URL Download Installer Utama <span style="color: var(--accent-danger);">*</span></label>
                <input type="url" name="download_url" id="download_url" class="form-control" value="{{ old('download_url', $software->download_url) }}" required>
                <small style="color: var(--text-admin-secondary); font-size: 0.78rem;">Link default untuk Windows atau rilis utama.</small>
            </div>

            <!-- OS Platform Support & Multi-OS Links -->
            @php
                $currentSupportedOs = old('supported_os', $software->supported_os_list);
                $currentOsDownloads = old('os_downloads', $software->os_downloads ?? []);
            @endphp
            <div style="background: rgba(15, 23, 42, 0.4); border: 1px solid var(--border-admin, #334155); padding: 1.25rem; border-radius: 8px; margin-bottom: 1.5rem;">
                <label class="form-label" style="font-weight: 700; margin-bottom: 0.5rem; display: block;">
                    🖥️ Sistem Operasi yang Didukung:
                </label>
                <div style="display: flex; gap: 1.5rem; flex-wrap: wrap; margin-bottom: 1rem;">
                    <label style="display: inline-flex; align-items: center; gap: 0.4rem; cursor: pointer; color: var(--text-admin-primary); font-size: 0.9rem;">
                        <input type="checkbox" name="supported_os[]" value="windows" {{ in_array('windows', $currentSupportedOs) ? 'checked' : '' }} style="accent-color: var(--accent-sky);">
                        <span>🪟 Windows</span>
                    </label>
                    <label style="display: inline-flex; align-items: center; gap: 0.4rem; cursor: pointer; color: var(--text-admin-primary); font-size: 0.9rem;">
                        <input type="checkbox" name="supported_os[]" value="mac" {{ in_array('mac', $currentSupportedOs) ? 'checked' : '' }} style="accent-color: #94a3b8;">
                        <span>🍎 macOS (Mac)</span>
                    </label>
                    <label style="display: inline-flex; align-items: center; gap: 0.4rem; cursor: pointer; color: var(--text-admin-primary); font-size: 0.9rem;">
                        <input type="checkbox" name="supported_os[]" value="linux" {{ in_array('linux', $currentSupportedOs) ? 'checked' : '' }} style="accent-color: #eab308;">
                        <span>🐧 Linux</span>
                    </label>
                    <label style="display: inline-flex; align-items: center; gap: 0.4rem; cursor: pointer; color: var(--text-admin-primary); font-size: 0.9rem;">
                        <input type="checkbox" name="supported_os[]" value="android" {{ in_array('android', $currentSupportedOs) ? 'checked' : '' }} style="accent-color: #10b981;">
                        <span>🤖 Android</span>
                    </label>
                </div>

                <div style="font-size: 0.8rem; color: var(--text-admin-secondary); margin-bottom: 0.75rem;">
                    Tautan Khusus OS Tertentu (Opsional jika file installer berbeda dengan link download utama di atas):
                </div>
                <div style="display: grid; grid-template-columns: 1fr; gap: 0.75rem;">
                    <div>
                        <label style="font-size: 0.785rem; color: var(--text-admin-secondary);">🍎 URL Download Mac (.dmg / pkg / website):</label>
                        <input type="url" name="os_downloads[mac]" class="form-control" placeholder="https://example.com/download/app-mac.dmg" value="{{ $currentOsDownloads['mac'] ?? '' }}">
                    </div>
                    <div>
                        <label style="font-size: 0.785rem; color: var(--text-admin-secondary);">🐧 URL Download Linux (.deb / AppImage / repo):</label>
                        <input type="url" name="os_downloads[linux]" class="form-control" placeholder="https://example.com/download/app-linux.deb" value="{{ $currentOsDownloads['linux'] ?? '' }}">
                    </div>
                    <div>
                        <label style="font-size: 0.785rem; color: var(--text-admin-secondary);">🤖 URL Download Android (.apk / Play Store):</label>
                        <input type="url" name="os_downloads[android]" class="form-control" placeholder="https://play.google.com/store/apps/details?id=..." value="{{ $currentOsDownloads['android'] ?? '' }}">
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="official_website" class="form-label">Website Resmi (Opsional)</label>
                <input type="url" name="official_website" id="official_website" class="form-control" value="{{ old('official_website', $software->official_website) }}">
            </div>

            <!-- Sync Source Settings -->
            <div style="background: var(--bg-admin-card-hover, #f8fafc); border: 1px solid var(--border-color); padding: 1.25rem; border-radius: 8px; margin-bottom: 1.5rem;">
                <h5 style="margin-bottom: 0.75rem; font-size: 0.95rem; font-weight: 700;">⚙️ Pengaturan Pembaruan Versi Otomatis</h5>
                
                <div class="form-row">
                    <div class="form-group" style="flex: 1;">
                        <label for="sync_source" class="form-label">Metode Pembaruan</label>
                        <select name="sync_source" id="sync_source" class="form-control">
                            <option value="github" {{ old('sync_source', $software->sync_source) === 'github' ? 'selected' : '' }}>GitHub Releases API (Otomatis)</option>
                            <option value="manual" {{ old('sync_source', $software->sync_source) === 'manual' ? 'selected' : '' }}>Manual (Diupdate Admin Sendiri)</option>
                        </select>
                    </div>

                    <div class="form-group" style="flex: 2;">
                        <label for="github_repo" class="form-label">Repositori GitHub (Format: owner/repo)</label>
                        <input type="text" name="github_repo" id="github_repo" class="form-control" placeholder="Contoh: obsproject/obs-studio" value="{{ old('github_repo', $software->github_repo) }}">
                        @if($software->last_synced_at)
                            <small style="color: var(--accent-emerald); font-size: 0.75rem;">Terakhir berhasil disinkronkan: {{ $software->last_synced_at->format('d M Y H:i') }} ({{ $software->last_synced_at->diffForHumans() }})</small>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Icon -->
            <div class="form-row" style="align-items: center;">
                @if($software->icon)
                    <div style="margin-right: 1rem; text-align: center;">
                        <div style="width: 50px; height: 50px; border-radius: 8px; border: 1px solid var(--border-color); background: #fff; display: flex; align-items: center; justify-content: center; overflow: hidden; padding: 4px; margin-bottom: 0.25rem;">
                            <img src="{{ $software->icon_url }}" alt="Preview" style="width: 100%; height: 100%; object-fit: contain;">
                        </div>
                        <span style="font-size: 0.7rem; color: var(--text-admin-secondary);">Ikon Saat Ini</span>
                    </div>
                @endif

                <div class="form-group" style="flex: 1;">
                    <label for="icon_file" class="form-label">Ganti File Logo / Icon</label>
                    <input type="file" name="icon_file" id="icon_file" class="form-control" accept="image/*">
                </div>

                <div class="form-group" style="flex: 1;">
                    <label for="icon_url" class="form-label">Atau Ganti URL Logo Eksternal</label>
                    <input type="url" name="icon_url" id="icon_url" class="form-control" value="{{ old('icon_url', filter_var($software->icon, FILTER_VALIDATE_URL) ? $software->icon : '') }}">
                </div>
            </div>

            <div class="form-group">
                <label for="description" class="form-label">Ringkasan / Deskripsi Singkat</label>
                <textarea name="description" id="description" class="form-control" rows="3">{{ old('description', $software->description) }}</textarea>
            </div>

            <div class="form-group">
                <label for="features" class="form-label">Fitur Lengkap (Bisa Format HTML)</label>
                <textarea name="features" id="features" class="form-control" rows="6">{{ old('features', $software->features) }}</textarea>
            </div>

            <div style="display: flex; gap: 2rem; margin-bottom: 2rem;">
                <label style="display: inline-flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                    <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $software->is_featured) ? 'checked' : '' }} style="width: 18px; height: 18px;">
                    <span style="font-size: 0.9rem; font-weight: 600;">Jadikan Featured (Sorotan)</span>
                </label>

                <label style="display: inline-flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $software->is_active) ? 'checked' : '' }} style="width: 18px; height: 18px;">
                    <span style="font-size: 0.9rem; font-weight: 600;">Status Aktif / Ditampilkan</span>
                </label>
            </div>

            <button type="submit" class="btn-admin btn-admin-primary">Simpan Perubahan</button>
        </form>
    </div>
@endsection
