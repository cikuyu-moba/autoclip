@extends('layouts.admin')

@section('title', 'Edit Produk')
@section('page_title', 'Perbarui Produk')

@section('admin_content')
    <div class="admin-card" style="max-width: 800px; margin: 0 auto;">
        <div class="admin-card-header">
            <h4 class="admin-card-title">Edit Produk: {{ Str::limit($product->name, 35) }}</h4>
            <a href="{{ route('admin.products.index') }}" class="btn-admin btn-admin-secondary btn-admin-sm">Kembali</a>
        </div>
        
        <form action="{{ route('admin.products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="form-group">
                <label for="name" class="form-label">Nama Produk</label>
                <input type="text" name="name" id="name" class="form-control" placeholder="Tulis nama produk..." value="{{ old('name', $product->name) }}" required>
                @error('name')
                    <span class="error-feedback">{{ $message }}</span>
                @enderror
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="price" class="form-label">Harga (Rupiah)</label>
                    <input type="number" name="price" id="price" class="form-control" placeholder="Contoh: 150000" value="{{ old('price', $product->price) }}" min="0" required>
                    @error('price')
                        <span class="error-feedback">{{ $message }}</span>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label class="form-label">Kategori Produk (Pilih Satu atau Lebih)</label>
                    <div id="productCategoriesWrapper" style="display: flex; flex-wrap: wrap; gap: 1rem; padding: 0.75rem; background: var(--bg-admin-card); border: 1px solid var(--border-color); border-radius: 6px;">
                        @foreach($categories as $category)
                            <label style="display: inline-flex; align-items: center; gap: 0.5rem; cursor: pointer; color: var(--text-admin-secondary); font-size: 0.9rem; margin-bottom: 0;">
                                <input type="checkbox" name="category_ids[]" value="{{ $category->id }}" 
                                    {{ (is_array(old('category_ids')) && in_array($category->id, old('category_ids'))) || (!is_array(old('category_ids')) && $product->categories->contains($category->id)) ? 'checked' : '' }}
                                    style="accent-color: var(--accent-blue); width: 16px; height: 16px; cursor: pointer;">
                                {{ $category->name }}
                            </label>
                        @endforeach
                    </div>
                    @error('category_ids')
                        <span class="error-feedback">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="tokopedia_link" class="form-label">Link Tokopedia (Opsional)</label>
                    <input type="url" name="tokopedia_link" id="tokopedia_link" class="form-control" placeholder="https://tokopedia.com/..." value="{{ old('tokopedia_link', $product->tokopedia_link) }}">
                    @error('tokopedia_link')
                        <span class="error-feedback">{{ $message }}</span>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label for="shopee_link" class="form-label">Link Shopee (Opsional)</label>
                    <input type="url" name="shopee_link" id="shopee_link" class="form-control" placeholder="https://shopee.co.id/..." value="{{ old('shopee_link', $product->shopee_link) }}">
                    @error('shopee_link')
                        <span class="error-feedback">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            
            <div class="form-group">
                <label class="form-label" style="font-weight: 600;">Gambar Produk (Maksimal 5 Gambar, Slot 1 adalah Gambar Utama)</label>
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(130px, 1fr)); gap: 1rem; margin-top: 0.5rem;">
                    @for ($i = 1; $i <= 5; $i++)
                        @php
                            $fieldName = $i === 1 ? 'image' : "image{$i}";
                            $previewId = "preview_{$fieldName}";
                            $placeholderId = "placeholder_{$fieldName}";
                            $inputId = "input_{$fieldName}";
                            $hasImage = !empty($product->$fieldName);
                        @endphp
                        <div class="image-upload-wrapper" style="flex-direction: column; align-items: center; gap: 0.5rem; border: 1px dashed var(--border-color); border-radius: var(--radius-md); padding: 1rem; background: var(--bg-card); position: relative; display: flex;">
                            <div class="image-preview-box" style="width: 100px; height: 100px; margin: 0 auto; display: flex; justify-content: center; align-items: center; overflow: hidden; border-radius: var(--radius-sm); background: rgba(0,0,0,0.02); border: 1px solid var(--border-color);">
                                @if($hasImage)
                                    <img id="{{ $previewId }}" src="{{ asset('storage/' . $product->$fieldName) }}" alt="Preview" style="width: 100%; height: 100%; object-fit: cover;">
                                    <svg id="{{ $placeholderId }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="width: 32px; height: 32px; color: var(--text-muted); display: none;"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
                                @else
                                    <img id="{{ $previewId }}" src="" alt="Preview" style="display: none; width: 100%; height: 100%; object-fit: cover;">
                                    <svg id="{{ $placeholderId }}" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="width: 32px; height: 32px; color: var(--text-muted);"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
                                @endif
                            </div>
                            <div style="text-align: center; width: 100%;">
                                <label for="{{ $inputId }}" class="btn-admin btn-admin-secondary btn-admin-sm" style="display: block; cursor: pointer; padding: 0.25rem 0.5rem; font-size: 0.75rem; text-align: center;">Pilih Foto {{ $i }}</label>
                                <input type="file" name="{{ $fieldName }}" id="{{ $inputId }}" accept="image/*" 
                                       onchange="previewImageUpload(this, '{{ $previewId }}', '{{ $placeholderId }}')" 
                                       style="display: none;">
                            </div>
                            @if ($i === 1)
                                <span style="position: absolute; top: 4px; left: 4px; background: var(--accent-sky); color: #fff; font-size: 0.65rem; font-weight: 700; padding: 2px 6px; border-radius: 4px; text-transform: uppercase;">Utama</span>
                            @endif
                        </div>
                    @endfor
                </div>
                <p style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.75rem;">Biarkan kosong jika tidak ingin mengubah gambar produk. Maksimal 2MB per gambar.</p>
                @error('image') <span class="error-feedback">{{ $message }}</span> @enderror
                @error('image2') <span class="error-feedback">{{ $message }}</span> @enderror
                @error('image3') <span class="error-feedback">{{ $message }}</span> @enderror
                @error('image4') <span class="error-feedback">{{ $message }}</span> @enderror
                @error('image5') <span class="error-feedback">{{ $message }}</span> @enderror
            </div>
            
            <div class="form-group" style="margin-bottom: 2.5rem;">
                <label for="description" class="form-label">Deskripsi Produk</label>
                <textarea name="description" id="description" class="form-control" style="min-height: 150px;" required>{{ old('description', $product->description) }}</textarea>
                @error('description')
                    <span class="error-feedback">{{ $message }}</span>
                @enderror
            </div>
            
            <button type="submit" class="btn-admin btn-admin-primary" style="width: 100%; padding: 0.85rem;">Perbarui Produk</button>
        </form>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            new ArhanaMultiSelect('productCategoriesWrapper', 'Pilih Kategori Produk...');
        });
    </script>
@endsection
