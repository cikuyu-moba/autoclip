@extends('layouts.app')

@section('title', 'Toko Online')

@section('content')
    <section class="section-container">
        <div class="section-title-wrap">
            <span class="section-subtitle">Produk Berkualitas</span>
            <h2 class="section-title">Katalog <span>Produk Kami</span></h2>
        </div>

        <!-- Premium Search Bar -->
        <div class="search-container" style="max-width: 500px; margin: 0 auto 3rem; position: relative;">
            <form action="{{ route('shop') }}" method="GET" style="display: flex; gap: 0.5rem; width: 100%;">
                <div style="position: relative; flex: 1;">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari produk berkualitas..." style="width: 100%; padding: 0.85rem 1rem 0.85rem 2.75rem; border: 1px solid var(--border-color); border-radius: var(--radius-md); background: var(--bg-secondary); color: var(--text-primary); font-size: 0.95rem; transition: all 0.3s ease; box-shadow: var(--shadow-sm);" onfocus="this.style.borderColor='var(--accent-amber)'; this.style.boxShadow='0 0 12px rgba(245, 158, 11, 0.15)'" onblur="this.style.borderColor='var(--border-color)'; this.style.boxShadow='var(--shadow-sm)'">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); width: 18px; height: 18px; color: var(--text-muted);"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                    <!-- Floating Suggestions Dropdown -->
                    <div id="shopSuggestResults" style="display: none; position: absolute; left: 0; right: 0; top: calc(100% + 8px); background: var(--bg-secondary); border: 1px solid var(--border-color); border-radius: var(--radius-md); box-shadow: var(--shadow-lg); z-index: 50; overflow: hidden; transition: all 0.2s;"></div>
                </div>
                <button type="submit" class="btn btn-primary" style="padding: 0.85rem 1.5rem; display: flex; align-items: center; justify-content: center; gap: 0.25rem;">
                    Cari
                </button>
                @if(request('search'))
                    <a href="{{ route('shop') }}" class="btn btn-secondary" style="padding: 0.85rem 1.25rem; display: flex; align-items: center; justify-content: center; text-decoration: none;">
                        Reset
                    </a>
                @endif
            </form>
        </div>

        <!-- Premium Category Tabs Filter -->
        @if(isset($categories) && $categories->count() > 0)
            <div class="category-tabs-container" style="display: flex; gap: 0.75rem; justify-content: center; flex-wrap: wrap; margin-bottom: 2.5rem;">
                <a href="{{ route('shop', request()->only('search')) }}" 
                   class="category-tab-btn {{ !request()->has('category') ? 'active' : '' }}"
                   style="text-decoration: none; padding: 0.6rem 1.25rem; border-radius: 100px; font-size: 0.875rem; font-weight: 600; border: 1px solid var(--border-color); background: var(--bg-secondary); color: var(--text-secondary); transition: all 0.25s ease;">
                    Semua Kategori ({{ \App\Models\Product::count() }})
                </a>
                @foreach($categories as $cat)
                    @if($cat->products_count > 0)
                        <a href="{{ route('shop', ['category' => $cat->slug] + request()->only('search')) }}"
                           class="category-tab-btn {{ request()->query('category') == $cat->slug ? 'active' : '' }}"
                           style="text-decoration: none; padding: 0.6rem 1.25rem; border-radius: 100px; font-size: 0.875rem; font-weight: 600; border: 1px solid {{ request()->query('category') == $cat->slug ? 'var(--accent-amber)' : 'var(--border-color)' }}; background: {{ request()->query('category') == $cat->slug ? 'rgba(245, 158, 11, 0.08)' : 'var(--bg-secondary)' }}; color: {{ request()->query('category') == $cat->slug ? 'var(--accent-amber)' : 'var(--text-secondary)' }}; transition: all 0.25s ease;">
                            {{ $cat->name }} ({{ $cat->products_count }})
                        </a>
                    @endif
                @endforeach
            </div>
            
            <style>
                .category-tab-btn:hover {
                    border-color: var(--accent-amber) !important;
                    color: var(--accent-amber) !important;
                    background: rgba(245, 158, 11, 0.04) !important;
                    transform: translateY(-1px);
                }
                .category-tab-btn.active {
                    border-color: var(--accent-amber) !important;
                    background: rgba(245, 158, 11, 0.08) !important;
                    color: var(--accent-amber) !important;
                }
            </style>
        @endif

        @if(request()->has('category') || request()->has('search'))
            <div style="max-width: 1200px; margin: 0 auto 2rem; background-color: rgba(245, 158, 11, 0.05); border: 1px dashed rgba(245, 158, 11, 0.2); padding: 0.75rem 1.25rem; border-radius: var(--radius-sm); display: flex; justify-content: space-between; align-items: center; gap: 1rem;">
                <span style="font-size: 0.875rem; color: var(--text-secondary); line-height: 1.5;">
                    @if(request()->has('category') && request()->has('search'))
                        Menampilkan produk dalam kategori: <strong>"{{ request()->query('category') }}"</strong> dengan kata kunci: <strong>"{{ request()->query('search') }}"</strong>
                    @elseif(request()->has('category'))
                        Menampilkan produk dalam kategori: <strong>"{{ request()->query('category') }}"</strong>
                    @else
                        Menampilkan produk dengan kata kunci: <strong>"{{ request()->query('search') }}"</strong>
                    @endif
                </span>
                <a href="{{ route('shop') }}" style="font-size: 0.8rem; color: var(--accent-amber); text-decoration: underline; white-space: nowrap;">
                    Hapus Semua Filter
                </a>
            </div>
        @endif
        
        <div class="grid-3">
            @forelse($products as $product)
                <a href="{{ route('shop.show', ['id' => $product->id, 'slug' => Str::slug($product->name)]) }}" class="card-product" style="text-decoration: none;">
                    <div class="product-media">
                        @if($product->image)
                            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}">
                        @else
                            <div class="product-media-placeholder">📦</div>
                        @endif
                    </div>
                    <div class="product-body">
                        <!-- Category badges on card -->
                        @if($product->categories && $product->categories->count() > 0)
                            <div style="display: flex; gap: 0.35rem; flex-wrap: wrap; margin-bottom: 0.5rem;">
                                @foreach($product->categories as $c)
                                    <span style="font-size: 0.7rem; font-weight: 700; color: var(--accent-amber); background: rgba(245, 158, 11, 0.06); border: 1px solid rgba(245, 158, 11, 0.12); padding: 0.1rem 0.45rem; border-radius: 4px; text-transform: uppercase;">
                                        {{ $c->name }}
                                    </span>
                                @endforeach
                            </div>
                        @endif
                        <h3 class="product-name">{{ $product->name }}</h3>
                        <p class="product-desc">{{ Str::limit($product->description, 120) }}</p>
                        
                        <div class="product-footer">
                            <span class="product-price">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                            <span class="btn btn-primary btn-sm">
                                Lihat Detail
                            </span>
                        </div>
                    </div>
                </a>
            @empty
                <div style="text-align: center; grid-column: 1/-1; padding: 4rem 2rem;">
                    @if(request('search'))
                        <p style="color: var(--text-secondary); margin-bottom: 1.5rem;">Tidak ada produk yang cocok dengan kata kunci: <strong>"{{ request('search') }}"</strong></p>
                        <a href="{{ route('shop') }}" class="btn btn-secondary">Reset Pencarian</a>
                    @else
                        <p style="color: var(--text-secondary); margin-bottom: 1.5rem;">Belum ada produk yang dijual saat ini.</p>
                        <a href="{{ route('home') }}" class="btn btn-secondary">Kembali ke Beranda</a>
                    @endif
                </div>
            @endforelse
        </div>

        <!-- Custom Elegant Pagination Rendering -->
        @if($products->hasPages())
            <div style="margin-top: 3.5rem;">
                {{ $products->appends(request()->query())->links() }}
            </div>
        @endif
    </section>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.querySelector('input[name="search"]');
        const suggestBox = document.getElementById('shopSuggestResults');
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
                suggestBox.innerHTML = '<div style="padding: 0.75rem 1rem; color: var(--text-muted); font-size: 0.85rem; font-style: italic;">Mencari saran...</div>';
                
                debounceTimeout = setTimeout(() => {
                    fetch(`/api/shop/suggest?search=${encodeURIComponent(query)}`)
                        .then(res => res.json())
                        .then(data => {
                            if (data.length === 0) {
                                suggestBox.innerHTML = '<div style="padding: 0.75rem 1rem; color: var(--text-muted); font-size: 0.85rem; font-style: italic;">Tidak ada saran ditemukan.</div>';
                                return;
                           }
                           
                           suggestBox.innerHTML = '';
                           data.forEach(item => {
                               const a = document.createElement('a');
                               const slug = item.name.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)+/g, '');
                               a.href = `/shop/${item.id}-${slug}`;
                               a.style.display = 'block';
                               a.style.padding = '0.75rem 1rem';
                               a.style.color = 'var(--text-primary)';
                               a.style.fontSize = '0.9rem';
                               a.style.textDecoration = 'none';
                               a.style.borderBottom = '1px solid var(--border-color)';
                               a.style.transition = 'background 0.2s';
                               a.textContent = item.name;
                               
                               a.addEventListener('mouseenter', () => {
                                   a.style.background = 'rgba(245, 158, 11, 0.05)';
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
