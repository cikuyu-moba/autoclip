@extends('layouts.admin')

@section('title', 'Kelola Menu')
@section('page_title', 'Menu &amp; Submenu Navigasi')

@section('admin_content')
    <div class="dashboard-grid" style="grid-template-columns: 1.3fr 0.7fr;">
        <!-- Left: Hierarchical Menus List -->
        <div class="admin-card">
            <div class="admin-card-header">
                <h4 class="admin-card-title">Daftar Struktur Navigasi</h4>
            </div>
            
            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Nama Menu</th>
                            <th>Tautan (URL)</th>
                            <th>Urutan</th>
                            <th>Tipe Link</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($menus as $menu)
                            <!-- Parent Menu Row -->
                            <tr>
                                <td style="font-weight: 700; color: var(--text-admin-primary);">
                                    {{ $menu->title }}
                                </td>
                                <td style="font-family: monospace; font-size: 0.8rem; color: var(--accent-blue);">
                                    {{ $menu->url }}
                                </td>
                                <td>
                                    <span class="badge-admin">{{ $menu->order }}</span>
                                </td>
                                <td>
                                    @if($menu->is_external)
                                        <span class="badge-admin badge-blue" style="border-color: rgba(245, 158, 11, 0.25); color: var(--accent-amber);">External Redirect</span>
                                    @else
                                        <span class="badge-admin badge-emerald">Internal URL</span>
                                    @endif
                                </td>
                                <td class="actions-cell">
                                    <button class="btn-admin btn-admin-secondary btn-admin-sm" 
                                            onclick="editMenu({{ json_encode($menu) }})">
                                        Edit
                                    </button>
                                    <form action="{{ route('admin.menus.destroy', $menu->id) }}" method="POST" 
                                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus menu utama ini beserta seluruh submenunya?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-admin btn-admin-danger btn-admin-sm">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                            
                            <!-- Submenus Loop -->
                            @foreach($menu->submenus as $submenu)
                                <tr class="nested-table-row">
                                    <td class="nested-title" style="font-weight: 500;">
                                        {{ $submenu->title }}
                                    </td>
                                    <td style="font-family: monospace; font-size: 0.8rem; color: var(--text-admin-muted); padding-left: 1.5rem;">
                                        {{ $submenu->url }}
                                    </td>
                                    <td>
                                        <span class="badge-admin">{{ $submenu->order }}</span>
                                    </td>
                                    <td>
                                        @if($submenu->is_external)
                                            <span class="badge-admin badge-blue" style="border-color: rgba(245, 158, 11, 0.25); color: var(--accent-amber);">External Redirect</span>
                                        @else
                                            <span class="badge-admin badge-emerald">Internal URL</span>
                                        @endif
                                    </td>
                                    <td class="actions-cell">
                                        <button class="btn-admin btn-admin-secondary btn-admin-sm" 
                                                onclick="editMenu({{ json_encode($submenu) }})">
                                            Edit
                                        </button>
                                        <form action="{{ route('admin.menus.destroy', $submenu->id) }}" method="POST" 
                                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus submenu ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-admin btn-admin-danger btn-admin-sm">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        @empty
                            <tr>
                                <td colspan="5" style="text-align: center; color: var(--text-admin-secondary);">Belum ada menu navigasi terdaftar.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Right: Add / Edit Card Form -->
        <div class="admin-card">
            <div class="admin-card-header">
                <h4 class="admin-card-title" id="formTitle">Tambah Menu Baru</h4>
            </div>
            
            <form id="menuForm" action="{{ route('admin.menus.store') }}" method="POST">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">
                
                <div class="form-group">
                    <label for="titleInput" class="form-label">Nama Menu</label>
                    <input type="text" name="title" id="titleInput" class="form-control" placeholder="Contoh: PORTFOLIO" required>
                </div>
                
                <div class="form-group">
                    <label for="urlInput" class="form-label">Tautan Link / URL</label>
                    <input type="text" name="url" id="urlInput" class="form-control" placeholder="Contoh: /portfolio atau https://link.com" required>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="orderInput" class="form-label">Urutan Tampil</label>
                        <input type="number" name="order" id="orderInput" class="form-control" value="0" min="0" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="parentInput" class="form-label">Parent Menu</label>
                        <select name="parent_id" id="parentInput" class="form-control" style="background-color: var(--bg-admin-card);">
                            <option value="">-- Menu Utama --</option>
                            @foreach($menus as $menu)
                                <option value="{{ $menu->id }}">{{ $menu->title }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                
                <div class="form-group" style="margin-bottom: 2rem;">
                    <label class="checkbox-label">
                        <input type="checkbox" name="is_external" id="externalInput" value="1">
                        <span>Buka di tab baru (External link)</span>
                    </label>
                </div>
                
                <div style="display: flex; gap: 0.5rem;">
                    <button type="submit" class="btn-admin btn-admin-primary" style="flex: 1;">Simpan Menu</button>
                    <button type="button" id="cancelEditBtn" class="btn-admin btn-admin-secondary" style="display: none;" onclick="cancelEdit()">Batal</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        function editMenu(menu) {
            document.getElementById('formTitle').innerText = 'Edit Menu: ' + menu.title;
            document.getElementById('formMethod').value = 'PUT';
            document.getElementById('menuForm').action = '/admin/menus/' + menu.id;
            document.getElementById('titleInput').value = menu.title;
            document.getElementById('urlInput').value = menu.url;
            document.getElementById('orderInput').value = menu.order;
            document.getElementById('parentInput').value = menu.parent_id || '';
            document.getElementById('externalInput').checked = menu.is_external === 1 || menu.is_external === true;
            
            // Show cancel button
            document.getElementById('cancelEditBtn').style.display = 'inline-block';
            
            // Scroll to form smoothly
            document.getElementById('menuForm').scrollIntoView({ behavior: 'smooth' });
        }

        function cancelEdit() {
            document.getElementById('formTitle').innerText = 'Tambah Menu Baru';
            document.getElementById('formMethod').value = 'POST';
            document.getElementById('menuForm').action = "{{ route('admin.menus.store') }}";
            document.getElementById('titleInput').value = '';
            document.getElementById('urlInput').value = '';
            document.getElementById('orderInput').value = '0';
            document.getElementById('parentInput').value = '';
            document.getElementById('externalInput').checked = false;
            
            // Hide cancel button
            document.getElementById('cancelEditBtn').style.display = 'none';
        }
    </script>
@endsection
