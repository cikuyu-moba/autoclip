@extends('layouts.admin')

@section('title', 'Kategori Berita')
@section('page_title', 'Kategori Berita &amp; Artikel')

@section('admin_content')
    <div class="dashboard-grid" style="grid-template-columns: 1.3fr 0.7fr;">
        <!-- Left: Categories List Table -->
        <div class="admin-card">
            <div class="admin-card-header">
                <h4 class="admin-card-title">Daftar Kategori</h4>
            </div>
            
            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Nama Kategori</th>
                            <th>Tipe</th>
                            <th>Slug Kategori</th>
                            <th>Jumlah Item</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categories as $cat)
                            <tr>
                                <td style="font-weight: 700;">{{ $cat->name }}</td>
                                <td>
                                    @if($cat->type === 'shop')
                                        <span class="badge-admin" style="background: rgba(245, 158, 11, 0.1); color: var(--accent-amber); border: 1px solid rgba(245, 158, 11, 0.2); padding: 3px 8px; border-radius: 4px; font-size: 0.75rem; font-weight: 600;">Toko / Produk</span>
                                    @elseif($cat->type === 'software')
                                        <span class="badge-admin" style="background: rgba(16, 185, 129, 0.1); color: var(--accent-emerald); border: 1px solid rgba(16, 185, 129, 0.2); padding: 3px 8px; border-radius: 4px; font-size: 0.75rem; font-weight: 600;">Software</span>
                                    @else
                                        <span class="badge-admin" style="background: rgba(56, 189, 248, 0.1); color: var(--accent-sky); border: 1px solid rgba(56, 189, 248, 0.2); padding: 3px 8px; border-radius: 4px; font-size: 0.75rem; font-weight: 600;">Berita / Artikel</span>
                                    @endif
                                </td>
                                <td style="font-family: monospace; font-size: 0.8rem; color: var(--accent-blue);">{{ $cat->slug }}</td>
                                <td>
                                    @if($cat->type === 'shop')
                                        <span class="badge-admin badge-amber" style="font-size: 0.75rem; font-weight: 600;">{{ $cat->products_count }} Produk</span>
                                    @elseif($cat->type === 'software')
                                        <span class="badge-admin" style="background: rgba(16, 185, 129, 0.15); color: #059669; font-size: 0.75rem; font-weight: 600; padding: 2px 8px; border-radius: 4px;">{{ $cat->softwares_count }} Software</span>
                                    @else
                                        <span class="badge-admin badge-blue" style="font-size: 0.75rem; font-weight: 600;">{{ $cat->posts_count }} Berita</span>
                                    @endif
                                </td>
                                <td class="actions-cell">
                                    <button class="btn-admin btn-admin-secondary btn-admin-sm" 
                                            onclick="editCategory({{ json_encode($cat) }})">
                                        Edit
                                    </button>
                                    <form action="{{ route('admin.categories.destroy', $cat->id) }}" method="POST" 
                                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus kategori ini? Seluruh item dalam kategori ini akan kehilangan relasinya.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-admin btn-admin-danger btn-admin-sm">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" style="text-align: center; color: var(--text-admin-secondary);">Belum ada kategori terdaftar.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Right: Add / Edit Card Form -->
        <div class="admin-card">
            <div class="admin-card-header">
                <h4 class="admin-card-title" id="formTitle">Tambah Kategori</h4>
            </div>
            
            <form id="categoryForm" action="{{ route('admin.categories.store') }}" method="POST">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">
                
                <div class="form-group" style="margin-bottom: 1.5rem;">
                    <label for="nameInput" class="form-label">Nama Kategori</label>
                    <input type="text" name="name" id="nameInput" class="form-control" placeholder="Contoh: Edukasi Teknologi" required>
                </div>

                <div class="form-group" style="margin-bottom: 2rem;">
                    <label for="typeSelect" class="form-label">Tipe Kategori</label>
                    <select name="type" id="typeSelect" class="form-control" required>
                        <option value="news">Berita / Artikel</option>
                        <option value="shop">Toko / Produk</option>
                        <option value="software">Software / Aplikasi</option>
                    </select>
                </div>
                
                <div style="display: flex; gap: 0.5rem;">
                    <button type="submit" class="btn-admin btn-admin-primary" style="flex: 1;">Simpan Kategori</button>
                    <button type="button" id="cancelEditBtn" class="btn-admin btn-admin-secondary" style="display: none;" onclick="cancelEdit()">Batal</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        function editCategory(cat) {
            document.getElementById('formTitle').innerText = 'Edit Kategori: ' + cat.name;
            document.getElementById('formMethod').value = 'PUT';
            document.getElementById('categoryForm').action = '/admin/categories/' + cat.id;
            document.getElementById('nameInput').value = cat.name;
            document.getElementById('typeSelect').value = cat.type;
            
            // Show cancel button
            document.getElementById('cancelEditBtn').style.display = 'inline-block';
            
            // Scroll smoothly
            document.getElementById('categoryForm').scrollIntoView({ behavior: 'smooth' });
        }

        function cancelEdit() {
            document.getElementById('formTitle').innerText = 'Tambah Kategori';
            document.getElementById('formMethod').value = 'POST';
            document.getElementById('categoryForm').action = "{{ route('admin.categories.store') }}";
            document.getElementById('nameInput').value = '';
            document.getElementById('typeSelect').value = 'news';
            
            // Hide cancel button
            document.getElementById('cancelEditBtn').style.display = 'none';
        }
    </script>
@endsection
