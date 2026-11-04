@extends('layouts.admin')

@section('title', 'Tulis Berita')
@section('page_title', 'Tulis Berita Baru')

@section('admin_content')
    <div class="admin-card" style="max-width: 800px; margin: 0 auto;">
        <div class="admin-card-header">
            <h4 class="admin-card-title">Rilis Artikel Berita Baru</h4>
            <a href="{{ route('admin.posts.index') }}" class="btn-admin btn-admin-secondary btn-admin-sm">Kembali</a>
        </div>
        
        <form action="{{ route('admin.posts.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="form-group">
                <label for="title" class="form-label">Judul Berita</label>
                <input type="text" name="title" id="title" class="form-control" placeholder="Tulis judul berita yang menarik..." value="{{ old('title') }}" required>
                @error('title')
                    <span class="error-feedback">{{ $message }}</span>
                @enderror
            </div>
            
            <div class="form-group">
                <label class="form-label">Kategori Berita (Pilih Satu atau Lebih)</label>
                <div id="postCategoriesWrapper" style="display: flex; flex-wrap: wrap; gap: 1rem; padding: 0.75rem; background: var(--bg-admin-card); border: 1px solid var(--border-color); border-radius: 6px;">
                    @foreach($categories as $cat)
                        <label style="display: inline-flex; align-items: center; gap: 0.5rem; cursor: pointer; color: var(--text-admin-secondary); font-size: 0.9rem; margin-bottom: 0;">
                            <input type="checkbox" name="category_ids[]" value="{{ $cat->id }}" 
                                {{ is_array(old('category_ids')) && in_array($cat->id, old('category_ids')) ? 'checked' : '' }}
                                style="accent-color: var(--accent-blue); width: 16px; height: 16px; cursor: pointer;">
                            {{ $cat->name }}
                        </label>
                    @endforeach
                </div>
                @error('category_ids')
                    <span class="error-feedback">{{ $message }}</span>
                @enderror
            </div>
            
            <div class="form-group">
                <label for="imageUpload" class="form-label">Gambar Utama / Cover</label>
                <div class="image-upload-wrapper">
                    <div class="image-preview-box">
                        <img id="imagePreview" src="" alt="Preview Gambar" style="display: none;">
                        <svg id="uploadPlaceholder" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
                    </div>
                    <div>
                        <input type="file" name="image" id="imageUpload" accept="image/*" 
                               onchange="previewImageUpload(this, 'imagePreview', 'uploadPlaceholder')" 
                               style="font-size: 0.85rem; color: var(--text-admin-secondary);">
                        <p style="font-size: 0.75rem; color: var(--text-admin-muted); margin-top: 0.5rem;">Format gambar: JPG, JPEG, PNG, WEBP. Maksimal 2MB.</p>
                    </div>
                </div>
                @error('image')
                    <span class="error-feedback">{{ $message }}</span>
                @enderror
            </div>
            
            <div class="form-group">
                <label for="excerpt" class="form-label">Kutipan Singkat (Excerpt)</label>
                <textarea name="excerpt" id="excerpt" class="form-control" placeholder="Tulis deskripsi singkat penarik minat pembaca (opsional, jika dikosongkan akan mengambil sebagian dari konten berita)...">{{ old('excerpt') }}</textarea>
                @error('excerpt')
                    <span class="error-feedback">{{ $message }}</span>
                @enderror
            </div>
            
            <div class="form-group" style="margin-bottom: 2.5rem;">
                <label for="content" class="form-label">Konten / Isi Berita Lengkap</label>
                <textarea name="content" id="content" class="form-control" style="min-height: 250px;" placeholder="Tulis isi artikel lengkap di sini...">{{ old('content') }}</textarea>
                @error('content')
                    <span class="error-feedback">{{ $message }}</span>
                @enderror
            </div>
            
            <div class="form-group" style="margin-bottom: 2rem;">
                <label for="published_at" class="form-label">Jadwalkan Tanggal Terbit (Opsional)</label>
                <input type="datetime-local" name="published_at" id="published_at" class="form-control" value="{{ old('published_at') }}">
                <p style="font-size: 0.75rem; color: var(--text-admin-muted); margin-top: 0.5rem;">Biarkan kosong jika ingin berita langsung diterbitkan saat ini.</p>
                @error('published_at')
                    <span class="error-feedback">{{ $message }}</span>
                @enderror
            </div>
            
            <button type="submit" class="btn-admin btn-admin-primary" style="width: 100%; padding: 0.85rem;">Terbitkan Berita</button>
        </form>
    </div>

    <style>
        .ck-editor__editable_inline {
            min-height: 250px;
            color: #000;
        }
    </style>
@endsection

@section('scripts')
    <!-- CKEditor 5 -->
    <script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
    <script>
        // Custom Upload Adapter for CKEditor 5
        class MyUploadAdapter {
            constructor(loader) {
                this.loader = loader;
            }

            upload() {
                return this.loader.file
                    .then(file => new Promise((resolve, reject) => {
                        this._initRequest();
                        this._initListeners(resolve, reject, file);
                        this._sendRequest(file);
                    }));
            }

            abort() {
                if (this.xhr) {
                    this.xhr.abort();
                }
            }

            _initRequest() {
                const xhr = this.xhr = new XMLHttpRequest();
                xhr.open('POST', "{{ route('admin.posts.upload') }}", true);
                xhr.setRequestHeader('X-CSRF-TOKEN', "{{ csrf_token() }}");
                xhr.responseType = 'json';
            }

            _initListeners(resolve, reject, file) {
                const xhr = this.xhr;
                const loader = this.loader;
                const genericErrorText = `Gagal mengunggah file: ${file.name}.`;

                xhr.addEventListener('error', () => reject(genericErrorText));
                xhr.addEventListener('abort', () => reject());
                xhr.addEventListener('load', () => {
                    const response = xhr.response;

                    if (!response || response.error) {
                        return reject(response && response.error ? response.error.message : genericErrorText);
                    }

                    resolve({
                        default: response.url
                    });
                });

                if (xhr.upload) {
                    xhr.upload.addEventListener('progress', evt => {
                        if (evt.lengthComputable) {
                            loader.uploadTotal = evt.total;
                            loader.uploaded = evt.loaded;
                        }
                    });
                }
            }

            _sendRequest(file) {
                const data = new FormData();
                data.append('upload', file);
                this.xhr.send(data);
            }
        }

        function MyCustomUploadAdapterPlugin(editor) {
            editor.plugins.get('FileRepository').createUploadAdapter = (loader) => {
                return new MyUploadAdapter(loader);
            };
        }

        document.addEventListener('DOMContentLoaded', function () {
            new ArhanaMultiSelect('postCategoriesWrapper', 'Pilih Kategori Berita...');
            
            ClassicEditor
                .create( document.querySelector( '#content' ), {
                    toolbar: [ 'heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote', '|', 'uploadImage', 'undo', 'redo' ],
                    extraPlugins: [ MyCustomUploadAdapterPlugin ]
                } )
                .catch( error => {
                    console.error( error );
                } );
        });
    </script>
@endsection
