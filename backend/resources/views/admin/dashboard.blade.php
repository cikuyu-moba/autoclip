@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page_title', 'Dashboard Utama')

@section('admin_content')
    <!-- Dashboard Statistics Metrics -->
    <div class="metrics-grid">
        <div class="metric-card">
            <div class="metric-header">
                <span>Menu Utama</span>
                <span class="metric-icon">🧭</span>
            </div>
            <span class="metric-value">{{ $menusCount }}</span>
        </div>
        
        <div class="metric-card">
            <div class="metric-header">
                <span>Kategori</span>
                <span class="metric-icon">🏷️</span>
            </div>
            <span class="metric-value">{{ $categoriesCount }}</span>
        </div>
        
        <div class="metric-card">
            <div class="metric-header">
                <span>Berita</span>
                <span class="metric-icon">📰</span>
            </div>
            <span class="metric-value">{{ $postsCount }}</span>
        </div>
        
        <div class="metric-card">
            <div class="metric-header">
                <span>Produk</span>
                <span class="metric-icon">🛍️</span>
            </div>
            <span class="metric-value">{{ $productsCount }}</span>
        </div>
        
        <div class="metric-card">
            <div class="metric-header">
                <span>Portofolio</span>
                <span class="metric-icon">💼</span>
            </div>
            <span class="metric-value">{{ $projectsCount }}</span>
        </div>

        <div class="metric-card">
            <div class="metric-header">
                <span>Software Gratis</span>
                <span class="metric-icon">💻</span>
            </div>
            <span class="metric-value">{{ $softwaresCount ?? 0 }}</span>
            <div style="font-size: 0.75rem; color: var(--accent-emerald); margin-top: 0.25rem; font-weight: 600;">
                📥 {{ number_format($totalSoftwareDownloads ?? 0) }} Unduhan
            </div>
        </div>
        
        <div class="metric-card">
            <div class="metric-header">
                <span>Admin</span>
                <span class="metric-icon">👥</span>
            </div>
            <span class="metric-value">{{ $usersCount }}</span>
        </div>
    </div>

    <!-- Visitor Analytics Dashboard Widget -->
    <div style="margin-bottom: 2.5rem;">
        <h3 style="margin-bottom: 1.5rem; font-weight: 700; font-size: 1.1rem; color: var(--text-admin-primary); display: flex; align-items: center; gap: 0.5rem;">
            <span>📊</span> Ringkasan Pengunjung Website
        </h3>
        <div class="metrics-grid" style="grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));">
            <div class="metric-card" style="border-left: 4px solid var(--accent-blue);">
                <div class="metric-header">
                    <span style="font-size: 0.8rem; color: var(--text-admin-secondary);">Pengunjung Hari Ini</span>
                    <span class="metric-icon">🌞</span>
                </div>
                <span class="metric-value" style="font-size: 1.8rem; font-weight: 800;">{{ number_format($todayVisitors) }}</span>
            </div>
            
            <div class="metric-card" style="border-left: 4px solid #10b981;">
                <div class="metric-header">
                    <span style="font-size: 0.8rem; color: var(--text-admin-secondary);">Minggu Ini</span>
                    <span class="metric-icon">📅</span>
                </div>
                <span class="metric-value" style="font-size: 1.8rem; font-weight: 800;">{{ number_format($weeklyVisitors) }}</span>
            </div>
            
            <div class="metric-card" style="border-left: 4px solid #f59e0b;">
                <div class="metric-header">
                    <span style="font-size: 0.8rem; color: var(--text-admin-secondary);">Bulan Ini</span>
                    <span class="metric-icon">🌕</span>
                </div>
                <span class="metric-value" style="font-size: 1.8rem; font-weight: 800;">{{ number_format($monthlyVisitors) }}</span>
            </div>
            
            <div class="metric-card" style="border-left: 4px solid #a855f7;">
                <div class="metric-header">
                    <span style="font-size: 0.8rem; color: var(--text-admin-secondary);">Tahun Ini</span>
                    <span class="metric-icon">🚀</span>
                </div>
                <span class="metric-value" style="font-size: 1.8rem; font-weight: 800;">{{ number_format($yearlyVisitors) }}</span>
            </div>
        </div>
    </div>

    <!-- Quick Shortcuts Panel -->
    <div class="admin-card" style="margin-bottom: 2.5rem; background: linear-gradient(135deg, var(--bg-admin-sidebar), rgba(56, 189, 248, 0.03));">
        <h3 style="margin-bottom: 1rem; font-weight: 700; font-size: 1.1rem;">Selamat Datang di Panel Kontrol Arhana!</h3>
        <p style="font-size: 0.875rem; color: var(--text-admin-secondary); margin-bottom: 1.5rem; max-width: 800px;">
            Di sini Anda dapat mengelola semua konten website personal secara dinamis. Tambahkan proyek baru ke portofolio Anda, publikasikan rilis berita terbaru, perbarui katalog produk toko online, atau atur susunan navigasi menu/submenu di situs publik.
        </p>
        <div style="display: flex; flex-wrap: wrap; gap: 0.75rem;">
            <a href="{{ route('admin.posts.create') }}" class="btn-admin btn-admin-primary btn-admin-sm">Tulis Berita Baru</a>
            <a href="{{ route('admin.projects.create') }}" class="btn-admin btn-admin-secondary btn-admin-sm">+ Tambah Portofolio</a>
            <a href="{{ route('admin.products.create') }}" class="btn-admin btn-admin-secondary btn-admin-sm">+ Jual Produk Baru</a>
        </div>
    </div>

    <!-- Gmail API Connection Status Widget -->
    @php
        $isGmailConnected = file_exists(storage_path('app/gmail_token.json'));
    @endphp
    <div class="admin-card" style="margin-bottom: 2.5rem; border-left: 4px solid {{ $isGmailConnected ? 'var(--accent-emerald)' : 'var(--accent-danger)' }};">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1.5rem;">
            <div>
                <h3 style="font-weight: 700; font-size: 1.1rem; display: flex; align-items: center; gap: 0.5rem;">
                    <span>📧</span> Integrasi Gmail API (OAuth 2.0)
                </h3>
                <p style="font-size: 0.85rem; color: var(--text-admin-secondary); margin-top: 0.25rem; max-width: 600px;">
                    Digunakan untuk mengirimkan email otomatis (seperti reset kata sandi mandiri siswa) secara aman tanpa menyimpan sandi asli Anda.
                </p>
            </div>
            <div style="display: flex; align-items: center; gap: 1rem; flex-wrap: wrap;">
                @if($isGmailConnected)
                    <span style="background: rgba(16, 185, 129, 0.1); color: var(--accent-emerald); border: 1px solid rgba(16, 185, 129, 0.2); padding: 0.4rem 0.85rem; border-radius: var(--radius-sm); font-size: 0.85rem; font-weight: 700; display: inline-flex; align-items: center; gap: 0.35rem;">
                        <span style="width: 8px; height: 8px; border-radius: 50%; background: var(--accent-emerald); display: inline-block;"></span>
                        Terhubung
                    </span>
                    <form action="{{ route('admin.gmail.disconnect') }}" method="POST" style="margin: 0;">
                        @csrf
                        <button type="submit" class="btn-admin btn-admin-danger btn-admin-sm">Putus Koneksi</button>
                    </form>
                @else
                    <span style="background: rgba(220, 38, 38, 0.1); color: var(--accent-danger); border: 1px solid rgba(220, 38, 38, 0.2); padding: 0.4rem 0.85rem; border-radius: var(--radius-sm); font-size: 0.85rem; font-weight: 700; display: inline-flex; align-items: center; gap: 0.35rem;">
                        <span style="width: 8px; height: 8px; border-radius: 50%; background: var(--accent-danger); display: inline-block;"></span>
                        Terputus
                    </span>
                    <a href="{{ route('admin.gmail.auth') }}" class="btn-admin btn-admin-primary btn-admin-sm" style="background-color: var(--accent-sky); border-color: var(--accent-sky); color: #ffffff !important; font-weight: 700; padding: 0.5rem 1rem; text-shadow: 0 1px 2px rgba(0,0,0,0.2); box-shadow: 0 4px 12px rgba(2, 132, 199, 0.25);">
                        Hubungkan Gmail API
                    </a>
                @endif
            </div>
    </div>

    <!-- Lists columns -->
    <div class="dashboard-grid">
        <!-- Left: Recent News Articles -->
        <div class="admin-card">
            <div class="admin-card-header">
                <h4 class="admin-card-title">Berita &amp; Artikel Terakhir</h4>
                <a href="{{ route('admin.posts.index') }}" style="font-size: 0.75rem; color: var(--accent-blue); font-weight: 600;">Lihat Semua</a>
            </div>
            
            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Judul Berita</th>
                            <th>Kategori</th>
                            <th>Tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($latestPosts as $post)
                            <tr>
                                <td style="font-weight: 600;">{{ Str::limit($post->title, 40) }}</td>
                                <td>
                                    @if($post->categories && $post->categories->count() > 0)
                                        @foreach($post->categories as $c)
                                            <span class="badge-admin badge-blue" style="margin-right: 2px;">{{ $c->name }}</span>
                                        @endforeach
                                    @else
                                        <span style="color: var(--text-admin-muted); font-size: 0.8rem;">-</span>
                                    @endif
                                </td>
                                <td style="font-size: 0.75rem; color: var(--text-admin-muted);">{{ $post->created_at->format('d M Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" style="text-align: center; color: var(--text-admin-secondary);">Belum ada berita yang diterbitkan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Right: Recent Projects -->
        <div class="admin-card">
            <div class="admin-card-header">
                <h4 class="admin-card-title">Portofolio Baru</h4>
                <a href="{{ route('admin.projects.index') }}" style="font-size: 0.75rem; color: var(--accent-blue); font-weight: 600;">Lihat Semua</a>
            </div>
            
            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Proyek</th>
                            <th>Teknologi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($latestProjects as $proj)
                            <tr>
                                <td style="font-weight: 600;">{{ Str::limit($proj->title, 25) }}</td>
                                <td style="font-size: 0.75rem; color: var(--text-admin-secondary);">{{ Str::limit($proj->technologies, 30) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" style="text-align: center; color: var(--text-admin-secondary);">Belum ada portofolio yang terdaftar.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
