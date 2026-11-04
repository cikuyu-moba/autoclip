@extends('layouts.app')

@section('title', $product->name)

@section('meta_tags')
    <meta name="description" content="{{ Str::limit(strip_tags($product->description), 150) }}">
    <meta property="og:title" content="{{ $product->name }}">
    <meta property="og:description" content="{{ Str::limit(strip_tags($product->description), 150) }}">
    @if($product->image)
        <meta property="og:image" content="{{ asset('storage/' . $product->image) }}">
    @endif
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:type" content="og:product">
    <link rel="canonical" href="{{ url()->current() }}" />

    <!-- Structured Data (JSON-LD) -->
    <script type="application/ld+json">
    {
      "@@context": "https://schema.org",
      "@@type": "Product",
      "name": "{{ $product->name }}",
      "image": "{{ $product->image ? asset('storage/' . $product->image) : asset('logo/logo.png') }}",
      "description": "{{ Str::limit(strip_tags($product->description), 200) }}",
      "offers": {
        "@@type": "Offer",
        "priceCurrency": "IDR",
        "price": "{{ $product->price }}",
        "availability": "https://schema.org/InStock"
      }
    }
    </script>
@endsection

@section('content')
    <style>
        .product-detail-grid {
            display: grid;
            grid-template-columns: 1fr 1.2fr;
            gap: 3.5rem;
            max-width: 1200px;
            margin: 0 auto 4rem;
            background: var(--bg-secondary);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-lg);
            padding: 3rem;
            box-shadow: var(--shadow-sm);
            box-sizing: border-box;
            width: 100%;
        }

        .product-buy-buttons {
            border-top: 1px solid var(--border-color);
            padding-top: 2rem;
            margin-top: 2rem;
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        @media (max-width: 992px) {
            .product-detail-grid {
                grid-template-columns: 1fr;
                gap: 2.5rem;
                padding: 2rem;
            }
        }

        @media (max-width: 768px) {
            /* Fix page horizontal clipping and margins */
            .section-container {
                padding: 2.5rem 1rem !important;
                max-width: 100%;
                overflow-x: hidden;
            }
            .product-detail-grid {
                padding: 1.5rem;
                gap: 1.75rem;
                margin-bottom: 2rem;
                border-radius: var(--radius-md);
            }
            .product-detail-grid h1 {
                font-size: 1.65rem !important;
                word-break: break-word;
            }
            /* E-commerce Style Mobile Sticky Bottom Action Bar */
            .product-buy-buttons {
                position: fixed;
                bottom: 0;
                left: 0;
                right: 0;
                z-index: 9999;
                flex-direction: row !important;
                background-color: var(--bg-secondary);
                border-top: 1px solid var(--border-color);
                padding: 0.75rem 1rem !important;
                margin: 0 !important;
                gap: 0.75rem !important;
                box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.2);
            }
            .product-buy-buttons a, .product-buy-buttons button {
                flex: 1;
                margin: 0 !important;
                padding: 0.85rem 0.5rem !important;
                font-size: 0.95rem !important;
                border-radius: var(--radius-md);
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: 0.35rem;
            }
            .product-buy-buttons svg {
                width: 18px !important;
                height: 18px !important;
            }
            /* Add bottom padding to body/main so content is not cut off by sticky footer */
            body {
                padding-bottom: 75px !important;
            }
        }

        @media (max-width: 576px) {
            .product-detail-grid {
                padding: 1rem;
                gap: 1.5rem;
            }
        }
    </style>
    <section class="section-container" style="padding-top: 4rem; padding-bottom: 4rem;">
        <!-- Back Navigation -->
        <div style="max-width: 1200px; margin: 0 auto 2rem;">
            <a href="{{ route('shop') }}"
                style="font-size: 0.85rem; color: var(--accent-sky); font-weight: 600; display: inline-flex; align-items: center; gap: 0.25rem; margin-bottom: 1rem; text-decoration: none; transition: color 0.2s ease;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round">
                    <line x1="19" y1="12" x2="5" y2="12"></line>
                    <polyline points="12 19 5 12 12 5"></polyline>
                </svg>
                Kembali ke Toko
            </a>
        </div>

        <!-- Main Product Detail Panel -->
        <div class="product-detail-grid">

            <!-- Left Column: Premium Gallery -->
            <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                <!-- Main Active Image -->
                <div
                    style="aspect-ratio: 1; background: linear-gradient(135deg, #f1f5f9, #e2e8f0); border-radius: var(--radius-md); border: 1px solid var(--border-color); overflow: hidden; display: flex; align-items: center; justify-content: center; position: relative; box-shadow: var(--shadow-sm);">
                    @if($product->image)
                        <img id="detailMainImg" src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}"
                            style="width: 100%; height: 100%; object-fit: cover; transition: opacity 0.25s ease;">
                    @else
                        <div style="font-size: 5rem; opacity: 0.15;">📦</div>
                    @endif
                </div>

                <!-- Thumbnails row -->
                @php
                    $imgs = [];
                    if ($product->image)
                        $imgs[] = asset('storage/' . $product->image);
                    if ($product->image2)
                        $imgs[] = asset('storage/' . $product->image2);
                    if ($product->image3)
                        $imgs[] = asset('storage/' . $product->image3);
                    if ($product->image4)
                        $imgs[] = asset('storage/' . $product->image4);
                    if ($product->image5)
                        $imgs[] = asset('storage/' . $product->image5);
                @endphp

                @if(count($imgs) > 1)
                    <div
                        style="display: flex; gap: 0.75rem; justify-content: center; overflow-x: auto; padding-bottom: 0.5rem;">
                        @foreach($imgs as $idx => $imgSrc)
                            <div class="thumb-card" onclick="changeMainImage('{{ $imgSrc }}', this)"
                                style="width: 60px; height: 60px; border-radius: var(--radius-sm); border: {{ $idx === 0 ? '2px solid var(--accent-sky)' : '1px solid var(--border-color)' }}; overflow: hidden; cursor: pointer; transition: all 0.2s ease; display: flex; align-items: center; justify-content: center; background: rgba(0,0,0,0.02);">
                                <img src="{{ $imgSrc }}" style="width: 100%; height: 100%; object-fit: cover;">
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Right Column: Product Premium Information -->
            <div style="display: flex; flex-direction: column; justify-content: space-between;">
                <div>
                    <!-- Badges -->
                    <div style="display: flex; gap: 0.5rem; margin-bottom: 1rem; align-items: center; flex-wrap: wrap;">
                        <span
                            style="background-color: rgba(245, 158, 11, 0.08); border: 1px solid rgba(245, 158, 11, 0.15); color: var(--accent-amber); border-radius: 100px; padding: 0.2rem 0.75rem; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">
                            Premium
                        </span>
                        <span
                            style="background-color: rgba(56, 189, 248, 0.08); border: 1px solid rgba(56, 189, 248, 0.15); color: var(--accent-sky); border-radius: 100px; padding: 0.2rem 0.75rem; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">
                            Stok Tersedia
                        </span>
                        @if($product->categories && $product->categories->count() > 0)
                            @foreach($product->categories as $cat)
                                <span
                                    style="background-color: rgba(16, 185, 129, 0.08); border: 1px solid rgba(16, 185, 129, 0.15); color: #10b981; border-radius: 100px; padding: 0.2rem 0.75rem; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">
                                    {{ $cat->name }}
                                </span>
                            @endforeach
                        @endif
                    </div>

                    <h1
                        style="font-size: 2.25rem; font-weight: 800; color: var(--text-primary); margin-bottom: 0.75rem; line-height: 1.2; letter-spacing: -0.5px;">
                        {{ $product->name }}
                    </h1>

                    <!-- Price block -->
                    <div
                        style="background: rgba(0,0,0,0.02); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 1.25rem 1.5rem; margin: 1.5rem 0 2rem;">
                        <span
                            style="font-size: 0.85rem; color: var(--text-muted); font-weight: 600; text-transform: uppercase; display: block; margin-bottom: 0.25rem;">Harga
                            Spesial</span>
                        <span style="font-size: 2rem; font-weight: 800; color: var(--accent-amber); display: block;">Rp
                            {{ number_format($product->price, 0, ',', '.') }}</span>
                    </div>

                    <!-- Description section -->
                    <div style="border-top: 1px solid var(--border-color); padding-top: 1.5rem;">
                        <h3
                            style="font-size: 0.9rem; font-weight: 700; text-transform: uppercase; color: var(--text-muted); margin-bottom: 0.75rem; letter-spacing: 0.5px;">
                            Deskripsi Produk</h3>
                        @if(strlen($product->description) > 250)
                            <div id="descContainer">
                                <div id="descPreview"
                                    style="font-size: 1rem; color: var(--text-secondary); line-height: 1.7; white-space: pre-line; padding-right: 0.5rem; transition: all 0.3s ease;">
                                    {!! nl2br(e(Str::limit($product->description, 200, '...'))) !!}
                                </div>
                                <div id="descFull"
                                    style="display: none; font-size: 1rem; color: var(--text-secondary); line-height: 1.7; white-space: pre-line; padding-right: 0.5rem; transition: all 0.3s ease;">
                                    {!! nl2br(e($product->description)) !!}
                                </div>
                                <button id="btnToggleDesc" onclick="toggleDescription()"
                                    style="background: none; border: none; padding: 0; color: var(--accent-sky); font-weight: 700; font-size: 0.9rem; cursor: pointer; display: inline-flex; align-items: center; gap: 0.25rem; margin-top: 0.75rem; transition: color 0.2s ease;">
                                    <span>Lihat Selengkapnya</span>
                                    <svg id="toggleDescIcon" width="16" height="16" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"
                                        style="transition: transform 0.3s ease;">
                                        <polyline points="6 9 12 15 18 9"></polyline>
                                    </svg>
                                </button>
                            </div>
                            <script>
                                function toggleDescription() {
                                    const preview = document.getElementById('descPreview');
                                    const full = document.getElementById('descFull');
                                    const btn = document.getElementById('btnToggleDesc');
                                    const btnText = btn.querySelector('span');
                                    const icon = document.getElementById('toggleDescIcon');

                                    if (full.style.display === 'none') {
                                        full.style.display = 'block';
                                        preview.style.display = 'none';
                                        btnText.textContent = 'Lihat Lebih Sedikit';
                                        icon.style.transform = 'rotate(180deg)';
                                    } else {
                                        full.style.display = 'none';
                                        preview.style.display = 'block';
                                        btnText.textContent = 'Lihat Selengkapnya';
                                        icon.style.transform = 'rotate(0deg)';
                                    }
                                }
                            </script>
                        @else
                            <p
                                style="font-size: 1rem; color: var(--text-secondary); line-height: 1.7; white-space: pre-line; padding-right: 0.5rem;">
                                {!! nl2br(e($product->description)) !!}
                            </p>
                        @endif
                    </div>
                </div>

                <!-- Buy Button block -->
                <div class="product-buy-buttons">
                    @if($product->tokopedia_link)
                    <a href="{{ $product->tokopedia_link }}" target="_blank" rel="noopener noreferrer" 
                        style="width: 100%; padding: 1.15rem; font-size: 1.1rem; display: flex; align-items: center; justify-content: center; gap: 0.75rem; border-radius: var(--radius-md); font-weight: 700; color: white; background-color: #00AA5B; box-shadow: 0 4px 14px rgba(0, 170, 91, 0.25); transition: all 0.2s ease; text-decoration: none;">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
                            <line x1="3" y1="6" x2="21" y2="6"></line>
                            <path d="M16 10a4 4 0 0 1-8 0"></path>
                        </svg>
                        Beli di Tokopedia
                    </a>
                    @endif

                    @if($product->shopee_link)
                    <a href="{{ $product->shopee_link }}" target="_blank" rel="noopener noreferrer" 
                        style="width: 100%; padding: 1.15rem; font-size: 1.1rem; display: flex; align-items: center; justify-content: center; gap: 0.75rem; border-radius: var(--radius-md); font-weight: 700; color: white; background-color: #EE4D2D; box-shadow: 0 4px 14px rgba(238, 77, 45, 0.25); transition: all 0.2s ease; text-decoration: none;">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
                            <line x1="3" y1="6" x2="21" y2="6"></line>
                            <path d="M16 10a4 4 0 0 1-8 0"></path>
                        </svg>
                        Beli di Shopee
                    </a>
                    @endif

                    @if(!$product->tokopedia_link && !$product->shopee_link)
                    <button disabled style="width: 100%; padding: 1.15rem; font-size: 1.1rem; display: flex; align-items: center; justify-content: center; border-radius: var(--radius-md); font-weight: 700; color: var(--text-muted); background-color: var(--bg-tertiary); border: 1px dashed var(--border-color); cursor: not-allowed;">
                        Belum Tersedia
                    </button>
                    @endif
                </div>

                <!-- Product Share Section -->
                <div class="product-share-section" style="margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid var(--border-color); display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;">
                    <span style="font-size: 0.9rem; font-weight: 600; color: var(--text-secondary); margin-right: 0.5rem;">Bagikan:</span>
                    
                    <!-- WhatsApp -->
                    <a href="https://api.whatsapp.com/send?text={{ urlencode($product->name . ' - ' . url()->current()) }}" target="_blank" rel="noopener noreferrer" title="Bagikan ke WhatsApp" style="display: inline-flex; align-items: center; justify-content: center; width: 38px; height: 38px; border-radius: 50%; background-color: #25D366; color: white; transition: transform 0.2s; text-decoration: none;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-7.6-4.7 8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path></svg>
                    </a>

                    <!-- Facebook -->
                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}" target="_blank" rel="noopener noreferrer" title="Bagikan ke Facebook" style="display: inline-flex; align-items: center; justify-content: center; width: 38px; height: 38px; border-radius: 50%; background-color: #1877F2; color: white; transition: transform 0.2s; text-decoration: none;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path></svg>
                    </a>

                    <!-- X / Twitter -->
                    <a href="https://twitter.com/intent/tweet?text={{ urlencode($product->name) }}&url={{ urlencode(url()->current()) }}" target="_blank" rel="noopener noreferrer" title="Bagikan ke X" style="display: inline-flex; align-items: center; justify-content: center; width: 38px; height: 38px; border-radius: 50%; background-color: #000000; color: white; transition: transform 0.2s; text-decoration: none;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4l11.733 16h4.267l-11.733 -16z"></path><path d="M4 20l6.768 -6.768m2.46 -2.46l6.772 -6.772"></path></svg>
                    </a>

                    <!-- Instagram (Copy Link) -->
                    <button type="button" onclick="copyProductLink('{{ url()->current() }}')" title="Salin Link (Instagram tidak mendukung share URL langsung)" style="border:none; cursor:pointer; display: inline-flex; align-items: center; justify-content: center; width: 38px; height: 38px; border-radius: 50%; background: linear-gradient(45deg, #f09433 0%, #e6683c 25%, #dc2743 50%, #cc2366 75%, #bc1888 100%); color: white; transition: transform 0.2s;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line></svg>
                    </button>

                    <!-- TikTok (Copy Link) -->
                    <button type="button" onclick="copyProductLink('{{ url()->current() }}')" title="Salin Link (TikTok tidak mendukung share URL langsung)" style="border:none; cursor:pointer; display: inline-flex; align-items: center; justify-content: center; width: 38px; height: 38px; border-radius: 50%; background-color: #25F4EE; color: #000000; transition: transform 0.2s;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 12a4 4 0 1 0 4 4V4a5 5 0 0 0 5 5"></path></svg>
                    </button>
                    
                    <span id="copyProductFeedback" style="display: none; font-size: 0.8rem; color: #10b981; font-weight: 600; margin-left: 0.5rem; background-color: rgba(16,185,129,0.1); padding: 0.3rem 0.6rem; border-radius: 4px;">Link Disalin!</span>
                </div>

                <script>
                    function copyProductLink(text) {
                        navigator.clipboard.writeText(text).then(function() {
                            const feedback = document.getElementById('copyProductFeedback');
                            feedback.style.display = 'inline-block';
                            setTimeout(() => {
                                feedback.style.display = 'none';
                            }, 2500);
                        }).catch(function(err) {
                            console.error('Could not copy text: ', err);
                        });
                    }
                </script>
            </div>

        </div>
    </section>

    <!-- Related Products Section -->
    @if($relatedProducts->count() > 0)
        <section class="section-container"
            style="border-top: 1px solid var(--border-color); background-color: rgba(255,255,255,0.01); padding-top: 4rem; padding-bottom: 4rem;">
            <div class="section-title-wrap" style="margin-bottom: 3rem;">
                <span class="section-subtitle">Mungkin Anda Butuhkan</span>
                <h2 class="section-title" style="font-size: 1.75rem;">Produk <span>Terkait Lainnya</span></h2>
            </div>

            <div class="grid-3" style="grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 2rem;">
                @foreach($relatedProducts as $rel)
                    <a href="{{ route('shop.show', ['id' => $rel->id, 'slug' => Str::slug($rel->name)]) }}" class="card-product" style="text-decoration: none;">
                        <div class="product-media">
                            @if($rel->image)
                                <img src="{{ asset('storage/' . $rel->image) }}" alt="{{ $rel->name }}">
                            @else
                                <div class="product-media-placeholder">📦</div>
                            @endif
                        </div>
                        <div class="product-body">
                            <h3 class="product-name">{{ $rel->name }}</h3>
                            <p class="product-desc">{{ Str::limit($rel->description, 120) }}</p>

                            <div class="product-footer">
                                <span class="product-price">Rp {{ number_format($rel->price, 0, ',', '.') }}</span>
                                <span class="btn btn-primary btn-sm">
                                    Lihat Detail
                                </span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    @if(count($imgs) > 1)
        <script>
            function changeMainImage(src, thumbEl) {
                const mainImg = document.getElementById('detailMainImg');
                if (mainImg) {
                    mainImg.style.opacity = '0.3';
                    setTimeout(() => {
                        mainImg.src = src;
                        mainImg.style.opacity = '1';
                    }, 100);
                }

                // Reset border for all thumbnails
                const thumbs = document.querySelectorAll('.thumb-card');
                thumbs.forEach(thumb => {
                    thumb.style.border = '1px solid var(--border-color)';
                });

                // Set active border
                thumbEl.style.border = '2px solid var(--accent-sky)';
            }
        </script>
    @endif
@endsection