<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Arhana') - Personal Website & Portfolio</title>
    @yield('meta_tags')
    
    <!-- Premium Styles -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}?v={{ filemtime(public_path('css/style.css')) }}">
    <link rel="icon" type="image/png" href="{{ asset('logo/logo.png') }}">
</head>
<body>

    <!-- Main Dynamic Header -->
    <header>
        <div class="nav-container">
            <a href="{{ route('home') }}" class="logo" style="display: flex; align-items: center; gap: 0.5rem; text-decoration: none;">
                <img src="{{ asset('logo/logo.png') }}" alt="Arhana Logo" style="height: 56px; width: auto; object-fit: contain; mix-blend-mode: multiply;">
            </a>
            
            <button class="mobile-toggle" id="mobileToggle" aria-label="Toggle Menu">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="3" y1="12" x2="21" y2="12"></line>
                    <line x1="3" y1="6" x2="21" y2="6"></line>
                    <line x1="3" y1="18" x2="21" y2="18"></line>
                </svg>
            </button>
            
            <ul class="nav-menu" id="navMenu">
                @if(isset($navigation_menus) && $navigation_menus->count() > 0)
                    @foreach($navigation_menus as $menu)
                        @if($menu->submenus->count() > 0)
                            <li class="nav-item nav-item-dropdown">
                                <a href="javascript:void(0)" class="nav-link dropdown-toggle" role="button" aria-expanded="false">
                                    {{ $menu->title }}
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="6 9 12 15 18 9"></polyline>
                                    </svg>
                                </a>
                                <ul class="dropdown-menu">
                                    @foreach($menu->submenus as $submenu)
                                        <li class="dropdown-item">
                                            <a href="{{ $submenu->url }}" {{ $submenu->is_external ? 'target="_blank" rel="noopener noreferrer"' : '' }}>
                                                {{ $submenu->title }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </li>
                        @else
                            <li class="nav-item">
                                <a href="{{ $menu->url }}" class="nav-link" {{ $menu->is_external ? 'target="_blank" rel="noopener noreferrer"' : '' }}>
                                    {{ $menu->title }}
                                </a>
                            </li>
                        @endif
                    @endforeach
                @else
                    <!-- Fallback default menu if database is empty -->
                    <li class="nav-item"><a href="{{ route('home') }}" class="nav-link">HOME</a></li>
                    <li class="nav-item"><a href="{{ route('portfolio') }}" class="nav-link">PORTFOLIO</a></li>
                    <li class="nav-item"><a href="{{ route('software.index') }}" class="nav-link">SOFTWARE</a></li>
                    <li class="nav-item"><a href="{{ route('shop') }}" class="nav-link">SHOP</a></li>
                    <li class="nav-item"><a href="{{ route('news') }}" class="nav-link">NEWS</a></li>
                    <li class="nav-item"><a href="{{ route('about') }}" class="nav-link">ABOUT</a></li>
                    <li class="nav-item"><a href="{{ route('contact') }}" class="nav-link">CONTACT</a></li>
                @endif

                @auth
                    <li class="nav-item nav-item-dropdown">
                        <a href="javascript:void(0)" class="nav-link dropdown-toggle" role="button" aria-expanded="false" style="color: var(--accent-sky); font-weight: 700; cursor: pointer;">
                            {{ Auth::user()->name }}
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 16px; height: 16px; margin-left: 4px;">
                                <polyline points="6 9 12 15 18 9"></polyline>
                            </svg>
                        </a>
                        <ul class="dropdown-menu">
                            @if(Auth::user()->isAdmin())
                                <li class="dropdown-item"><a href="{{ route('admin.dashboard') }}">Admin Panel</a></li>
                            @endif
                            <li class="dropdown-item">
                                <form action="{{ route('student.logout') }}" method="POST" id="studentLogoutForm" style="display: none;">@csrf</form>
                                <a href="#" onclick="event.preventDefault(); document.getElementById('studentLogoutForm').submit();">Keluar</a>
                            </li>
                        </ul>
                    </li>
                @else
                    <li class="nav-item student-auth-links" style="display: flex; align-items: center; gap: 0.75rem; border-left: 1px solid var(--border-color); padding-left: 1rem; margin-left: 0.5rem;">
                        <a href="{{ route('student.login', ['redirect' => request()->fullUrl()]) }}" class="nav-link" style="color: var(--text-secondary); font-weight: 600;">Masuk</a>
                        <a href="{{ route('student.register', ['redirect' => request()->fullUrl()]) }}" style="color: #fff; background: var(--accent-sky); padding: 0.4rem 0.85rem; border-radius: var(--radius-sm); font-weight: 600; line-height: 1.2; text-decoration: none; transition: var(--transition-fast);" onmouseenter="this.style.opacity='0.9'" onmouseleave="this.style.opacity='1'">Daftar</a>
                    </li>
                @endauth
            </ul>
        </div>
    </header>

    <!-- Main Content Yield -->
    <main>
        @yield('content')
    </main>

    <!-- Master Footer -->
    <footer>
        <div class="footer-wrap">
            <div class="footer-col">
                <a href="{{ route('home') }}" class="logo footer-logo" style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1.5rem; text-decoration: none;">
                    <img src="{{ asset('logo/logo.png') }}" alt="Arhana Logo" style="height: 48px; width: auto; object-fit: contain; filter: invert(1); mix-blend-mode: screen;">
                </a>
                <p class="footer-desc">
                    Website teknologi, direktori download software gratis terverifikasi, portofolio rekayasa web, dan berita komputasi terkini yang dikembangkan menggunakan Laravel.
                </p>
            </div>
            
            <div class="footer-col">
                <h4 class="footer-widget-title">Navigasi Utama</h4>
                <ul class="footer-links">
                    <li><a href="{{ route('home') }}">Beranda</a></li>
                    <li><a href="{{ route('portfolio') }}">Portofolio Proyek</a></li>
                    <li><a href="{{ route('software.index') }}">Software Gratis</a></li>
                    <li><a href="{{ route('shop') }}">Toko Online</a></li>
                    <li><a href="{{ route('news') }}">Berita &amp; Artikel</a></li>
                </ul>
            </div>
            
            <div class="footer-col">
                <h4 class="footer-widget-title">Informasi &amp; Kebijakan</h4>
                <ul class="footer-links">
                    <li><a href="{{ route('about') }}">Tentang Kami (About Us)</a></li>
                    <li><a href="{{ route('contact') }}">Kontak Kami (Contact Us)</a></li>
                    <li><a href="{{ route('privacy') }}">Kebijakan Privasi (Privacy)</a></li>
                    <li><a href="{{ route('disclaimer') }}">Disclaimer (Penafian)</a></li>
                    <li><a href="{{ route('sitemap') }}" target="_blank">Peta Situs (Sitemap XML)</a></li>
                </ul>
            </div>

            <div class="footer-col" style="max-width: 200px;">
                <h4 class="footer-widget-title">Pengunjung</h4>
                <ul class="footer-links" style="display: flex; flex-direction: column; gap: 0.35rem; padding: 0;">
                    <li style="display: flex; justify-content: space-between; font-size: 0.8rem; color: var(--text-secondary); border-bottom: 1px dashed var(--border-color); padding-bottom: 0.25rem; opacity: 0.85;">
                        <span>Hari Ini</span>
                        <strong style="color: var(--text-primary);">{{ number_format($visitor_stats['today']) }}</strong>
                    </li>
                    <li style="display: flex; justify-content: space-between; font-size: 0.8rem; color: var(--text-secondary); border-bottom: 1px dashed var(--border-color); padding-bottom: 0.25rem; opacity: 0.85;">
                        <span>Minggu Ini</span>
                        <strong style="color: var(--text-primary);">{{ number_format($visitor_stats['weekly']) }}</strong>
                    </li>
                    <li style="display: flex; justify-content: space-between; font-size: 0.8rem; color: var(--text-secondary); border-bottom: 1px dashed var(--border-color); padding-bottom: 0.25rem; opacity: 0.85;">
                        <span>Bulan Ini</span>
                        <strong style="color: var(--text-primary);">{{ number_format($visitor_stats['monthly']) }}</strong>
                    </li>
                    <li style="display: flex; justify-content: space-between; font-size: 0.8rem; color: var(--text-secondary); opacity: 0.85;">
                        <span>Tahun Ini</span>
                        <strong style="color: var(--text-primary);">{{ number_format($visitor_stats['yearly']) }}</strong>
                    </li>
                </ul>
            </div>
        </div>
        
        <div class="footer-bottom">
            <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                <p style="margin: 0;">&copy; {{ date('Y') }} Arhana Computer. All rights reserved.</p>
                <div style="display: flex; gap: 0.75rem; font-size: 0.8rem; color: var(--text-muted); flex-wrap: wrap;">
                    <a href="{{ route('privacy') }}" style="color: var(--text-secondary); text-decoration: none;">Privacy Policy</a>
                    <span>&bull;</span>
                    <a href="{{ route('disclaimer') }}" style="color: var(--text-secondary); text-decoration: none;">Disclaimer</a>
                    <span>&bull;</span>
                    <a href="{{ route('about') }}" style="color: var(--text-secondary); text-decoration: none;">About Us</a>
                    <span>&bull;</span>
                    <a href="{{ route('contact') }}" style="color: var(--text-secondary); text-decoration: none;">Contact Us</a>
                </div>
            </div>
            <div class="footer-socials">
                <a href="#" class="footer-social-link" aria-label="Twitter">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M23 3a10.9 10.9 0 0 1-3.14 1.53 4.48 4.48 0 0 0-7.86 3v1A10.66 10.66 0 0 1 3 4s-4 9 5 13a11.64 11.64 0 0 1-7 2c9 5 20 0 20-11.5a4.5 4.5 0 0 0-.08-.83A7.72 7.72 0 0 0 23 3z"></path></svg>
                </a>
                <a href="https://github.com" target="_blank" rel="noopener noreferrer" class="footer-social-link" aria-label="GitHub">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 19c-5 1.5-5-2.5-7-3m14 6v-3.87a3.37 3.37 0 0 0-.94-2.61c3.14-.35 6.44-1.54 6.44-7A5.44 5.44 0 0 0 20 4.77 5.07 5.07 0 0 0 19.91 1S18.73.65 16 2.48a13.38 13.38 0 0 0-7 0C6.27.65 5.09 1 5.09 1A5.07 5.07 0 0 0 5 4.77a5.44 5.44 0 0 0-1.5 3.78c0 5.42 3.3 6.61 6.44 7A3.37 3.37 0 0 0 9 18.13V22"></path></svg>
                </a>
                <a href="https://linkedin.com" target="_blank" rel="noopener noreferrer" class="footer-social-link" aria-label="LinkedIn">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"></path><rect x="2" y="9" width="4" height="12"></rect><circle cx="4" cy="4" r="2"></circle></svg>
                </a>
            </div>
        </div>
    </footer>



    <!-- Premium Cookie Consent Popup (We value your privacy) -->
    <div id="cookieConsentBanner" style="display: none; position: fixed; bottom: 24px; right: 24px; max-width: 440px; width: calc(100% - 48px); background: rgba(15, 23, 42, 0.9); backdrop-filter: blur(12px); border: 1px solid rgba(255,255,255,0.08); border-radius: 12px; padding: 1.75rem; box-shadow: 0 10px 40px rgba(0,0,0,0.4); z-index: 99999; animation: cookieSlideUp 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards; box-sizing: border-box; font-family: system-ui, -apple-system, sans-serif;">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.75rem;">
            <h4 style="font-size: 1.15rem; font-weight: 700; color: #fff; margin: 0; letter-spacing: -0.2px;">We value your privacy</h4>
            <button type="button" onclick="dismissCookieConsent()" style="background: none; border: none; cursor: pointer; color: #94a3b8; padding: 0; display: flex; align-items: center; transition: color 0.2s;" onmouseenter="this.style.color='#fff'" onmouseleave="this.style.color='#94a3b8'">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
            </button>
        </div>
        
        <p style="font-size: 0.85rem; color: #94a3b8; line-height: 1.5; margin: 0 0 1.5rem 0; font-weight: 400;">
            Kami menggunakan cookie untuk meningkatkan pengalaman browsing Anda, menyajikan iklan yang relevan (Google AdSense), dan menganalisis lalu lintas situs web. Dengan mengklik "Accept All", Anda menyetujui penggunaan cookie kami. <a href="{{ route('privacy') }}" style="color: #38bdf8; text-decoration: underline; font-weight: 500;">Kebijakan Privasi &amp; Cookie</a>
        </p>

        <div class="cookie-btn-group">
            <button type="button" onclick="dismissCookieConsent()" class="cookie-btn cookie-btn-outline">Reject All</button>
            <button type="button" onclick="acceptAllCookies()" class="cookie-btn cookie-btn-primary">Accept All</button>
        </div>
    </div>

    <style>
        .cookie-btn-group {
            display: flex;
            gap: 0.75rem;
            align-items: center;
            justify-content: flex-end;
        }
        .cookie-btn {
            padding: 0.65rem 1.25rem;
            border-radius: 6px;
            font-size: 0.85rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            text-align: center;
            font-family: inherit;
        }
        .cookie-btn-outline {
            background: transparent;
            color: #38bdf8;
            border: 1px solid rgba(56, 189, 248, 0.4);
        }
        .cookie-btn-outline:hover {
            background: rgba(56, 189, 248, 0.08);
            border-color: #38bdf8;
        }
        .cookie-btn-primary {
            background: #2563eb;
            color: #fff;
            border: none;
        }
        .cookie-btn-primary:hover {
            background: #1d4ed8;
            box-shadow: 0 0 12px rgba(37, 99, 235, 0.4);
        }
        @keyframes cookieSlideUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @media (max-width: 576px) {
            #cookieConsentBanner {
                bottom: 12px;
                right: 12px;
                width: calc(100% - 24px);
                padding: 1.25rem;
            }
            .cookie-btn-group {
                flex-direction: column;
                align-items: stretch;
            }
            .cookie-btn {
                width: 100%;
            }
        }
        @media (max-width: 768px) {
            .student-auth-links {
                border-left: none !important;
                padding-left: 0 !important;
                margin-left: 0 !important;
                margin-top: 1rem;
                width: 100%;
                justify-content: center;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Check if user has already accepted cookie consent
            const consent = localStorage.getItem('arhana_cookie_consent_accepted');
            if (!consent) {
                const banner = document.getElementById('cookieConsentBanner');
                if (banner) {
                    banner.style.display = 'block';
                }
            }
        });

        function acceptAllCookies() {
            localStorage.setItem('arhana_cookie_consent_accepted', 'true');
            const banner = document.getElementById('cookieConsentBanner');
            if (banner) {
                banner.style.transition = 'opacity 0.3s, transform 0.3s';
                banner.style.opacity = '0';
                banner.style.transform = 'translateY(20px)';
                setTimeout(() => banner.remove(), 300);
            }
        }

        function dismissCookieConsent() {
            // Do NOT save to localStorage, so it will appear again on the next visit or page refresh
            const banner = document.getElementById('cookieConsentBanner');
            if (banner) {
                banner.style.transition = 'opacity 0.3s, transform 0.3s';
                banner.style.opacity = '0';
                banner.style.transform = 'translateY(20px)';
                setTimeout(() => banner.remove(), 300);
            }
        }
    </script>

    <!-- Interactive Scripts -->
    <script src="{{ asset('js/app.js') }}?v={{ filemtime(public_path('js/app.js')) }}"></script>
</body>
</html>
