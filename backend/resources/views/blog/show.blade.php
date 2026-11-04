@extends('layouts.app')

@section('title', $post->title)

@section('meta_tags')
    <meta name="description" content="{{ Str::limit(strip_tags($post->content), 150) }}">
    <meta property="og:title" content="{{ $post->title }}">
    <meta property="og:description" content="{{ Str::limit(strip_tags($post->content), 150) }}">
    @if($post->image)
        <meta property="og:image" content="{{ asset('storage/' . $post->image) }}">
    @endif
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:type" content="article">
    <link rel="canonical" href="{{ url()->current() }}" />

    <!-- Structured Data (JSON-LD) -->
    <script type="application/ld+json">
    {
      "@@context": "https://schema.org",
      "@@type": "NewsArticle",
      "headline": "{{ $post->title }}",
      "image": "{{ $post->image ? asset('storage/' . $post->image) : asset('logo/logo.png') }}",
      "datePublished": "{{ $post->created_at->toIso8601String() }}",
      "dateModified": "{{ $post->updated_at->toIso8601String() }}",
      "author": [{
          "@@type": "Person",
          "name": "Arhana"
      }]
    }
    </script>
@endsection

@section('content')
    <section class="section-container" style="padding-top: 4rem;">
    <style>
        .product-sidebar {
            position: fixed;
            top: 140px;
            width: 190px;
            z-index: 100;
            display: flex;
            flex-direction: column;
            gap: 1.25rem;
        }
        .left-sidebar {
            left: calc(50% - 450px - 220px);
        }
        .right-sidebar {
            right: calc(50% - 450px - 220px);
        }
        .product-sidebar .sidebar-title {
            font-size: 0.85rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--accent-sky);
            margin: 0 0 0.5rem 0;
            border-bottom: 2px solid rgba(56, 189, 248, 0.2);
            padding-bottom: 0.5rem;
            text-align: center;
        }
        .sidebar-product-card {
            display: flex;
            flex-direction: column;
            background: var(--bg-secondary);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-md);
            overflow: hidden;
            text-decoration: none;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: var(--shadow-sm);
        }
        .sidebar-product-card:hover {
            transform: translateY(-4px);
            border-color: var(--accent-sky);
            box-shadow: 0 10px 20px rgba(56, 189, 248, 0.15);
        }
        .sidebar-product-card .card-img-wrap {
            width: 100%;
            height: 105px;
            overflow: hidden;
            position: relative;
            background: rgba(0,0,0,0.05);
        }
        .sidebar-product-card .card-img-wrap img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }
        .sidebar-product-card:hover .card-img-wrap img {
            transform: scale(1.06);
        }
        .sidebar-product-card .card-placeholder {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
        }
        .sidebar-product-card .card-info {
            padding: 0.6rem;
            display: flex;
            flex-direction: column;
            gap: 0.2rem;
        }
        .sidebar-product-card .prod-name {
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--text-primary);
            line-height: 1.3;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            height: 2.6em;
        }
        .sidebar-product-card .prod-price {
            font-size: 0.85rem;
            font-weight: 700;
            color: var(--accent-amber);
        }
        @media (max-width: 1400px) {
            .left-sidebar {
                left: 20px;
            }
            .right-sidebar {
                right: 20px;
            }
        }
        @media (max-width: 1340px) {
            .product-sidebar {
                display: none !important;
            }
        }

        /* Inline Product Ad inside content */
        .inline-product-ad-wrapper {
            display: none; /* Hide on desktop since we have sidebars */
            margin: 2rem 0;
            padding: 1.25rem;
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid var(--border-color);
            border-left: 4px solid var(--accent-sky);
            border-radius: 0 var(--radius-md) var(--radius-md) 0;
        }

        .inline-ad-badge {
            display: inline-block;
            font-size: 0.65rem;
            font-weight: 800;
            text-transform: uppercase;
            color: var(--accent-sky);
            letter-spacing: 1px;
            margin-bottom: 0.75rem;
        }

        .inline-ad-card {
            display: flex;
            gap: 1.25rem;
            text-decoration: none;
            color: inherit;
        }

        .inline-ad-img {
            width: 100px;
            height: 100px;
            border-radius: var(--radius-sm);
            overflow: hidden;
            flex-shrink: 0;
            background: rgba(0, 0, 0, 0.15);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .inline-ad-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .inline-ad-placeholder {
            font-size: 2rem;
        }

        .inline-ad-info {
            display: flex;
            flex-direction: column;
            flex: 1;
            min-width: 0;
            justify-content: space-between;
        }

        .inline-ad-title {
            font-size: 0.95rem;
            font-weight: 700;
            color: var(--text-primary);
            margin: 0 0 0.25rem 0;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .inline-ad-desc {
            font-size: 0.8rem;
            color: var(--text-secondary);
            margin: 0 0 0.5rem 0;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            line-height: 1.4;
        }

        .inline-ad-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .inline-ad-price {
            font-size: 0.9rem;
            font-weight: 800;
            color: var(--accent-amber);
        }

        .inline-ad-btn {
            font-size: 0.75rem;
            font-weight: 700;
            color: var(--accent-sky);
        }

        /* Bottom Product Ads Styling */
        .mobile-product-ads-wrapper {
            display: none; /* Hidden by default on desktop */
            margin: 2.5rem 0 1.5rem;
            padding: 1.5rem;
            background: linear-gradient(135deg, rgba(30, 41, 59, 0.5) 0%, rgba(15, 23, 42, 0.8) 100%);
            border: 1px solid rgba(56, 189, 248, 0.2);
            border-radius: var(--radius-lg);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            position: relative;
            overflow: hidden;
        }
        
        .mobile-product-ads-wrapper::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, var(--accent-sky), var(--accent-amber));
        }

        .mobile-product-ads-header {
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
            margin-bottom: 1.25rem;
        }

        .mobile-product-ads-header .ads-badge {
            font-size: 0.7rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: var(--accent-sky);
        }

        .mobile-product-ads-header .ads-title {
            font-size: 1.15rem;
            font-weight: 700;
            color: var(--text-primary);
            margin: 0;
        }

        .mobile-product-ads-list {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .mobile-ad-card {
            display: flex;
            align-items: center;
            gap: 1rem;
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-md);
            padding: 0.75rem;
            text-decoration: none;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .mobile-ad-card:hover {
            background: rgba(56, 189, 248, 0.04);
            border-color: var(--accent-sky);
            transform: scale(1.01);
        }

        .mobile-ad-img {
            width: 70px;
            height: 70px;
            border-radius: var(--radius-sm);
            overflow: hidden;
            flex-shrink: 0;
            background: rgba(0,0,0,0.2);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .mobile-ad-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .mobile-ad-placeholder {
            font-size: 1.5rem;
        }

        .mobile-ad-details {
            display: flex;
            flex-direction: column;
            flex: 1;
            min-width: 0;
            gap: 0.2rem;
        }

        .mobile-ad-name {
            font-size: 0.9rem;
            font-weight: 600;
            color: var(--text-primary);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .mobile-ad-price {
            font-size: 0.85rem;
            font-weight: 700;
            color: var(--accent-amber);
        }

        .mobile-ad-action {
            font-size: 0.75rem;
            font-weight: 700;
            color: var(--accent-sky);
            display: inline-flex;
            align-items: center;
            gap: 2px;
            margin-top: 0.25rem;
        }

        @media (max-width: 1340px) {
            .inline-product-ad-wrapper {
                display: block;
            }
            .mobile-product-ads-wrapper {
                display: block;
            }
        }
        
        @media (max-width: 576px) {
            .inline-ad-card {
                flex-direction: column;
                gap: 0.75rem;
            }
            .inline-ad-img {
                width: 100%;
                height: 140px;
            }
            .inline-product-ad-wrapper {
                border-left: none;
                border-top: 4px solid var(--accent-sky);
                border-radius: 0 0 var(--radius-md) var(--radius-md);
            }
        }
    </style>

    @php
        $leftProducts = collect();
        $rightProducts = collect();
        if (isset($randomProducts) && $randomProducts->count() > 0) {
            $count = $randomProducts->count();
            if ($count >= 6) {
                $leftProducts = $randomProducts->slice(0, 3);
                $rightProducts = $randomProducts->slice(3, 3);
            } elseif ($count == 5) {
                $leftProducts = $randomProducts->slice(0, 3);
                $rightProducts = $randomProducts->slice(3, 2);
            } elseif ($count == 4) {
                $leftProducts = $randomProducts->slice(0, 2);
                $rightProducts = $randomProducts->slice(2, 2);
            } elseif ($count == 3) {
                $leftProducts = $randomProducts->slice(0, 2);
                $rightProducts = $randomProducts->slice(2, 1);
            } else {
                $leftProducts = $randomProducts->slice(0, 1);
                $rightProducts = $randomProducts->slice(1, 1);
            }
        }
    @endphp

    @if($leftProducts->count() > 0)
        <!-- Left Product Sidebar Ads -->
        <div class="product-sidebar left-sidebar">
            <h4 class="sidebar-title">Rekomendasi</h4>
            @foreach($leftProducts as $prod)
                <a href="{{ route('shop.show', ['id' => $prod->id, 'slug' => Str::slug($prod->name)]) }}" class="sidebar-product-card">
                    <div class="card-img-wrap">
                        @if($prod->image)
                            <img src="{{ asset('storage/' . $prod->image) }}" alt="{{ $prod->name }}">
                        @else
                            <div class="card-placeholder">📦</div>
                        @endif
                    </div>
                    <div class="card-info">
                        <span class="prod-name">{{ $prod->name }}</span>
                        <span class="prod-price">Rp {{ number_format($prod->price, 0, ',', '.') }}</span>
                    </div>
                </a>
            @endforeach
        </div>
    @endif

    @if($rightProducts->count() > 0)
        <!-- Right Product Sidebar Ads -->
        <div class="product-sidebar right-sidebar">
            <h4 class="sidebar-title">Rekomendasi</h4>
            @foreach($rightProducts as $prod)
                <a href="{{ route('shop.show', ['id' => $prod->id, 'slug' => Str::slug($prod->name)]) }}" class="sidebar-product-card">
                    <div class="card-img-wrap">
                        @if($prod->image)
                            <img src="{{ asset('storage/' . $prod->image) }}" alt="{{ $prod->name }}">
                        @else
                            <div class="card-placeholder">📦</div>
                        @endif
                    </div>
                    <div class="card-info">
                        <span class="prod-name">{{ $prod->name }}</span>
                        <span class="prod-price">Rp {{ number_format($prod->price, 0, ',', '.') }}</span>
                    </div>
                </a>
            @endforeach
        </div>
    @endif

        <div style="max-width: 800px; margin: 0 auto 2rem;">
            <a href="{{ route('news') }}" style="font-size: 0.85rem; color: var(--accent-sky); font-weight: 600; display: inline-flex; align-items: center; gap: 0.25rem; margin-bottom: 2rem;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
                Kembali ke Berita
            </a>
        </div>

        <article class="post-detail">
            <!-- Article Header -->
            <div class="post-detail-header">
                <div class="post-detail-meta" style="display: flex; flex-wrap: wrap; gap: 6px; align-items: center;">
                    @foreach($post->categories as $cat)
                        <span class="post-category" style="background-color: rgba(56, 189, 248, 0.08); border: 1px solid rgba(56, 189, 248, 0.15); color: var(--accent-sky); border-radius: 100px; padding: 0.15rem 0.6rem; font-size: 0.75rem; font-weight: 600; margin: 0;">
                            {{ $cat->name }}
                        </span>
                    @endforeach
                    <span>&bull;</span>
                    <span>{{ $post->created_at->format('d F Y') }}</span>
                </div>
                <h1 class="post-detail-title">{{ $post->title }}</h1>
            </div>

            <!-- Cover Image -->
            @if($post->image)
                <div class="post-detail-media">
                    <img src="{{ asset('storage/' . $post->image) }}" alt="{{ $post->title }}" style="width: 100%; height: auto; max-height: 480px; object-fit: cover;">
                </div>
            @endif

            <!-- Article Content -->
            <div class="post-detail-body">
                @php
                    $paragraphs = explode('</p>', $post->content);
                    $totalParagraphs = count($paragraphs);
                    $inserted = false;
                @endphp

                @foreach($paragraphs as $index => $paragraph)
                    @if(trim($paragraph) !== '')
                        {!! $paragraph !!}</p>
                    @endif

                    {{-- Insert inline product ad after 2nd paragraph on mobile --}}
                    @if($index === 1 && $totalParagraphs > 2 && isset($randomProducts) && $randomProducts->count() > 0)
                        @php
                            $inlineProd = $randomProducts->first();
                            $inserted = true;
                        @endphp
                        <div class="inline-product-ad-wrapper">
                            <span class="inline-ad-badge">Rekomendasi Produk</span>
                            <a href="{{ route('shop.show', ['id' => $inlineProd->id, 'slug' => Str::slug($inlineProd->name)]) }}" class="inline-ad-card">
                                <div class="inline-ad-img">
                                    @if($inlineProd->image)
                                        <img src="{{ asset('storage/' . $inlineProd->image) }}" alt="{{ $inlineProd->name }}">
                                    @else
                                        <div class="inline-ad-placeholder">📦</div>
                                    @endif
                                </div>
                                <div class="inline-ad-info">
                                    <h5 class="inline-ad-title">{{ $inlineProd->name }}</h5>
                                    <p class="inline-ad-desc">{{ Str::limit($inlineProd->description, 80) }}</p>
                                    <div class="inline-ad-footer">
                                        <span class="inline-ad-price">Rp {{ number_format($inlineProd->price, 0, ',', '.') }}</span>
                                        <span class="inline-ad-btn">Lihat Detail ↗</span>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endif
                @endforeach

                {{-- If the post is very short and the inline ad was not inserted, or we want a bottom ad --}}
                @if(isset($randomProducts) && $randomProducts->count() > 0)
                    <!-- Mobile Product Ads Section at the end -->
                    <div class="mobile-product-ads-wrapper">
                        <div class="mobile-product-ads-header">
                            <span class="ads-badge">Rekomendasi Untuk Anda</span>
                            <h4 class="ads-title">Produk Pilihan</h4>
                        </div>
                        <div class="mobile-product-ads-list">
                            @php
                                $displayProducts = $inserted ? $randomProducts->slice(1, 3) : $randomProducts->take(3);
                            @endphp
                            @foreach($displayProducts as $prod)
                                <a href="{{ route('shop.show', ['id' => $prod->id, 'slug' => Str::slug($prod->name)]) }}" class="mobile-ad-card">
                                    <div class="mobile-ad-img">
                                        @if($prod->image)
                                            <img src="{{ asset('storage/' . $prod->image) }}" alt="{{ $prod->name }}">
                                        @else
                                            <div class="mobile-ad-placeholder">📦</div>
                                        @endif
                                    </div>
                                    <div class="mobile-ad-details">
                                        <span class="mobile-ad-name">{{ $prod->name }}</span>
                                        <span class="mobile-ad-price">Rp {{ number_format($prod->price, 0, ',', '.') }}</span>
                                        <span class="mobile-ad-action">Beli Sekarang ↗</span>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <!-- Share Buttons -->
            <div class="post-share-section" style="margin-top: 3.5rem; padding-top: 2rem; border-top: 1px solid var(--border-color); display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;">
                <span style="font-size: 0.9rem; font-weight: 600; color: var(--text-secondary); margin-right: 0.5rem;">Bagikan:</span>
                
                <!-- WhatsApp -->
                <a href="https://api.whatsapp.com/send?text={{ urlencode($post->title . ' - ' . url()->current()) }}" target="_blank" rel="noopener noreferrer" title="Bagikan ke WhatsApp" style="display: inline-flex; align-items: center; justify-content: center; width: 38px; height: 38px; border-radius: 50%; background-color: #25D366; color: white; transition: transform 0.2s;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path></svg>
                </a>

                <!-- Facebook -->
                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}" target="_blank" rel="noopener noreferrer" title="Bagikan ke Facebook" style="display: inline-flex; align-items: center; justify-content: center; width: 38px; height: 38px; border-radius: 50%; background-color: #1877F2; color: white; transition: transform 0.2s;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path></svg>
                </a>

                <!-- X / Twitter -->
                <a href="https://twitter.com/intent/tweet?text={{ urlencode($post->title) }}&url={{ urlencode(url()->current()) }}" target="_blank" rel="noopener noreferrer" title="Bagikan ke X" style="display: inline-flex; align-items: center; justify-content: center; width: 38px; height: 38px; border-radius: 50%; background-color: #000000; color: white; transition: transform 0.2s;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4l11.733 16h4.267l-11.733 -16z"></path><path d="M4 20l6.768 -6.768m2.46 -2.46l6.772 -6.772"></path></svg>
                </a>

                <!-- Instagram (Copy Link) -->
                <button type="button" onclick="copyToClipboard('{{ url()->current() }}')" title="Salin Link (Instagram tidak mendukung share URL langsung)" style="border:none; cursor:pointer; display: inline-flex; align-items: center; justify-content: center; width: 38px; height: 38px; border-radius: 50%; background: linear-gradient(45deg, #f09433 0%, #e6683c 25%, #dc2743 50%, #cc2366 75%, #bc1888 100%); color: white; transition: transform 0.2s;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line></svg>
                </button>

                <!-- TikTok (Copy Link) -->
                <button type="button" onclick="copyToClipboard('{{ url()->current() }}')" title="Salin Link (TikTok tidak mendukung share URL langsung)" style="border:none; cursor:pointer; display: inline-flex; align-items: center; justify-content: center; width: 38px; height: 38px; border-radius: 50%; background-color: #25F4EE; color: #000000; transition: transform 0.2s;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 12a4 4 0 1 0 4 4V4a5 5 0 0 0 5 5"></path></svg>
                </button>
                
                <span id="copyFeedback" style="display: none; font-size: 0.8rem; color: #10b981; font-weight: 600; margin-left: 0.5rem; background-color: rgba(16,185,129,0.1); padding: 0.3rem 0.6rem; border-radius: 4px;">Link Disalin!</span>
            </div>

            <script>
                function copyToClipboard(text) {
                    navigator.clipboard.writeText(text).then(function() {
                        const feedback = document.getElementById('copyFeedback');
                        feedback.style.display = 'inline-block';
                        setTimeout(() => {
                            feedback.style.display = 'none';
                        }, 2500);
                    }).catch(function(err) {
                        console.error('Could not copy text: ', err);
                    });
                }
            </script>
        </article>

        <!-- Premium Comments System Widget -->
        <div class="comments-section" style="max-width: 800px; margin: 4rem auto 0; padding: 3rem; background: var(--bg-secondary); border: 1px solid var(--border-color); border-radius: var(--radius-lg); box-shadow: var(--shadow-sm);">
            <h3 style="font-size: 1.35rem; font-weight: 700; color: var(--text-primary); margin-bottom: 2rem; display: flex; align-items: center; gap: 0.5rem;">
                <span>💬</span> Komentar Pengunjung ({{ $post->comments()->where('is_approved', true)->count() }})
            </h3>

            <!-- Alert Success Feedback -->
            @if(session('success') && str_contains(session('success'), 'Komentar'))
                <div style="background-color: rgba(16, 185, 129, 0.08); border: 1px solid rgba(16, 185, 129, 0.2); color: #10b981; padding: 1rem 1.25rem; border-radius: var(--radius-sm); font-size: 0.9rem; font-weight: 600; margin-bottom: 2rem; display: flex; align-items: center; gap: 0.5rem;">
                    <span>✓</span> {{ session('success') }}
                </div>
            @endif

            <!-- Comment Input Box Form -->
            <form action="{{ route('comments.store', $post->id) }}" method="POST" style="margin-bottom: 3rem; border-bottom: 1px solid var(--border-color); padding-bottom: 2.5rem;">
                @csrf
                <div style="display: grid; grid-template-columns: 1fr; gap: 1.25rem; margin-bottom: 1.25rem;">
                    <!-- Auto Display Username or Input Field -->
                    <div id="usernameInputArea">
                        <label for="commentUsername" style="display: block; font-size: 0.85rem; font-weight: 600; color: var(--text-secondary); margin-bottom: 0.5rem;">Nama Pengguna (Username)</label>
                        <input type="text" name="username" id="commentUsername" placeholder="Masukkan username Anda..." style="width: 100%; padding: 0.75rem 1rem; border: 1px solid var(--border-color); border-radius: var(--radius-sm); background: var(--bg-secondary); color: var(--text-primary); font-size: 0.9rem; transition: all 0.25s;" onfocus="this.style.borderColor='var(--accent-sky)'; this.style.boxShadow='0 0 10px rgba(56, 189, 248, 0.15)'" onblur="this.style.borderColor='var(--border-color)'; this.style.boxShadow='none'">
                        <span style="font-size: 0.75rem; color: var(--text-muted); display: block; margin-top: 0.35rem;">Username disimpan di browser untuk memudahkan Anda berkomentar kembali.</span>
                    </div>

                    <div id="usernameGreetingArea" style="display: none; background: rgba(56, 189, 248, 0.04); border: 1px solid rgba(56, 189, 248, 0.12); padding: 0.85rem 1.25rem; border-radius: var(--radius-sm); align-items: center; justify-content: space-between; gap: 1rem;">
                        <span style="font-size: 0.9rem; color: var(--text-secondary);">
                            Berkomentar sebagai: <strong id="activeUsernameDisplay" style="color: var(--accent-sky);"></strong>
                        </span>
                        <button type="button" onclick="changeCommenterUsername()" style="background: none; border: none; padding: 0; color: var(--accent-amber); font-weight: 700; font-size: 0.8rem; cursor: pointer; text-decoration: underline;">
                            Ubah Username
                        </button>
                    </div>

                    <div>
                        <label for="commentContent" style="display: block; font-size: 0.85rem; font-weight: 600; color: var(--text-secondary); margin-bottom: 0.5rem;">Isi Komentar</label>
                        <textarea name="content" id="commentContent" rows="4" placeholder="Ketik komentar Anda di sini secara bijak..." style="width: 100%; padding: 0.75rem 1rem; border: 1px solid var(--border-color); border-radius: var(--radius-sm); background: var(--bg-secondary); color: var(--text-primary); font-size: 0.9rem; line-height: 1.5; resize: vertical; transition: all 0.25s;" onfocus="this.style.borderColor='var(--accent-sky)'; this.style.boxShadow='0 0 10px rgba(56, 189, 248, 0.15)'" onblur="this.style.borderColor='var(--border-color)'; this.style.boxShadow='none'" required></textarea>
                    </div>
                </div>

                <div style="display: flex; justify-content: flex-end;">
                    <button type="submit" class="btn btn-primary" style="padding: 0.7rem 2rem; font-weight: 700;">Kirim Komentar</button>
                </div>
            </form>

            <!-- Recursive Comments Loop Renderer -->
            <div class="comments-list" style="display: flex; flex-direction: column; gap: 2rem;">
                @php
                    // Helper function to render a comment and its children recursively
                    if (!function_exists('renderCommentCard')) {
                        function renderCommentCard($c, $__env) {
                            $isAdmin = !empty($c->user_id);
                            @endphp
                            <div class="comment-card" id="comment-{{ $c->id }}" style="display: flex; gap: 1rem; margin-bottom: 1.5rem; transition: transform 0.2s;">
                                <!-- Avatar icon -->
                                <div style="width: 42px; height: 42px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.25rem; font-weight: 800; flex-shrink: 0; background: {{ $isAdmin ? 'linear-gradient(135deg, var(--accent-sky), #0284c7)' : 'rgba(255,255,255,0.03)' }}; border: 1px solid {{ $isAdmin ? 'var(--accent-sky)' : 'var(--border-color)' }}; color: {{ $isAdmin ? '#000' : 'var(--text-secondary)' }};">
                                    {{ strtoupper(substr($c->username, 0, 1)) }}
                                </div>

                                <div style="flex: 1;">
                                    <!-- Comment Header -->
                                    <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap; margin-bottom: 0.35rem;">
                                        <strong style="font-size: 0.95rem; color: var(--text-primary);">{{ $c->username }}</strong>
                                        @if($isAdmin)
                                            <span style="font-size: 0.65rem; background-color: rgba(56, 189, 248, 0.15); color: var(--accent-sky); padding: 1px 6px; border-radius: 4px; font-weight: 800; border: 1px solid rgba(56, 189, 248, 0.2); text-transform: uppercase;">Admin</span>
                                        @endif
                                        <span style="font-size: 0.75rem; color: var(--text-muted);">&bull;</span>
                                        <span style="font-size: 0.75rem; color: var(--text-muted);">{{ $c->created_at->diffForHumans() }}</span>
                                    </div>

                                    <!-- Comment Content Text -->
                                    <p style="font-size: 0.95rem; color: var(--text-secondary); line-height: 1.6; margin-bottom: 0.5rem; white-space: pre-wrap;">{{ $c->content }}</p>

                                    <!-- Reply Button Trigger -->
                                    <button type="button" onclick="showReplyBox({{ $c->id }})" style="background: none; border: none; padding: 0; color: var(--accent-sky); font-weight: 700; font-size: 0.8rem; cursor: pointer; display: inline-flex; align-items: center; gap: 0.25rem;">
                                        <span>Balas</span>
                                    </button>

                                    <!-- Nested Reply Form Box -->
                                    <div id="reply-box-{{ $c->id }}" style="display: none; margin-top: 1rem; padding: 1rem; border-radius: var(--radius-sm); border: 1px solid var(--border-color); background-color: rgba(255,255,255,0.01);">
                                        <form action="{{ route('comments.store', $c->post_id) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="parent_id" value="{{ $c->id }}">
                                            
                                            <div style="display: grid; grid-template-columns: 1fr; gap: 1rem; margin-bottom: 1rem;">
                                                <div class="reply-username-wrapper">
                                                    <label style="display: block; font-size: 0.8rem; font-weight: 600; color: var(--text-secondary); margin-bottom: 0.35rem;">Nama Pengguna (Username)</label>
                                                    <input type="text" name="username" class="reply-username-field" placeholder="Masukkan username Anda..." style="width: 100%; padding: 0.6rem 0.85rem; border: 1px solid var(--border-color); border-radius: var(--radius-sm); background: var(--bg-secondary); color: var(--text-primary); font-size: 0.85rem;">
                                                </div>
                                                <div class="reply-greeting-wrapper" style="display: none; font-size: 0.85rem; color: var(--text-secondary);">
                                                    Membalas sebagai: <strong class="reply-active-username" style="color: var(--accent-sky);"></strong>
                                                </div>
                                                <div>
                                                    <label style="display: block; font-size: 0.8rem; font-weight: 600; color: var(--text-secondary); margin-bottom: 0.35rem;">Balasan Anda</label>
                                                    <textarea name="content" rows="3" placeholder="Ketik balasan Anda..." style="width: 100%; padding: 0.6rem 0.85rem; border: 1px solid var(--border-color); border-radius: var(--radius-sm); background: var(--bg-secondary); color: var(--text-primary); font-size: 0.85rem; line-height: 1.5; resize: vertical;" required></textarea>
                                                </div>
                                            </div>

                                            <div style="display: flex; gap: 0.5rem; justify-content: flex-end;">
                                                <button type="button" class="btn btn-secondary btn-sm" onclick="showReplyBox({{ $c->id }})">Batal</button>
                                                <button type="submit" class="btn btn-primary btn-sm">Balas Komentar</button>
                                            </div>
                                        </form>
                                    </div>

                                    <!-- Render Replies recursively -->
                                    @if($c->replies && $c->replies->count() > 0)
                                        <div class="nested-replies" style="margin-top: 1.5rem; padding-left: 1.5rem; border-left: 2px dashed var(--border-color);">
                                            @foreach($c->replies as $reply)
                                                @if($reply->is_approved)
                                                    {!! renderCommentCard($reply, $__env) !!}
                                                @endif
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                            @php
                        }
                    }
                @endphp

                @forelse($post->comments()->where('is_approved', true)->get() as $topComment)
                    {!! renderCommentCard($topComment, $__env) !!}
                @empty
                    <div style="text-align: center; padding: 3rem 1rem; color: var(--text-muted); font-size: 0.95rem;">
                        Belum ada komentar disetujui. Jadilah yang pertama memberikan tanggapan!
                    </div>
                @endforelse
            </div>
        </div>

        <script>
            // Retrieve username from cookie/localStorage and set it automatically
            document.addEventListener('DOMContentLoaded', function () {
                checkUsernameCookie();
            });

            function checkUsernameCookie() {
                // Try reading from localStorage first (100% reliable client-side), then fallback to cookie
                let username = localStorage.getItem('arhana_comment_username');
                if (!username || username.trim() === '') {
                    username = getCookieValue('arhana_comment_username');
                }
                
                const inputArea = document.getElementById('usernameInputArea');
                const greetingArea = document.getElementById('usernameGreetingArea');
                const displaySpan = document.getElementById('activeUsernameDisplay');
                const mainInput = document.getElementById('commentUsername');
                
                if (username && username.trim() !== '') {
                    // Save to localStorage just in case it was only in cookie
                    localStorage.setItem('arhana_comment_username', username);

                    // Hide input field and show greeting
                    if (inputArea) inputArea.style.display = 'none';
                    if (greetingArea) greetingArea.style.display = 'flex';
                    if (displaySpan) displaySpan.textContent = username;
                    if (mainInput) mainInput.value = username;

                    // Apply for all nested reply boxes too
                    const replyInputs = document.querySelectorAll('.reply-username-field');
                    replyInputs.forEach(inp => {
                        inp.value = username;
                    });
                    
                    const replyWrappers = document.querySelectorAll('.reply-username-wrapper');
                    replyWrappers.forEach(w => w.style.display = 'none');

                    const replyGreetings = document.querySelectorAll('.reply-greeting-wrapper');
                    replyGreetings.forEach(g => {
                        g.style.display = 'block';
                        g.querySelector('.reply-active-username').textContent = username;
                    });
                } else {
                    // Show input field and hide greeting
                    if (inputArea) inputArea.style.display = 'block';
                    if (greetingArea) greetingArea.style.display = 'none';
                    if (mainInput) mainInput.value = '';

                    const replyWrappers = document.querySelectorAll('.reply-username-wrapper');
                    replyWrappers.forEach(w => w.style.display = 'block');

                    const replyGreetings = document.querySelectorAll('.reply-greeting-wrapper');
                    replyGreetings.forEach(g => g.style.display = 'none');
                }
            }

            function changeCommenterUsername() {
                // Clear the cookie at frontend
                document.cookie = "arhana_comment_username=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
                // Clear localStorage
                localStorage.removeItem('arhana_comment_username');
                
                // Refresh layout
                checkUsernameCookie();
                
                // Focus main input
                const mainInput = document.getElementById('commentUsername');
                if (mainInput) mainInput.focus();
            }

            function getCookieValue(name) {
                const value = `; ${document.cookie}`;
                const parts = value.split(`; ${name}=`);
                if (parts.length === 2) return decodeURIComponent(parts.pop().split(';').shift());
                return null;
            }

            function showReplyBox(id) {
                const box = document.getElementById(`reply-box-${id}`);
                if (box.style.display === 'none') {
                    // Close other reply boxes
                    document.querySelectorAll('[id^="reply-box-"]').forEach(el => el.style.display = 'none');

                    box.style.display = 'block';
                    
                    // Fill username inside nested reply too
                    const username = localStorage.getItem('arhana_comment_username') || getCookieValue('arhana_comment_username');
                    if (username) {
                        box.querySelector('.reply-username-field').value = username;
                    }
                } else {
                    box.style.display = 'none';
                }
            }

            // Save input username directly to LocalStorage on form submission
            document.addEventListener('DOMContentLoaded', function () {
                const commentForm = document.querySelector('form[action*="/comments"]');
                if (commentForm) {
                    commentForm.addEventListener('submit', function () {
                        const usernameInput = document.getElementById('commentUsername');
                        if (usernameInput && usernameInput.value.trim() !== '') {
                            localStorage.setItem('arhana_comment_username', usernameInput.value.trim());
                        }
                    });
                }

                // Apply dynamic saving for all nested reply forms
                document.querySelectorAll('form[action*="/comments"]').forEach(form => {
                    form.addEventListener('submit', function() {
                        const replyInput = form.querySelector('.reply-username-field');
                        if (replyInput && replyInput.value.trim() !== '') {
                            localStorage.setItem('arhana_comment_username', replyInput.value.trim());
                        }
                    });
                });
            });
        </script>
    </section>

    <!-- Related Articles Section -->
    @if($relatedPosts->count() > 0)
        <section class="section-container" style="border-top: 1px solid var(--border-color); background-color: rgba(255,255,255,0.01);">
            <div class="section-title-wrap" style="margin-bottom: 2.5rem;">
                <span class="section-subtitle">Mungkin Anda Suka</span>
                <h2 class="section-title" style="font-size: 1.75rem;">Berita <span>Terkait</span></h2>
            </div>
            
            <div class="grid-3" style="grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 1.75rem;">
                @foreach($relatedPosts as $rel)
                    <div class="card-post">
                        <div class="post-media">
                            @if($rel->image)
                                <img src="{{ asset('storage/' . $rel->image) }}" alt="{{ $rel->title }}">
                            @else
                                <div class="post-media-placeholder">✍️</div>
                            @endif
                        </div>
                        <div class="post-body">
                            <div class="post-meta">
                                <div style="display: inline-flex; flex-wrap: wrap; gap: 4px; align-items: center;">
                                    @foreach($rel->categories as $cat)
                                        <span class="post-category" style="margin-right: 0;">{{ $cat->name }}</span>
                                    @endforeach
                                </div>
                                <span>&bull;</span>
                                <span>{{ $rel->created_at->format('d M Y') }}</span>
                            </div>
                            <h3 class="post-title">
                                <a href="{{ route('news.show', $rel->slug) }}">{{ $rel->title }}</a>
                            </h3>
                            <p class="post-excerpt">{{ Str::limit($rel->excerpt, 120) }}</p>
                            
                            <div class="post-footer">
                                <a href="{{ route('news.show', $rel->slug) }}" class="post-read-more">
                                    Baca Selengkapnya
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    @endif
@endsection
