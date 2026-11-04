@extends('layouts.app')

@section('title', 'Beranda')

@section('content')
    <style>
        .hero-section {
            position: relative;
            overflow: hidden;
            background-color: var(--bg-primary);
            min-height: 600px;
            display: flex;
            align-items: center;
        }

        .hero-bg-visual {
            position: absolute;
            right: 0;
            top: 0;
            bottom: 0;
            width: 65%;
            z-index: 1;
            pointer-events: none;
            overflow: hidden;
        }

        .hero-bg-visual img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .hero-bg-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(90deg,
                    var(--bg-primary) 0%,
                    var(--bg-primary) 20%,
                    rgba(248, 250, 252, 0.95) 25%,
                    rgba(248, 250, 252, 0.7) 35%,
                    rgba(248, 250, 252, 0.2) 60%,
                    rgba(248, 250, 252, 0) 100%);
        }

        @media (max-width: 991px) {
            .hero-section {
                min-height: auto;
                padding-top: 4rem;
                padding-bottom: 4rem;
            }

            .hero-bg-visual {
                width: 100%;
                opacity: 0.5;
            }

            .hero-bg-overlay {
                background: radial-gradient(circle, rgba(248, 250, 252, 0.3) 0%, var(--bg-primary) 100%);
            }
        }

        /* Premium Responsive Carousel Slider */
        .slider-container {
            position: relative;
            width: 100%;
            overflow: hidden;
            padding: 0.75rem 0;
        }
        .slider-track {
            display: flex;
            transition: transform 0.65s cubic-bezier(0.16, 1, 0.3, 1);
            gap: 1.75rem;
            box-sizing: border-box;
            will-change: transform;
        }
        .slider-item {
            flex: 0 0 calc((100% - 3.5rem) / 3);
            box-sizing: border-box;
            transition: all 0.3s ease;
        }
        @media (max-width: 991px) {
            .slider-track {
                gap: 1.25rem;
            }
            .slider-item {
                flex: 0 0 calc((100% - 1.25rem) / 2);
            }
        }
        @media (max-width: 576px) {
            .slider-item {
                flex: 0 0 100%;
            }
        }
        
        .slider-dots {
            display: flex;
            gap: 0.5rem;
            justify-content: center;
            margin-top: 2rem;
        }
        .slider-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background-color: var(--border-color);
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .slider-dot.active {
            background-color: var(--accent-amber);
            width: 24px;
            border-radius: 100px;
        }
    </style>

    <!-- News Highlights Section (NEWS) -->
    <section class="section-container section-news-accent" style="border-top: none; padding-top: 3.5rem;">
        <div class="section-title-wrap reveal-on-scroll">
            <span class="section-subtitle">Wawasan &amp; Catatan</span>
            <h2 class="section-title">Berita &amp; <span>Artikel</span></h2>
        </div>
        <div class="slider-container reveal-on-scroll" id="newsSlider" style="transition-delay: 0.15s;">
            <div class="slider-track">
                @forelse($posts as $post)
                    <div class="slider-item">
                        <div class="card-post" style="height: 100%; margin: 0;">
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
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <line x1="5" y1="12" x2="19" y2="12"></line>
                                            <polyline points="12 5 19 12 12 19"></polyline>
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <p style="text-align: center; width: 100%; color: var(--text-secondary);">Belum ada berita atau artikel diterbitkan.</p>
                @endforelse
            </div>
            @if($posts->count() > 0)
                <div class="slider-dots"></div>
            @endif
        </div>

        @if($posts->count() > 0)
            <div class="reveal-on-scroll" style="text-align: center; margin-top: 3.5rem; transition-delay: 0.25s;">
                <a href="{{ route('news') }}" class="btn btn-secondary">Kunjungi Blog Artikel</a>
            </div>
        @endif
    </section>

    <!-- Software Highlights Section (SOFTWARE) -->
    <section class="section-container section-software-accent" style="border-top: 1px solid rgba(2, 132, 199, 0.08); background: linear-gradient(180deg, rgba(2, 132, 199, 0.01) 0%, rgba(2, 132, 199, 0.03) 100%);">
        <div class="section-title-wrap reveal-on-scroll">
            <span class="section-subtitle">Freeware &amp; Open Source</span>
            <h2 class="section-title">Software Gratis <span>Terbaru</span></h2>
        </div>

        <div class="slider-container reveal-on-scroll" id="softwareSlider" style="transition-delay: 0.15s;">
            <div class="slider-track">
                @forelse($softwares as $software)
                    <div class="slider-item">
                        <div class="software-card" style="height: 100%; margin: 0; background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 1.5rem; display: flex; flex-direction: column; justify-content: space-between; box-shadow: var(--shadow-sm); transition: all 0.3s ease; position: relative;">
                            
                            @if($software->is_featured)
                                <div style="position: absolute; top: 12px; right: 12px; background: rgba(245, 158, 11, 0.12); color: var(--accent-amber); border: 1px solid rgba(245, 158, 11, 0.3); font-size: 0.65rem; font-weight: 800; padding: 2px 8px; border-radius: 100px; letter-spacing: 0.5px;">
                                    POPULER
                                </div>
                            @endif

                            <div>
                                <div style="display: flex; gap: 1rem; align-items: flex-start; margin-bottom: 1rem;">
                                    <a href="{{ route('software.show', $software->slug) }}" style="flex-shrink: 0;">
                                        <div style="width: 54px; height: 54px; border-radius: 12px; border: 1px solid var(--border-color); background: #ffffff; display: flex; align-items: center; justify-content: center; overflow: hidden; padding: 6px; box-shadow: var(--shadow-sm);">
                                            <img src="{{ $software->icon_url }}" alt="{{ $software->title }}" style="width: 100%; height: 100%; object-fit: contain;">
                                        </div>
                                    </a>

                                    <div style="flex: 1; min-width: 0;">
                                        <h3 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 0.2rem; line-height: 1.3;">
                                            <a href="{{ route('software.show', $software->slug) }}" style="color: var(--text-primary); text-decoration: none; transition: color 0.2s;" onmouseenter="this.style.color='var(--accent-sky)'" onmouseleave="this.style.color='var(--text-primary)'">
                                                {{ $software->title }}
                                            </a>
                                        </h3>
                                        <div style="font-size: 0.775rem; color: var(--text-muted); margin-bottom: 0.4rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                            {{ $software->developer ?? 'Komunitas Open Source' }}
                                        </div>
                                        <div style="display: flex; flex-wrap: wrap; gap: 0.35rem; align-items: center;">
                                            <span style="background: rgba(15, 23, 42, 0.06); color: var(--text-primary); font-size: 0.7rem; font-weight: 700; padding: 2px 7px; border-radius: 4px;">
                                                v{{ $software->version }}
                                            </span>
                                            <span style="background: rgba(148, 163, 184, 0.12); color: var(--text-secondary); font-size: 0.7rem; font-weight: 600; padding: 2px 7px; border-radius: 4px;">
                                                {{ $software->license }}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <p style="font-size: 0.85rem; color: var(--text-secondary); line-height: 1.5; margin-bottom: 1.25rem;">
                                    {{ Str::limit($software->description, 100) }}
                                </p>
                            </div>

                            <div style="border-top: 1px solid var(--border-color); padding-top: 1rem; margin-top: auto;">
                                <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.75rem; color: var(--text-muted); margin-bottom: 0.75rem;">
                                    <span title="Kompatibilitas">💻 {{ Str::limit($software->os_compatibility, 20) }}</span>
                                    <span title="Ukuran File">📦 {{ $software->file_size ?? 'Gratis' }}</span>
                                </div>

                                <div style="display: flex; gap: 0.5rem;">
                                    <a href="{{ route('software.show', $software->slug) }}" class="btn btn-secondary btn-sm" style="flex: 1; text-align: center; text-decoration: none; padding: 0.55rem 0.4rem; font-size: 0.8rem;">
                                        Detail
                                    </a>
                                    <a href="{{ route('software.download', $software->slug) }}" class="btn btn-primary btn-sm" style="flex: 1.2; text-align: center; text-decoration: none; padding: 0.55rem 0.4rem; font-size: 0.8rem; display: flex; align-items: center; justify-content: center; gap: 0.25rem;">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 13px; height: 13px;"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                                        Download
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <p style="text-align: center; width: 100%; color: var(--text-secondary);">Belum ada software ditambahkan.</p>
                @endforelse
            </div>
            @if(isset($softwares) && $softwares->count() > 0)
                <div class="slider-dots"></div>
            @endif
        </div>

        @if(isset($softwares) && $softwares->count() > 0)
            <div class="reveal-on-scroll" style="text-align: center; margin-top: 3.5rem; transition-delay: 0.25s;">
                <a href="{{ route('software.index') }}" class="btn btn-secondary">Katalog Software Lengkap</a>
            </div>
        @endif
    </section>

    <!-- Product Highlights Section (SHOP) -->
    <section class="section-container section-products-accent">
        <div class="section-title-wrap reveal-on-scroll">
            <span class="section-subtitle">Katalog Toko</span>
            <h2 class="section-title">Produk <span>Digital</span></h2>
        </div>

        <div class="slider-container reveal-on-scroll" id="shopSlider" style="transition-delay: 0.15s;">
            <div class="slider-track">
                @forelse($products as $product)
                    <div class="slider-item">
                        <a href="{{ route('shop.show', ['id' => $product->id, 'slug' => Str::slug($product->name)]) }}" class="card-product" style="text-decoration: none; height: 100%; display: flex; flex-direction: column; margin: 0;">
                            <div class="product-media">
                                @if($product->image)
                                    <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}">
                                @else
                                    <div class="product-media-placeholder">📦</div>
                                @endif
                            </div>
                            <div class="product-body" style="flex-grow: 1; display: flex; flex-direction: column; justify-content: space-between;">
                                <div>
                                    <h3 class="product-name">{{ $product->name }}</h3>
                                    <p class="product-desc">{{ Str::limit($product->description, 120) }}</p>
                                </div>
                                <div class="product-footer" style="margin-top: 1rem;">
                                    <span class="product-price">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                                    <span class="btn btn-primary btn-sm">
                                        Lihat Detail
                                    </span>
                                </div>
                            </div>
                        </a>
                    </div>
                @empty
                    <p style="text-align: center; width: 100%; color: var(--text-secondary);">Belum ada produk yang dijual saat ini.</p>
                @endforelse
            </div>
            @if($products->count() > 0)
                <div class="slider-dots"></div>
            @endif
        </div>

        @if($products->count() > 0)
            <div class="reveal-on-scroll" style="text-align: center; margin-top: 3.5rem; transition-delay: 0.25s;">
                <a href="{{ route('shop') }}" class="btn btn-secondary">Kunjungi Toko Lengkap</a>
            </div>
        @endif
    </section>

    <!-- Hero / Company Profile Banner Section -->
    <section class="hero-section" style="border-top: 1px solid var(--border-color); border-bottom: 1px solid var(--border-color);">
        <!-- Right side full image background with gradient blending -->
        <div class="hero-bg-visual">
            <img src="{{ asset('logo/gambar 01.jpg') }}?v={{ file_exists(public_path('logo/gambar 01.jpg')) ? filemtime(public_path('logo/gambar 01.jpg')) : time() }}" alt="Arhana Computer">
            <div class="hero-bg-overlay"></div>
        </div>

        <div class="hero-wrap" style="position: relative; z-index: 2; width: 100%;">
            <div class="hero-content" style="z-index: 3;">
                <div class="hero-badge">
                    <span class="hero-badge-dot"></span>
                    Tersedia untuk Kolaborasi Proyek
                </div>
                <h1 class="hero-title">
                    ARHANA COMPUTER<br>
                    <span>Your IT solutions</span>
                </h1>
                <p class="hero-description"
                    style="color: var(--text-primary); font-weight: 500; text-shadow: 0 1px 2px rgba(255, 255, 255, 0.9);">
                    Arhana Computer menyediakan kebutuhan teknologi untuk personal, sekolah, kantor, dan bisnis Anda. Mulai dari
                    pembuatan Website, Aplikasi Mobile, Jasa Setting Jaringan, Instalasi Software, Service Komputer, Laptop
                    & Printer. <br>
                    Untuk contoh project yang pernah dikerjakan bisa di lihat di menu <a
                        href="{{ route('portfolio') }}">PORTFOLIO</a>.<br>
                    Kami juga menyediakan link produk IT dari toko-toko terpercaya di menu <a
                        href="{{ route('shop') }}">SHOP</a>.<br>
                    Kamu juga bisa menemukan Berita atau artikel menarik di menu <a href="{{ route('news') }}">NEWS</a>.
                </p>
                <div class="hero-actions">
                    <a href="{{ route('portfolio') }}" class="btn btn-primary">Lihat Portofolio</a>
                    <a href="{{ route('shop') }}" class="btn btn-secondary">Kunjungi Toko</a>
                </div>
            </div>

            <!-- Placeholder to maintain grid column allocation -->
            <div style="pointer-events: none;"></div>
        </div>
    </section>

    <!-- Portfolio Highlights Section (PORTFOLIO) -->
    <section class="section-container section-portfolio-accent">
        <div class="section-title-wrap reveal-on-scroll">
            <span class="section-subtitle">Karya Pilihan</span>
            <h2 class="section-title">Portofolio <span>Terbaru</span></h2>
        </div>

        <div class="slider-container reveal-on-scroll" id="portfolioSlider" style="transition-delay: 0.15s;">
            <div class="slider-track">
                @forelse($projects as $project)
                    <div class="slider-item">
                        <div class="card-project" style="height: 100%; margin: 0; display: flex; flex-direction: column; justify-content: space-between;">
                            <div>
                                <div class="project-cover">
                                    @if($project->image)
                                        <img src="{{ asset('storage/' . $project->image) }}" alt="{{ $project->title }}">
                                    @else
                                        <div class="project-cover-placeholder">💻</div>
                                    @endif
                                </div>
                                <div class="project-body">
                                    <h3 class="project-title" style="margin-top: 0;">{{ $project->title }}</h3>
                                    <p class="project-desc">{{ Str::limit($project->description, 130) }}</p>

                                    <div class="project-tags" style="margin-bottom: 0;">
                                        @foreach(explode(',', $project->technologies) as $tech)
                                            <span class="tag-tech">{{ trim($tech) }}</span>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            @if($project->project_link)
                                <div class="project-body" style="padding-top: 0; margin-top: auto;">
                                    <div class="project-footer" style="padding-top: 1rem; border-top: 1px solid var(--border-color); margin-top: 0;">
                                        <a href="{{ route('projects.visit', $project->id) }}" target="_blank" rel="noopener noreferrer"
                                            class="project-link-icon">
                                            Buka Aplikasi
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path>
                                                <polyline points="15 3 21 3 21 9"></polyline>
                                                <line x1="10" y1="14" x2="21" y2="3"></line>
                                            </svg>
                                        </a>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <p style="text-align: center; width: 100%; color: var(--text-secondary);">Belum ada portofolio yang dipublikasikan.</p>
                @endforelse
            </div>
            @if($projects->count() > 0)
                <div class="slider-dots"></div>
            @endif
        </div>

        @if($projects->count() > 0)
            <div class="reveal-on-scroll" style="text-align: center; margin-top: 3.5rem; transition-delay: 0.25s;">
                <a href="{{ route('portfolio') }}" class="btn btn-secondary">Lihat Semua Proyek</a>
            </div>
        @endif
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // 1. Scroll Reveal Observer
            const reveals = document.querySelectorAll('.reveal-on-scroll');

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('revealed');
                        observer.unobserve(entry.target);
                    }
                });
            }, {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            });

            reveals.forEach(el => observer.observe(el));

            // 2. Premium Responsive Carousel Slider Initializer
            function initSlider(containerId) {
                const container = document.getElementById(containerId);
                if (!container) return;
                
                const track = container.querySelector('.slider-track');
                const items = container.querySelectorAll('.slider-item');
                const dotContainer = container.querySelector('.slider-dots');
                
                const totalItems = items.length;
                if (totalItems <= 0) return;
                
                let currentPage = 0;
                let itemsPerPage = 3;
                let timer = null;
                
                function getItemsPerPage() {
                    if (window.innerWidth <= 576) return 1;
                    if (window.innerWidth <= 991) return 2;
                    return 3;
                }
                
                function getGap() {
                    if (window.innerWidth <= 991) return 20; // 1.25rem = 20px
                    return 28; // 1.75rem = 28px
                }
                
                function updateSlider() {
                    itemsPerPage = getItemsPerPage();
                    const maxPages = Math.ceil(totalItems / itemsPerPage);
                    
                    if (currentPage >= maxPages) {
                        currentPage = 0;
                    }
                    
                    // Generate Dots
                    if (dotContainer) {
                        dotContainer.innerHTML = '';
                        // Only show dots if there's more than 1 page
                        if (maxPages > 1) {
                            for (let i = 0; i < maxPages; i++) {
                                const dot = document.createElement('span');
                                dot.className = 'slider-dot' + (i === currentPage ? ' active' : '');
                                dot.addEventListener('click', () => {
                                    currentPage = i;
                                    updateSlider();
                                    resetTimer();
                                });
                                dotContainer.appendChild(dot);
                            }
                        }
                    }
                    
                    // Calculate slide width and move
                    const gap = getGap();
                    const itemWidth = (container.offsetWidth - (gap * (itemsPerPage - 1))) / itemsPerPage;
                    
                    // Set items width dynamically to prevent breaking grid logic
                    items.forEach(item => {
                        item.style.flex = `0 0 ${itemWidth}px`;
                    });
                    
                    const slideWidth = (itemWidth + gap) * itemsPerPage;
                    track.style.transform = `translateX(-${currentPage * slideWidth}px)`;
                }
                
                function nextSlide() {
                    itemsPerPage = getItemsPerPage();
                    const maxPages = Math.ceil(totalItems / itemsPerPage);
                    if (maxPages > 1) {
                        currentPage = (currentPage + 1) % maxPages;
                        updateSlider();
                    }
                }
                
                function resetTimer() {
                    clearInterval(timer);
                    timer = setInterval(nextSlide, 5000);
                }
                
                // Recalculate on window resize
                window.addEventListener('resize', updateSlider);
                
                // Init
                updateSlider();
                resetTimer();
            }

            // Initialize all sliders in order of page layout
            initSlider('newsSlider');
            initSlider('softwareSlider');
            initSlider('shopSlider');
            initSlider('portfolioSlider');
        });
    </script>
@endsection