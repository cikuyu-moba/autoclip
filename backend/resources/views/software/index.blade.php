@extends('layouts.app')

@section('title', 'Download Software Gratis Terbaru')

@section('meta_tags')
    <meta name="description" content="Pusat unduh software gratis, freeware, dan open-source terbaik untuk PC Windows. Versi terbaru, aman, bebas virus, dan langsung dari sumber resmi.">
    <meta property="og:title" content="Download Software Gratis Terbaru - Arhana">
    <meta property="og:description" content="Koleksi software gratis & open-source terpopuler untuk Windows. Versi teranyar selalu terupdate otomatis.">
@endsection

@section('content')
    <section class="section-container">
        <!-- Hero Header -->
        <div class="section-title-wrap" style="text-align: center; margin-bottom: 2.5rem;">
            <span class="section-subtitle">Katalog Freeware &amp; Open Source</span>
            <h1 class="section-title" style="font-size: 2.4rem;">Pusat Download <span>Software Gratis</span></h1>
            <p style="color: var(--text-secondary); max-width: 650px; margin: 0.75rem auto 0; font-size: 1rem; line-height: 1.6;">
                Koleksi software terpercaya, aman, bebas virus, dan selalu diperbarui ke versi rilis resmi terbaru.
            </p>
        </div>

        <!-- Search Bar with Live Autocomplete -->
        <div class="search-container" style="max-width: 580px; margin: 0 auto 1.75rem; position: relative;">
            <form action="{{ route('software.index') }}" method="GET" style="display: flex; gap: 0.5rem; width: 100%;">
                @if(request('category'))
                    <input type="hidden" name="category" value="{{ request('category') }}">
                @endif
                @if(request('os'))
                    <input type="hidden" name="os" value="{{ request('os') }}">
                @endif
                @if(request('sort'))
                    <input type="hidden" name="sort" value="{{ request('sort') }}">
                @endif

                <div style="position: relative; flex: 1;">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama software, developer, atau utilitas..." style="width: 100%; padding: 0.8rem 1rem 0.8rem 2.75rem; border: 1px solid var(--border-color); border-radius: var(--radius-md); background: var(--bg-secondary); color: var(--text-primary); font-size: 0.95rem; transition: all 0.25s ease; box-shadow: var(--shadow-sm);" onfocus="this.style.borderColor='#64748b'; this.style.boxShadow='0 0 0 3px rgba(100, 116, 139, 0.12)'" onblur="this.style.borderColor='var(--border-color)'; this.style.boxShadow='var(--shadow-sm)'">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); width: 18px; height: 18px; color: var(--text-muted);"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                    
                    <!-- Floating Suggestions Dropdown -->
                    <div id="softwareSuggestResults" style="display: none; position: absolute; left: 0; right: 0; top: calc(100% + 8px); background: var(--bg-secondary); border: 1px solid var(--border-color); border-radius: var(--radius-md); box-shadow: var(--shadow-lg); z-index: 50; overflow: hidden; transition: all 0.2s;"></div>
                </div>

                <button type="submit" class="btn btn-primary" style="padding: 0.8rem 1.4rem; display: flex; align-items: center; justify-content: center; gap: 0.25rem;">
                    Cari
                </button>
                @if(request('search'))
                    <a href="{{ route('software.index', request()->only(['category', 'os', 'sort'])) }}" class="btn btn-secondary" style="padding: 0.8rem 1.1rem; display: flex; align-items: center; justify-content: center; text-decoration: none;">
                        Reset
                    </a>
                @endif
            </form>
        </div>

        <!-- Operating System Tabs (Submenu: Windows, Mac, Linux, Android) -->
        <div class="os-tabs-container" style="display: flex; gap: 0.6rem; justify-content: center; flex-wrap: wrap; margin-bottom: 1.5rem;">
            @php
                $currentOs = request('os');
                $osItems = [
                    ['slug' => null, 'label' => 'Semua OS', 'icon' => '💻', 'count' => $osCounts['all'] ?? 0],
                    ['slug' => 'windows', 'label' => 'Windows', 'icon' => '🪟', 'count' => $osCounts['windows'] ?? 0],
                    ['slug' => 'mac', 'label' => 'Mac (macOS)', 'icon' => '🍎', 'count' => $osCounts['mac'] ?? 0],
                    ['slug' => 'linux', 'label' => 'Linux', 'icon' => '🐧', 'count' => $osCounts['linux'] ?? 0],
                    ['slug' => 'android', 'label' => 'Android', 'icon' => '🤖', 'count' => $osCounts['android'] ?? 0],
                ];
            @endphp
            @foreach($osItems as $item)
                @php
                    $isActive = ($currentOs === $item['slug']) || (empty($currentOs) && $item['slug'] === null);
                    $params = array_filter(['os' => $item['slug']] + request()->only(['search', 'category', 'sort']));
                @endphp
                <a href="{{ route('software.index', $params) }}" 
                   class="os-tab-btn {{ $isActive ? 'active' : '' }}"
                   style="text-decoration: none; padding: 0.55rem 1.15rem; border-radius: 8px; font-size: 0.875rem; font-weight: 700; display: inline-flex; align-items: center; gap: 0.5rem; border: 1px solid {{ $isActive ? 'var(--accent-sky)' : 'var(--border-color)' }}; background: {{ $isActive ? 'rgba(2, 132, 199, 0.12)' : 'var(--bg-card)' }}; color: {{ $isActive ? 'var(--accent-sky)' : 'var(--text-primary)' }}; box-shadow: var(--shadow-sm); transition: all 0.2s ease;">
                    <span style="font-size: 1.1rem;">{{ $item['icon'] }}</span>
                    <span>{{ $item['label'] }}</span>
                    <span style="background: {{ $isActive ? 'var(--accent-sky)' : 'rgba(148, 163, 184, 0.18)' }}; color: {{ $isActive ? '#ffffff' : 'var(--text-secondary)' }}; font-size: 0.7rem; font-weight: 800; padding: 1px 7px; border-radius: 10px;">{{ $item['count'] }}</span>
                </a>
            @endforeach
        </div>

        <!-- Category Tabs Filter (Subtle & Elegant) -->
        @if(isset($categories) && $categories->count() > 0)
            <div class="category-tabs-container" style="display: flex; gap: 0.5rem; justify-content: center; flex-wrap: wrap; margin-bottom: 2rem;">
                <a href="{{ route('software.index', request()->only(['search', 'os', 'sort'])) }}" 
                   class="category-tab-btn {{ !request()->has('category') ? 'active' : '' }}"
                   style="text-decoration: none; padding: 0.45rem 1rem; border-radius: 100px; font-size: 0.825rem; font-weight: 500; border: 1px solid {{ !request()->has('category') ? 'var(--text-primary)' : 'var(--border-color)' }}; background: {{ !request()->has('category') ? 'var(--text-primary)' : 'var(--bg-secondary)' }}; color: {{ !request()->has('category') ? '#ffffff' : 'var(--text-secondary)' }}; transition: all 0.2s ease;">
                    Semua Kategori ({{ \App\Models\Software::active()->count() }})
                </a>
                @foreach($categories as $cat)
                    @if($cat->softwares_count > 0)
                        <a href="{{ route('software.index', array_filter(['category' => $cat->slug] + request()->only(['search', 'os', 'sort']))) }}"
                           class="category-tab-btn {{ request()->query('category') == $cat->slug ? 'active' : '' }}"
                           style="text-decoration: none; padding: 0.45rem 1rem; border-radius: 100px; font-size: 0.825rem; font-weight: 500; border: 1px solid {{ request()->query('category') == $cat->slug ? 'var(--text-primary)' : 'var(--border-color)' }}; background: {{ request()->query('category') == $cat->slug ? 'var(--text-primary)' : 'var(--bg-secondary)' }}; color: {{ request()->query('category') == $cat->slug ? '#ffffff' : 'var(--text-secondary)' }}; transition: all 0.2s ease;">
                            {{ $cat->name }} ({{ $cat->softwares_count }})
                        </a>
                    @endif
                @endforeach
            </div>
        @endif

        <!-- Sorting & Status Bar -->
        <div style="max-width: 1200px; margin: 0 auto 1.5rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; padding: 0.5rem 0.25rem;">
            <div style="font-size: 0.875rem; color: var(--text-secondary);">
                Menampilkan <strong>{{ $softwares->total() }}</strong> software
                @if(request('category'))
                    dalam kategori <em>"{{ request('category') }}"</em>
                @endif
                @if(request('search'))
                    untuk pencarian <em>"{{ request('search') }}"</em>
                @endif
            </div>

            <div style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.85rem;">
                <span style="color: var(--text-muted);">Urutkan:</span>
                <a href="{{ route('software.index', array_filter(['sort' => 'updated'] + request()->only(['search', 'category', 'os']))) }}" 
                   style="text-decoration: none; padding: 0.35rem 0.75rem; border-radius: var(--radius-sm); font-weight: {{ request('sort', 'updated') === 'updated' ? '700' : '500' }}; background: {{ request('sort', 'updated') === 'updated' ? 'var(--accent-sky)' : 'var(--bg-secondary)' }}; color: {{ request('sort', 'updated') === 'updated' ? '#fff' : 'var(--text-secondary)' }}; border: 1px solid var(--border-color);">
                    Baru Diperbarui
                </a>
                <a href="{{ route('software.index', array_filter(['sort' => 'popular'] + request()->only(['search', 'category', 'os']))) }}" 
                   style="text-decoration: none; padding: 0.35rem 0.75rem; border-radius: var(--radius-sm); font-weight: {{ request('sort') === 'popular' ? '700' : '500' }}; background: {{ request('sort') === 'popular' ? 'var(--accent-sky)' : 'var(--bg-secondary)' }}; color: {{ request('sort') === 'popular' ? '#fff' : 'var(--text-secondary)' }}; border: 1px solid var(--border-color);">
                    Terpopuler
                </a>
                <a href="{{ route('software.index', array_filter(['sort' => 'name'] + request()->only(['search', 'category', 'os']))) }}" 
                   style="text-decoration: none; padding: 0.35rem 0.75rem; border-radius: var(--radius-sm); font-weight: {{ request('sort') === 'name' ? '700' : '500' }}; background: {{ request('sort') === 'name' ? 'var(--accent-sky)' : 'var(--bg-secondary)' }}; color: {{ request('sort') === 'name' ? '#fff' : 'var(--text-secondary)' }}; border: 1px solid var(--border-color);">
                    A-Z
                </a>
            </div>
        </div>

        <!-- Software Grid -->
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 1.5rem; max-width: 1200px; margin: 0 auto;">
            @forelse($softwares as $software)
                <div class="software-card" style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 1.5rem; display: flex; flex-direction: column; justify-content: space-between; transition: all 0.3s ease; box-shadow: var(--shadow-sm); position: relative;">
                    
                    @if($software->is_featured)
                        <div style="position: absolute; top: 12px; right: 12px; background: rgba(245, 158, 11, 0.12); color: var(--accent-amber); border: 1px solid rgba(245, 158, 11, 0.3); font-size: 0.65rem; font-weight: 800; padding: 2px 8px; border-radius: 100px; letter-spacing: 0.5px;">
                            POPULER
                        </div>
                    @endif

                    <div>
                        <!-- Header with Icon & Titles -->
                        <div style="display: flex; gap: 1rem; align-items: flex-start; margin-bottom: 1rem;">
                            <a href="{{ route('software.show', $software->slug) }}" style="flex-shrink: 0;">
                                <div style="width: 58px; height: 58px; border-radius: 12px; border: 1px solid var(--border-color); background: #ffffff; display: flex; align-items: center; justify-content: center; overflow: hidden; padding: 6px; box-shadow: var(--shadow-sm); transition: transform 0.2s;" onmouseenter="this.style.transform='scale(1.05)'" onmouseleave="this.style.transform='scale(1)'">
                                    <img src="{{ $software->icon_url }}" alt="{{ $software->title }}" style="width: 100%; height: 100%; object-fit: contain;">
                                </div>
                            </a>

                            <div style="flex: 1; min-width: 0;">
                                <h3 style="font-size: 1.15rem; font-weight: 700; margin-bottom: 0.2rem; line-height: 1.3;">
                                    <a href="{{ route('software.show', $software->slug) }}" style="color: var(--text-primary); text-decoration: none; transition: color 0.2s;" onmouseenter="this.style.color='var(--accent-sky)'" onmouseleave="this.style.color='var(--text-primary)'">
                                        {{ $software->title }}
                                    </a>
                                </h3>
                                <div style="font-size: 0.8rem; color: var(--text-muted); margin-bottom: 0.4rem;">
                                    {{ $software->developer ?? 'Komunitas Open Source' }}
                                </div>
                                <div style="display: flex; flex-wrap: wrap; gap: 0.35rem; align-items: center;">
                                    <span style="background: rgba(2, 132, 199, 0.1); color: var(--accent-sky); font-size: 0.725rem; font-weight: 700; padding: 2px 7px; border-radius: 4px;">
                                        v{{ $software->version }}
                                    </span>
                                    <span style="background: rgba(5, 150, 105, 0.1); color: var(--accent-emerald); font-size: 0.725rem; font-weight: 600; padding: 2px 7px; border-radius: 4px;">
                                        {{ $software->license }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Description -->
                        <p style="font-size: 0.875rem; color: var(--text-secondary); line-height: 1.5; margin-bottom: 1rem;">
                            {{ Str::limit($software->description, 110) }}
                        </p>

                        <!-- OS Compatibility Badges -->
                        <div style="display: flex; flex-wrap: wrap; gap: 0.35rem; align-items: center; margin-bottom: 1.25rem;">
                            @foreach($software->supported_os_list as $osSlug)
                                @php $meta = \App\Models\Software::getOsMeta($osSlug); @endphp
                                <span style="background: {{ $meta['badge_bg'] }}; color: {{ $meta['badge_color'] }}; font-size: 0.72rem; font-weight: 700; padding: 2px 7px; border-radius: 4px; display: inline-flex; align-items: center; gap: 3px;" title="Mendukung {{ $meta['label'] }}">
                                    <span>{{ $meta['icon'] }}</span>
                                    <span>{{ $meta['label'] }}</span>
                                </span>
                            @endforeach
                        </div>
                    </div>

                    <!-- Footer Info & Action Buttons -->
                    <div style="border-top: 1px solid var(--border-color); padding-top: 1rem; margin-top: auto;">
                        <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.75rem; color: var(--text-muted); margin-bottom: 0.85rem;">
                            <span title="Kompatibilitas Utama">💻 {{ Str::limit($software->os_compatibility, 22) }}</span>
                            <span title="Ukuran File">📦 {{ $software->file_size ?? 'Gratis' }}</span>
                        </div>

                        <div style="display: flex; gap: 0.5rem;">
                            <a href="{{ route('software.show', $software->slug) }}" class="btn btn-secondary btn-sm" style="flex: 1; text-align: center; text-decoration: none; padding: 0.6rem 0.5rem; font-size: 0.825rem;">
                                Detail Info
                            </a>
                            <a href="{{ route('software.download', array_filter(['slug' => $software->slug, 'os' => request('os')])) }}" class="btn btn-primary btn-sm" style="flex: 1.2; text-align: center; text-decoration: none; padding: 0.6rem 0.5rem; font-size: 0.825rem; display: flex; align-items: center; justify-content: center; gap: 0.35rem;">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 14px; height: 14px;"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                                Download
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div style="text-align: center; grid-column: 1/-1; padding: 4rem 2rem; background: var(--bg-card); border-radius: var(--radius-md); border: 1px solid var(--border-color);">
                    <div style="font-size: 3rem; margin-bottom: 1rem;">🔍</div>
                    <h3 style="font-size: 1.25rem; margin-bottom: 0.5rem; color: var(--text-primary);">Tidak ada software yang ditemukan</h3>
                    <p style="color: var(--text-secondary); margin-bottom: 1.5rem; font-size: 0.95rem;">
                        @if(request('search'))
                            Tidak ada hasil untuk kata kunci <strong>"{{ request('search') }}"</strong>.
                        @else
                            Belum ada software dalam kategori ini.
                        @endif
                    </p>
                    <a href="{{ route('software.index') }}" class="btn btn-primary">Lihat Semua Software</a>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($softwares->hasPages())
            <div style="margin-top: 1.5rem;">
                {{ $softwares->appends(request()->query())->links() }}
            </div>
        @endif
    </section>

    <!-- Custom CSS for Card Hovers -->
    <style>
        .software-card:hover {
            transform: translateY(-4px);
            border-color: rgba(2, 132, 199, 0.3) !important;
            box-shadow: var(--shadow-md) !important;
        }
        .category-tab-btn:hover {
            border-color: var(--text-primary) !important;
            color: var(--text-primary) !important;
            background: rgba(15, 23, 42, 0.05) !important;
            transform: translateY(-1px);
        }
        .category-tab-btn.active:hover {
            color: #ffffff !important;
            background: var(--text-primary) !important;
        }
    </style>

    <!-- Live Search Autocomplete Script -->
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.querySelector('input[name="search"]');
        const suggestBox = document.getElementById('softwareSuggestResults');
        let debounceTimeout;
        
        if (searchInput && suggestBox) {
            searchInput.setAttribute('autocomplete', 'off');
            
            searchInput.addEventListener('input', function () {
                clearTimeout(debounceTimeout);
                const query = this.value.trim();
                
                if (query.length < 2) {
                    suggestBox.style.display = 'none';
                    suggestBox.innerHTML = '';
                    return;
                }
                
                suggestBox.style.display = 'block';
                suggestBox.innerHTML = '<div style="padding: 0.75rem 1rem; color: var(--text-muted); font-size: 0.85rem; font-style: italic;">Mencari software...</div>';
                
                debounceTimeout = setTimeout(() => {
                    fetch(`/api/software/suggest?search=${encodeURIComponent(query)}`)
                        .then(res => res.json())
                        .then(data => {
                            if (!data || data.length === 0) {
                                suggestBox.innerHTML = '<div style="padding: 0.75rem 1rem; color: var(--text-muted); font-size: 0.85rem;">Tidak ada software yang cocok.</div>';
                                return;
                            }
                            
                            let html = '<div style="padding: 0.5rem 0;">';
                            data.forEach(item => {
                                html += `
                                    <a href="${item.url}" style="display: flex; align-items: center; gap: 0.75rem; padding: 0.6rem 1rem; text-decoration: none; color: var(--text-primary); transition: background 0.15s;" onmouseenter="this.style.background='var(--bg-card-hover)'" onmouseleave="this.style.background='transparent'">
                                        <img src="${item.icon_url}" alt="${item.title}" style="width: 28px; height: 28px; object-fit: contain; border-radius: 4px; border: 1px solid var(--border-color); padding: 2px;">
                                        <div style="flex: 1; min-width: 0;">
                                            <div style="font-weight: 600; font-size: 0.9rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">${item.title}</div>
                                            <div style="font-size: 0.75rem; color: var(--text-muted);">${item.developer || ''} &bull; v${item.version}</div>
                                        </div>
                                        <span style="font-size: 0.75rem; color: var(--accent-sky); font-weight: 600;">Lihat &rarr;</span>
                                    </a>
                                `;
                            });
                            html += '</div>';
                            suggestBox.innerHTML = html;
                        })
                        .catch(() => {
                            suggestBox.innerHTML = '<div style="padding: 0.75rem 1rem; color: var(--accent-danger); font-size: 0.85rem;">Gagal memuat saran.</div>';
                        });
                }, 250);
            });
            
            // Close suggestion box when clicking outside
            document.addEventListener('click', function (e) {
                if (!searchInput.contains(e.target) && !suggestBox.contains(e.target)) {
                    suggestBox.style.display = 'none';
                }
            });
        }
    });
    </script>
@endsection
