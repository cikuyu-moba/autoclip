@extends('layouts.app')

@section('title', 'Berita & Catatan')

@section('content')
    <section class="section-container">
        <div class="section-title-wrap">
            <span class="section-subtitle">Pembaruan &amp; Edukasi</span>
            <h2 class="section-title">Berita &amp; <span>Catatan Teknologi</span></h2>
        </div>
        
        <div class="blog-layout">
            <!-- Left Side: Posts List -->
            <div class="blog-main-content">
                @if(request()->has('category') || request()->has('search'))
                    <div style="background-color: rgba(56, 189, 248, 0.05); border: 1px dashed rgba(56, 189, 248, 0.2); padding: 0.75rem 1.25rem; border-radius: var(--radius-sm); margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: center; gap: 1rem;">
                        <span style="font-size: 0.875rem; color: var(--text-secondary); line-height: 1.5;">
                            @if(request()->has('category') && request()->has('search'))
                                Menampilkan berita dalam kategori: <strong>"{{ request()->query('category') }}"</strong> dengan kata kunci: <strong>"{{ request()->query('search') }}"</strong>
                            @elseif(request()->has('category'))
                                Menampilkan berita dalam kategori: <strong>"{{ request()->query('category') }}"</strong>
                            @else
                                Menampilkan berita dengan kata kunci: <strong>"{{ request()->query('search') }}"</strong>
                            @endif
                        </span>
                        <a href="{{ route('news') }}" style="font-size: 0.8rem; color: var(--accent-sky); text-decoration: underline; white-space: nowrap;">
                            Hapus Semua Filter
                        </a>
                    </div>
                @endif
                
                <div class="grid-3" style="grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 1.75rem;">
                    @forelse($posts as $post)
                        <div class="card-post">
                            <div class="post-media">
                                @if($post->image)
                                    <img src="{{ asset('storage/' . $post->image) }}" alt="{{ $post->title }}">
                                @else
                                    <div class="post-media-placeholder">✍️</div>
                                @endif
                            </div>
                            <div class="post-body">
                                <div class="post-meta">
                                    <div style="display: inline-flex; flex-wrap: wrap; gap: 4px; align-items: center;">
                                        @foreach($post->categories as $cat)
                                            <span class="post-category" style="margin-right: 0;">{{ $cat->name }}</span>
                                        @endforeach
                                    </div>
                                    <span>&bull;</span>
                                    <span>{{ $post->created_at->format('d M Y') }}</span>
                                </div>
                                <h3 class="post-title">
                                    <a href="{{ route('news.show', $post->slug) }}">{{ $post->title }}</a>
                                </h3>
                                <p class="post-excerpt">{{ Str::limit($post->excerpt, 120) }}</p>
                                
                                <div class="post-footer">
                                    <a href="{{ route('news.show', $post->slug) }}" class="post-read-more">
                                        Baca Selengkapnya
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div style="text-align: center; grid-column: 1/-1; padding: 4rem 2rem;">
                            @if(request('search'))
                                <p style="color: var(--text-secondary); margin-bottom: 1.5rem;">Tidak ada artikel berita yang cocok dengan kata kunci: <strong>"{{ request('search') }}"</strong></p>
                                <a href="{{ route('news') }}" class="btn btn-secondary">Reset Pencarian</a>
                            @else
                                <p style="color: var(--text-secondary); margin-bottom: 1.5rem;">Tidak ada artikel berita yang ditemukan.</p>
                                <a href="{{ route('news') }}" class="btn btn-secondary">Lihat Semua Berita</a>
                            @endif
                        </div>
                    @endforelse
                </div>
                
                <!-- Custom Elegant Pagination Rendering -->
                @if($posts->hasPages())
                    <div style="margin-top: 3.5rem;">
                        {{ $posts->appends(request()->query())->links() }}
                    </div>
                @endif
            </div>
            
            <!-- Right Side: Sidebar Widget -->
            <div class="blog-sidebar">
                <!-- Search Widget -->
                <div class="sidebar-widget">
                    <h3 class="widget-title">Cari Artikel</h3>
                    <form action="{{ route('news') }}" method="GET" style="display: flex; gap: 0.5rem; flex-direction: column;">
                        @if(request('category'))
                            <input type="hidden" name="category" value="{{ request('category') }}">
                        @endif
                        <div style="position: relative;">
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Ketik kata kunci..." style="width: 100%; padding: 0.75rem 1rem 0.75rem 2.5rem; border: 1px solid var(--border-color); border-radius: var(--radius-sm); background: var(--bg-secondary); color: var(--text-primary); font-size: 0.875rem; transition: all 0.3s ease;" onfocus="this.style.borderColor='var(--accent-sky)'; this.style.boxShadow='0 0 10px rgba(56, 189, 248, 0.15)'" onblur="this.style.borderColor='var(--border-color)'; this.style.boxShadow='none'">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="position: absolute; left: 0.85rem; top: 50%; transform: translateY(-50%); width: 16px; height: 16px; color: var(--text-muted);"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                            <!-- Floating Suggestions Dropdown -->
                            <div id="newsSuggestResults" style="display: none; position: absolute; left: 0; right: 0; top: calc(100% + 5px); background: var(--bg-secondary); border: 1px solid var(--border-color); border-radius: var(--radius-sm); box-shadow: var(--shadow-lg); z-index: 50; overflow: hidden; transition: all 0.2s;"></div>
                        </div>
                        <div style="display: flex; gap: 0.5rem;">
                            <button type="submit" class="btn btn-primary btn-sm" style="flex: 1; padding: 0.65rem; font-size: 0.85rem; display: flex; align-items: center; justify-content: center; gap: 0.25rem;">
                                Cari
                            </button>
                            @if(request('search'))
                                <a href="{{ route('news', request()->only('category')) }}" class="btn btn-secondary btn-sm" style="padding: 0.65rem 0.85rem; font-size: 0.85rem; display: flex; align-items: center; justify-content: center; text-decoration: none;">
                                    Reset
                                </a>
                            @endif
                        </div>
                    </form>
                </div>

                <div class="sidebar-widget">
                    <h3 class="widget-title">Kategori Berita</h3>
                    <ul class="category-list">
                        <li class="category-item">
                            <a href="{{ route('news', request()->only('search')) }}" class="category-link {{ !request()->has('category') ? 'active' : '' }}">
                                <span>Semua Kategori</span>
                                <span class="category-count">{{ \App\Models\Post::count() }}</span>
                            </a>
                        </li>
                        @foreach($categories as $cat)
                            @if($cat->posts_count > 0)
                                <li class="category-item">
                                    <a href="{{ route('news', ['category' => $cat->slug] + request()->only('search')) }}" 
                                       class="category-link {{ request()->query('category') == $cat->slug ? 'active' : '' }}">
                                        <span>{{ $cat->name }}</span>
                                        <span class="category-count">{{ $cat->posts_count }}</span>
                                    </a>
                                </li>
                            @endif
                        @endforeach
                    </ul>
                </div>
                
                <div class="sidebar-widget" style="text-align: center; padding: 2rem 1.5rem;">
                    <h3 class="widget-title" style="border: none; margin-bottom: 0.5rem; text-align: center;">Butuh Bantuan?</h3>
                    <p style="font-size: 0.8rem; color: var(--text-secondary); margin-bottom: 1.5rem;">Saya siap membantu mewujudkan proyek impian Anda dengan standar kualitas premium.</p>
                    <a href="https://linkedin.com" target="_blank" rel="noopener noreferrer" class="btn btn-primary btn-sm" style="width: 100%;">Hubungi Saya</a>
                </div>
            </div>
        </div>
    </section>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.querySelector('.blog-sidebar input[name="search"]');
        const suggestBox = document.getElementById('newsSuggestResults');
        let debounceTimeout;
        
        if (searchInput && suggestBox) {
            searchInput.setAttribute('autocomplete', 'off');
            
            searchInput.addEventListener('input', function () {
                clearTimeout(debounceTimeout);
                const query = this.value.trim();
                
                if (query.length < 3) {
                    suggestBox.style.display = 'none';
                    suggestBox.innerHTML = '';
                    return;
                }
                
                suggestBox.style.display = 'block';
                suggestBox.innerHTML = '<div style="padding: 0.65rem 0.85rem; color: var(--text-muted); font-size: 0.8rem; font-style: italic;">Mencari...</div>';
                
                debounceTimeout = setTimeout(() => {
                    fetch(`/api/news/suggest?search=${encodeURIComponent(query)}`)
                        .then(res => res.json())
                        .then(data => {
                            if (data.length === 0) {
                                suggestBox.innerHTML = '<div style="padding: 0.65rem 0.85rem; color: var(--text-muted); font-size: 0.8rem; font-style: italic;">Tidak ada saran.</div>';
                                return;
                            }
                            
                            suggestBox.innerHTML = '';
                            data.forEach(item => {
                                const a = document.createElement('a');
                                a.href = `/news/${item.slug}`;
                                a.style.display = 'block';
                                a.style.padding = '0.65rem 0.85rem';
                                a.style.color = 'var(--text-primary)';
                                a.style.fontSize = '0.85rem';
                                a.style.textDecoration = 'none';
                                a.style.borderBottom = '1px solid var(--border-color)';
                                a.style.transition = 'background 0.2s';
                                a.textContent = item.title;
                                
                                a.addEventListener('mouseenter', () => {
                                    a.style.background = 'rgba(56, 189, 248, 0.05)';
                                });
                                a.addEventListener('mouseleave', () => {
                                    a.style.background = 'transparent';
                                });
                                suggestBox.appendChild(a);
                            });
                        })
                        .catch(err => {
                            suggestBox.style.display = 'none';
                        });
                }, 2000); // Debounce duration: 2 seconds!
            });
            
            document.addEventListener('click', function (e) {
                if (!searchInput.contains(e.target) && !suggestBox.contains(e.target)) {
                    suggestBox.style.display = 'none';
                }
            });
        }
    });
    </script>
@endsection
