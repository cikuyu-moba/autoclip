<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Panel') - Arhana CMS</title>
    
    <!-- Admin Styling -->
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <link rel="icon" type="image/png" href="{{ asset('logo/logo.png') }}">
</head>
<body>

    <div class="admin-container">
        <!-- Sidebar Navigation -->
        <aside class="admin-sidebar" id="adminSidebar">
            <div class="sidebar-brand" style="padding: 1rem 1.5rem; display: flex; align-items: center;">
                <a href="{{ route('admin.dashboard') }}" class="sidebar-logo" style="display: flex; align-items: center; gap: 0.65rem; text-decoration: none;">
                    <img src="{{ asset('logo/logo.png') }}" alt="Arhana Logo" style="height: 44px; width: auto; object-fit: contain; filter: invert(1); mix-blend-mode: screen;">
                    <span style="font-weight: 800; color: var(--text-admin-primary); font-size: 1.15rem; letter-spacing: -0.5px;">Admin</span>
                </a>
            </div>
            
            <ul class="sidebar-menu">
                <li class="sidebar-item">
                    <a href="{{ route('admin.dashboard') }}" class="sidebar-link {{ Route::is('admin.dashboard') ? 'active' : '' }}">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="9"></rect><rect x="14" y="3" width="7" height="5"></rect><rect x="14" y="12" width="7" height="9"></rect><rect x="3" y="16" width="7" height="5"></rect></svg>
                        Dashboard
                    </a>
                </li>
                
                <li class="sidebar-item">
                    <a href="{{ route('admin.menus.index') }}" class="sidebar-link {{ Route::is('admin.menus.*') ? 'active' : '' }}">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="4" y1="9" x2="20" y2="9"></line><line x1="4" y1="15" x2="20" y2="15"></line><line x1="10" y1="3" x2="8" y2="21"></line><line x1="16" y1="3" x2="14" y2="21"></line></svg>
                        Menu Navigasi
                    </a>
                </li>
                
                <li class="sidebar-item">
                    <a href="{{ route('admin.categories.index') }}" class="sidebar-link {{ Route::is('admin.categories.*') ? 'active' : '' }}">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path></svg>
                        Kategori Berita
                    </a>
                </li>
                
                <li class="sidebar-item">
                    <a href="{{ route('admin.posts.index') }}" class="sidebar-link {{ Route::is('admin.posts.*') ? 'active' : '' }}">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                        Berita / Artikel
                    </a>
                </li>

                <li class="sidebar-item">
                    <a href="{{ route('admin.comments.index') }}" class="sidebar-link {{ Route::is('admin.comments.*') ? 'active' : '' }}">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                        Komentar
                        @php
                            $pendingCommentsCount = rescue(fn() => \App\Models\Comment::where('is_approved', false)->count(), 0, false);
                        @endphp
                        @if($pendingCommentsCount > 0)
                            <span style="background-color: var(--accent-amber); color: #000; font-size: 0.7rem; font-weight: 800; padding: 1px 6px; border-radius: 10px; margin-left: auto;">
                                {{ $pendingCommentsCount }}
                            </span>
                        @endif
                    </a>
                </li>

                <li class="sidebar-item">
                    <a href="{{ \Illuminate\Support\Facades\Route::has('admin.contact-messages.index') ? route('admin.contact-messages.index') : url('/admin/contact-messages') }}" class="sidebar-link {{ request()->is('admin/contact-messages*') ? 'active' : '' }}">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                        Pesan Kontak
                        @php
                            $unreadContactMessagesCount = rescue(function () {
                                if (\Illuminate\Support\Facades\Schema::hasTable('contact_messages')) {
                                    return \App\Models\ContactMessage::where('status', 'unread')->count();
                                }
                                return 0;
                            }, 0, false);
                        @endphp
                        @if($unreadContactMessagesCount > 0)
                            <span style="background-color: var(--accent-red); color: #fff; font-size: 0.7rem; font-weight: 800; padding: 1px 6px; border-radius: 10px; margin-left: auto;">
                                {{ $unreadContactMessagesCount }}
                            </span>
                        @endif
                    </a>
                </li>
                
                <li class="sidebar-item">
                    <a href="{{ route('admin.products.index') }}" class="sidebar-link {{ Route::is('admin.products.*') ? 'active' : '' }}">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"></circle><circle cx="20" cy="21" r="1"></circle><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path></svg>
                        Toko / Produk
                    </a>
                </li>

                <li class="sidebar-item">
                    <a href="{{ route('admin.software.index') }}" class="sidebar-link {{ Route::is('admin.software.*') ? 'active' : '' }}">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                        Software Gratis
                        @php
                            $pendingReportsCount = rescue(fn() => \App\Models\SoftwareReport::where('status', 'pending')->count(), 0, false);
                        @endphp
                        @if($pendingReportsCount > 0)
                            <span style="background-color: var(--accent-red); color: #fff; font-size: 0.65rem; font-weight: 800; padding: 2px 6px; border-radius: 10px; margin-left: auto;" title="{{ $pendingReportsCount }} Laporan Link Rusak">
                                {{ $pendingReportsCount }} Rusak
                            </span>
                        @endif
                    </a>
                </li>
                
                <li class="sidebar-item">
                    <a href="{{ route('admin.projects.index') }}" class="sidebar-link {{ Route::is('admin.projects.*') ? 'active' : '' }}">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="16 18 22 12 16 6"></polyline><polyline points="8 6 2 12 8 18"></polyline><line x1="12" y1="2" x2="12" y2="22"></line></svg>
                        Portofolio Proyek
                    </a>
                </li>
                
                <li class="sidebar-item">
                    <a href="{{ route('admin.users.index') }}" class="sidebar-link {{ Route::is('admin.users.*') ? 'active' : '' }}">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                        User / Admin
                    </a>
                </li>

                <!-- E-learning Menu Section -->
                <li class="sidebar-item" style="margin-top: 1rem; border-top: 1px dashed var(--border-admin); padding-top: 0.5rem; opacity: 0.5; padding-left: 1.5rem; font-size: 0.75rem; font-weight: 700; color: var(--text-admin-secondary); text-transform: uppercase; letter-spacing: 0.5px;">E-Learning</li>
                
                <li class="sidebar-item">
                    <a href="{{ route('admin.courses.index') }}" class="sidebar-link {{ Route::is('admin.courses.*') ? 'active' : '' }}">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
                        Materi Kursus
                    </a>
                </li>

                <li class="sidebar-item">
                    <a href="{{ route('admin.questions.index') }}" class="sidebar-link {{ Route::is('admin.questions.*') ? 'active' : '' }}">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path><line x1="12" y1="17" x2="12.01" y2="17"></line><circle cx="12" cy="12" r="10"></circle></svg>
                        Soal Ujian
                    </a>
                </li>

                <li class="sidebar-item">
                    <a href="{{ route('admin.students.index') }}" class="sidebar-link {{ Route::is('admin.students.*') ? 'active' : '' }}">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="8" cy="7" r="4"></circle><path d="M22 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                        Siswa Terdaftar
                    </a>
                </li>
                
                <li class="sidebar-item">
                    <a href="{{ route('admin.bookings.index') }}" class="sidebar-link {{ Route::is('admin.bookings.*') ? 'active' : '' }}">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                        Booking Private
                        @php
                            $pendingBookingsCount = rescue(fn() => \App\Models\BookingRequest::where('status', 'pending')->count(), 0, false);
                        @endphp
                        @if($pendingBookingsCount > 0)
                            <span style="background-color: var(--accent-amber); color: #000; font-size: 0.7rem; font-weight: 800; padding: 1px 6px; border-radius: 10px; margin-left: auto;">
                                {{ $pendingBookingsCount }}
                            </span>
                        @endif
                    </a>
                </li>
                
                <li class="sidebar-item" style="margin-top: 1rem; border-top: 1px dashed var(--border-admin); padding-top: 0.5rem;">
                    <a href="{{ route('admin.reports.index') }}" class="sidebar-link {{ Route::is('admin.reports.*') ? 'active' : '' }}">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="20" x2="18" y2="10"></line><line x1="12" y1="20" x2="12" y2="4"></line><line x1="6" y1="20" x2="6" y2="14"></line></svg>
                        Laporan Analitik
                    </a>
                </li>
                
                <li class="sidebar-item" style="margin-top: 2rem; border-top: 1px solid var(--border-admin); padding-top: 1rem;">
                    <a href="{{ route('home') }}" class="sidebar-link" target="_blank">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
                        Buka Website Utama
                    </a>
                </li>
            </ul>
            
            <div class="sidebar-footer">
                &copy; {{ date('Y') }} Arhana CMS
            </div>
        </aside>

        <!-- Main Page Container -->
        <div class="admin-main">
            <!-- Top Navbar -->
            <nav class="admin-navbar">
                <button class="navbar-toggle" id="adminNavbarToggle" aria-label="Toggle Sidebar">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="18" x2="21" y2="18"></line></svg>
                </button>
                
                <div class="navbar-page-title">
                    @yield('page_title', 'Dashboard')
                </div>
                
                <div class="navbar-user">
                    <div class="user-info">
                        <span class="user-name">{{ Auth::user()->name }}</span>
                        <span class="user-role">Administrator</span>
                    </div>
                    
                    <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" class="logout-btn">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                            Keluar
                        </button>
                    </form>
                </div>
            </nav>

            <!-- Content Area -->
            <div class="admin-content">
                <!-- Status Feedback Alerts -->
                @if(session('success'))
                    <div class="alert-admin alert-admin-success">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                        {{ session('success') }}
                    </div>
                @endif
                
                @if(session('error'))
                    <div class="alert-admin alert-admin-error">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                        {{ session('error') }}
                    </div>
                @endif

                @if(session('warning'))
                    <div class="alert-admin alert-admin-warning">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
                        {{ session('warning') }}
                    </div>
                @endif

                @if(session('info'))
                    <div class="alert-admin alert-admin-info">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
                        {{ session('info') }}
                    </div>
                @endif
                
                @yield('admin_content')
            </div>
        </div>
    </div>

    <!-- Client-side JavaScript -->
    <script src="{{ asset('js/arhana-multiselect.js') }}"></script>
    <script src="{{ asset('js/app.js') }}"></script>
    @yield('scripts')
</body>
</html>
