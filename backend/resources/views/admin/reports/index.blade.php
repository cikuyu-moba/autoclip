@extends('layouts.admin')

@section('title', 'Laporan Analitik')
@section('page_title', 'Statistik & Laporan Pengunjung')

@section('admin_content')
    <!-- Filter Bar -->
    <div class="admin-card" style="margin-bottom: 1.5rem; padding: 1.25rem;">
        <form action="{{ route('admin.reports.index') }}" method="GET" style="display: flex; gap: 1rem; align-items: flex-end; flex-wrap: wrap;">
            <div>
                <label for="start_date" style="display: block; margin-bottom: 0.5rem; font-weight: 600; font-size: 0.85rem;">Tanggal Mulai</label>
                <input type="date" id="start_date" name="start_date" value="{{ $startDate }}" class="form-control" style="padding: 0.5rem; border: 1px solid var(--border-admin); border-radius: 6px; width: auto; color-scheme: dark;">
            </div>
            <div>
                <label for="end_date" style="display: block; margin-bottom: 0.5rem; font-weight: 600; font-size: 0.85rem;">Tanggal Akhir</label>
                <input type="date" id="end_date" name="end_date" value="{{ $endDate }}" class="form-control" style="padding: 0.5rem; border: 1px solid var(--border-admin); border-radius: 6px; width: auto; color-scheme: dark;">
            </div>
            <div>
                <button type="submit" class="btn-admin btn-admin-primary" style="padding: 0.55rem 1.5rem;">Terapkan Filter</button>
            </div>
        </form>
    </div>

    <!-- Summary Widgets -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
        <div class="admin-card" style="padding: 1.5rem; text-align: center; border-bottom: 4px solid var(--accent-blue);">
            <div style="font-size: 0.85rem; font-weight: 600; color: var(--text-admin-secondary); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 0.5rem;">Total Pengunjung Unik</div>
            <div style="font-size: 2.5rem; font-weight: 800; color: var(--text-admin-primary);">{{ number_format($totalPeriodVisitors) }}</div>
            <div style="font-size: 0.75rem; color: var(--text-admin-muted); margin-top: 0.25rem;">Dalam rentang tanggal terpilih</div>
        </div>
        
        <div class="admin-card" style="padding: 1.5rem; text-align: center; border-bottom: 4px solid #10b981;">
            <div style="font-size: 0.85rem; font-weight: 600; color: var(--text-admin-secondary); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 0.5rem;">Negara Teratas</div>
            <div style="font-size: 1.75rem; font-weight: 800; color: var(--text-admin-primary); display: flex; align-items: center; justify-content: center; gap: 0.5rem; margin-top: 0.5rem;">
                @if($topCountries->isNotEmpty())
                    <span>{{ \App\Services\GeoIpService::countryFlag($topCountries->first()->country_code) }}</span>
                    <span style="font-size: 1.4rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $topCountries->first()->country }}</span>
                @else
                    <span style="color: var(--text-admin-muted); font-size: 1.2rem;">Belum ada data</span>
                @endif
            </div>
            <div style="font-size: 0.75rem; color: var(--text-admin-muted); margin-top: 0.25rem;">
                {{ $topCountries->isNotEmpty() ? number_format($topCountries->first()->count) . ' pengunjung' : 'Menunggu kunjungan' }}
            </div>
        </div>

        <div class="admin-card" style="padding: 1.5rem; text-align: center; border-bottom: 4px solid #f59e0b;">
            <div style="font-size: 0.85rem; font-weight: 600; color: var(--text-admin-secondary); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 0.5rem;">Kota Teratas</div>
            <div style="font-size: 1.5rem; font-weight: 800; color: var(--text-admin-primary); margin-top: 0.5rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                @if($topCities->isNotEmpty())
                    {{ $topCities->first()->city }}
                @else
                    <span style="color: var(--text-admin-muted); font-size: 1.2rem;">Belum ada data</span>
                @endif
            </div>
            <div style="font-size: 0.75rem; color: var(--text-admin-muted); margin-top: 0.25rem;">
                {{ $topCities->isNotEmpty() ? number_format($topCities->first()->count) . ' pengunjung' : 'Menunggu kunjungan' }}
            </div>
        </div>
    </div>

    <!-- Top Geolocation Breakdown -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
        <!-- Top Countries -->
        <div class="admin-card">
            <div class="admin-card-header" style="border-bottom: 1px solid var(--border-admin);">
                <h4 class="admin-card-title" style="display: flex; align-items: center; gap: 0.5rem;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="2" y1="12" x2="22" y2="12"></line><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path></svg>
                    Asal Negara Pengunjung
                </h4>
            </div>
            <div style="padding: 1rem 1.5rem;">
                <ul style="list-style: none; padding: 0; margin: 0;">
                    @php $maxCountryCount = $topCountries->max('count') ?: 1; @endphp
                    @forelse($topCountries as $c)
                        <li style="margin-bottom: 0.9rem;">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.35rem; font-size: 0.88rem;">
                                <div style="display: flex; align-items: center; gap: 0.5rem;">
                                    <span style="font-size: 1.1rem;">{{ \App\Services\GeoIpService::countryFlag($c->country_code) }}</span>
                                    <span style="font-weight: 600; color: var(--text-admin-primary);">{{ $c->country }}</span>
                                </div>
                                <span class="badge-admin badge-blue" style="font-size: 0.75rem;">{{ number_format($c->count) }} pengunjung</span>
                            </div>
                            <div style="width: 100%; background: rgba(255,255,255,0.05); height: 6px; border-radius: 3px; overflow: hidden;">
                                <div style="width: {{ ($c->count / $maxCountryCount) * 100 }}%; background: var(--accent-blue); height: 100%; border-radius: 3px;"></div>
                            </div>
                        </li>
                    @empty
                        <li style="text-align: center; color: var(--text-admin-secondary); padding: 1.5rem;">Belum ada data negara terdeteksi.</li>
                    @endforelse
                </ul>
            </div>
        </div>

        <!-- Top Cities -->
        <div class="admin-card">
            <div class="admin-card-header" style="border-bottom: 1px solid var(--border-admin);">
                <h4 class="admin-card-title" style="display: flex; align-items: center; gap: 0.5rem;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                    Asal Kota Pengunjung
                </h4>
            </div>
            <div style="padding: 1rem 1.5rem;">
                <ul style="list-style: none; padding: 0; margin: 0;">
                    @php $maxCityCount = $topCities->max('count') ?: 1; @endphp
                    @forelse($topCities as $city)
                        <li style="margin-bottom: 0.9rem;">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.35rem; font-size: 0.88rem;">
                                <div>
                                    <span style="font-weight: 600; color: var(--text-admin-primary);">{{ $city->city }}</span>
                                    @if(!empty($city->region))
                                        <span style="font-size: 0.75rem; color: var(--text-admin-muted);">({{ $city->region }})</span>
                                    @endif
                                </div>
                                <span class="badge-admin badge-emerald" style="font-size: 0.75rem;">{{ number_format($city->count) }} pengunjung</span>
                            </div>
                            <div style="width: 100%; background: rgba(255,255,255,0.05); height: 6px; border-radius: 3px; overflow: hidden;">
                                <div style="width: {{ ($city->count / $maxCityCount) * 100 }}%; background: #10b981; height: 100%; border-radius: 3px;"></div>
                            </div>
                        </li>
                    @empty
                        <li style="text-align: center; color: var(--text-admin-secondary); padding: 1.5rem;">Belum ada data kota terdeteksi.</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>

    <!-- TABEL UTAMA: DAFTAR PENGUNJUNG BERDASARKAN IP & LOKASI -->
    <div class="admin-card" style="margin-bottom: 2rem;">
        <div class="admin-card-header" style="border-bottom: 1px solid var(--border-admin); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.5rem;">
            <div>
                <h4 class="admin-card-title" style="margin: 0; display: flex; align-items: center; gap: 0.5rem;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="2" y1="12" x2="22" y2="12"></line><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path></svg>
                    Daftar Pengunjung Berdasarkan Alamat IP & Lokasi
                </h4>
                <div style="font-size: 0.8rem; color: var(--text-admin-muted); margin-top: 0.25rem;">
                    Mendeteksi alamat IP, negara, kota asal, dan halaman yang diakses oleh pengunjung website.
                </div>
            </div>
            <span class="badge-admin badge-blue">
                Total: {{ $visitors->total() }} Data
            </span>
        </div>
        
        <div style="padding: 0 1.5rem 1.5rem 1.5rem; overflow-x: auto;">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th style="min-width: 140px;">Waktu Terakhir</th>
                        <th style="min-width: 150px;">Alamat IP</th>
                        <th style="min-width: 200px;">Lokasi Geografis</th>
                        <th style="min-width: 220px;">Halaman Terakhir Dikunjungi</th>
                        <th style="text-align: center; min-width: 80px;">Kunjungan</th>
                        <th style="text-align: right; min-width: 150px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($visitors as $visitor)
                        <tr>
                            <td style="font-size: 0.825rem;">
                                <div style="font-weight: 600; color: var(--text-admin-primary);">
                                    {{ \Carbon\Carbon::parse($visitor->updated_at ?? $visitor->date)->format('d M Y, H:i') }}
                                </div>
                                <div style="font-size: 0.72rem; color: var(--text-admin-muted);">
                                    {{ \Carbon\Carbon::parse($visitor->updated_at ?? $visitor->date)->diffForHumans() }}
                                </div>
                            </td>
                            <td>
                                <div style="display: flex; align-items: center; gap: 0.5rem;">
                                    <code style="background: rgba(255,255,255,0.06); padding: 0.2rem 0.45rem; border-radius: 4px; font-family: monospace; font-size: 0.825rem; color: var(--accent-blue);">
                                        {{ $visitor->ip_address }}
                                    </code>
                                    @if(in_array($visitor->ip_address, ['127.0.0.1', '::1']))
                                        <span class="badge-admin" style="font-size: 0.65rem; background: rgba(255,255,255,0.08);">Dev</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div style="display: flex; align-items: center; gap: 0.6rem;">
                                    <span style="font-size: 1.25rem; line-height: 1;">{{ $visitor->flag }}</span>
                                    <div>
                                        <div style="font-weight: 600; font-size: 0.875rem; color: var(--text-admin-primary);">
                                            {{ $visitor->city ?: 'Kota Tidak Diketahui' }}
                                        </div>
                                        <div style="font-size: 0.75rem; color: var(--text-admin-muted);">
                                            {{ implode(', ', array_filter([$visitor->region, $visitor->country])) ?: '-' }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if(!empty($visitor->last_page))
                                    <div style="display: flex; align-items: center; gap: 0.4rem; max-width: 260px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink: 0; color: var(--accent-blue);"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>
                                        <span style="font-size: 0.825rem; color: var(--text-admin-secondary); overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $visitor->last_page }}">
                                            {{ $visitor->last_page }}
                                        </span>
                                    </div>
                                @else
                                    <span style="font-size: 0.75rem; color: var(--text-admin-muted); font-style: italic;">Belum tercatat</span>
                                @endif
                            </td>
                            <td style="text-align: center;">
                                <span class="badge-admin badge-blue" style="font-weight: 700; font-size: 0.75rem;">
                                    {{ number_format($visitor->hits ?? 1) }}x
                                </span>
                            </td>
                            <td style="text-align: right;">
                                <button type="button" class="btn-admin btn-admin-secondary btn-admin-sm" 
                                    onclick="openVisitorLogsModal('{{ $visitor->ip_address }}')"
                                    style="gap: 0.4rem;"
                                    title="Lihat riwayat halaman yang dikunjungi oleh IP ini">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                                    <span>Riwayat Halaman</span>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; color: var(--text-admin-secondary); padding: 2.5rem 1rem;">
                                Tidak ada data pengunjung pada rentang tanggal ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($visitors->hasPages())
            <div class="admin-pagination-wrapper" style="padding: 0 1.5rem 1.5rem 1.5rem;">
                {{ $visitors->links() }}
            </div>
        @endif
    </div>

    <!-- LOG AKTIVITAS TERKINI (REAL-TIME STREAM) -->
    @if($recentLogs->isNotEmpty())
        <div class="admin-card" style="margin-bottom: 2rem;">
            <div class="admin-card-header" style="border-bottom: 1px solid var(--border-admin);">
                <h4 class="admin-card-title" style="margin: 0; display: flex; align-items: center; gap: 0.5rem;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline></svg>
                    Log Kunjungan Halaman Terkini
                </h4>
            </div>
            <div style="padding: 0 1.5rem 1.5rem 1.5rem; overflow-x: auto;">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th style="width: 140px;">Waktu</th>
                            <th style="width: 150px;">Alamat IP</th>
                            <th style="width: 180px;">Lokasi</th>
                            <th>Halaman yang Dibuka</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentLogs as $log)
                            <tr>
                                <td style="font-size: 0.8rem; color: var(--text-admin-muted);">
                                    {{ $log->created_at ? \Carbon\Carbon::parse($log->created_at)->format('H:i:s') . ' (' . \Carbon\Carbon::parse($log->created_at)->diffForHumans() . ')' : '-' }}
                                </td>
                                <td>
                                    <code style="background: rgba(255,255,255,0.06); padding: 0.15rem 0.4rem; border-radius: 4px; font-family: monospace; font-size: 0.8rem; color: var(--text-admin-primary);">
                                        {{ $log->ip_address }}
                                    </code>
                                </td>
                                <td style="font-size: 0.825rem;">
                                    <span>{{ $log->flag }}</span>
                                    <span style="color: var(--text-admin-secondary); margin-left: 0.3rem;">{{ $log->city ?: $log->country }}</span>
                                </td>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 0.5rem;">
                                        <span class="badge-admin badge-blue" style="font-size: 0.72rem;">{{ $log->page_name ?: 'Halaman' }}</span>
                                        <a href="{{ url($log->url) }}" target="_blank" style="color: var(--accent-blue); text-decoration: none; font-size: 0.825rem; font-family: monospace;" title="Buka halaman">
                                            {{ $log->url }}
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <div style="display: grid; grid-template-columns: 1fr; gap: 1.5rem; margin-bottom: 2rem;">
        <!-- Grafik Kunjungan (Tabel Sederhana) -->
        <div class="admin-card">
            <div class="admin-card-header">
                <h4 class="admin-card-title">Tren Kunjungan Harian</h4>
            </div>
            <div style="padding: 0 1.5rem 1.5rem 1.5rem;">
                <div style="max-height: 300px; overflow-y: auto;">
                    <table class="admin-table" style="margin-top: 0;">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th style="text-align: right;">Jumlah Pengunjung</th>
                                <th style="width: 50%;">Representasi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $maxVisitors = $dailyVisitors->max('count') ?: 1;
                            @endphp
                            @forelse($dailyVisitors as $day)
                                <tr>
                                    <td style="font-weight: 600;">{{ \Carbon\Carbon::parse($day->date)->format('d M Y') }}</td>
                                    <td style="text-align: right; font-weight: 700;">{{ number_format($day->count) }}</td>
                                    <td>
                                        <div style="width: 100%; background: #f0f4f8; height: 12px; border-radius: 6px; overflow: hidden;">
                                            <div style="width: {{ ($day->count / $maxVisitors) * 100 }}%; background: var(--accent-blue); height: 100%;"></div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" style="text-align: center; color: var(--text-admin-secondary); padding: 2rem;">Tidak ada data pengunjung pada rentang tanggal ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Top 10 Lists -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
        
        <!-- Top Articles -->
        <div class="admin-card">
            <div class="admin-card-header" style="border-bottom: 1px solid var(--border-admin);">
                <h4 class="admin-card-title">10 Berita Terpopuler</h4>
            </div>
            <div>
                <ul style="list-style: none; padding: 0; margin: 0;">
                    @forelse($popularPosts as $index => $post)
                        <li style="padding: 1rem 1.5rem; border-bottom: 1px solid var(--border-admin); display: flex; justify-content: space-between; align-items: center;">
                            <div style="display: flex; align-items: center; gap: 1rem; overflow: hidden;">
                                <span style="font-weight: 800; color: var(--text-admin-muted); font-size: 1.2rem; min-width: 24px;">{{ $index + 1 }}</span>
                                <div style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                    <div style="font-weight: 600; font-size: 0.95rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $post->title }}</div>
                                </div>
                            </div>
                            <span class="badge-admin badge-blue" style="margin-left: 1rem; white-space: nowrap;">{{ number_format($post->views_count) }}x</span>
                        </li>
                    @empty
                        <li style="padding: 2rem; text-align: center; color: var(--text-admin-secondary);">Belum ada data tampilan berita.</li>
                    @endforelse
                </ul>
            </div>
        </div>

        <!-- Top Products -->
        <div class="admin-card">
            <div class="admin-card-header" style="border-bottom: 1px solid var(--border-admin);">
                <h4 class="admin-card-title">10 Produk Terpopuler</h4>
            </div>
            <div>
                <ul style="list-style: none; padding: 0; margin: 0;">
                    @forelse($popularProducts as $index => $product)
                        <li style="padding: 1rem 1.5rem; border-bottom: 1px solid var(--border-admin); display: flex; justify-content: space-between; align-items: center;">
                            <div style="display: flex; align-items: center; gap: 1rem; overflow: hidden;">
                                <span style="font-weight: 800; color: var(--text-admin-muted); font-size: 1.2rem; min-width: 24px;">{{ $index + 1 }}</span>
                                <div style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                    <div style="font-weight: 600; font-size: 0.95rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $product->name }}</div>
                                </div>
                            </div>
                            <span class="badge-admin badge-amber" style="margin-left: 1rem; white-space: nowrap;">{{ number_format($product->views_count) }}x</span>
                        </li>
                    @empty
                        <li style="padding: 2rem; text-align: center; color: var(--text-admin-secondary);">Belum ada data tampilan produk.</li>
                    @endforelse
                </ul>
            </div>
        </div>

        <!-- Top Projects -->
        <div class="admin-card">
            <div class="admin-card-header" style="border-bottom: 1px solid var(--border-admin);">
                <h4 class="admin-card-title">10 Portofolio Terpopuler</h4>
            </div>
            <div>
                <ul style="list-style: none; padding: 0; margin: 0;">
                    @forelse($popularProjects as $index => $project)
                        <li style="padding: 1rem 1.5rem; border-bottom: 1px solid var(--border-admin); display: flex; justify-content: space-between; align-items: center;">
                            <div style="display: flex; align-items: center; gap: 1rem; overflow: hidden;">
                                <span style="font-weight: 800; color: var(--text-admin-muted); font-size: 1.2rem; min-width: 24px;">{{ $index + 1 }}</span>
                                <div style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                    <div style="font-weight: 600; font-size: 0.95rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $project->title }}</div>
                                </div>
                            </div>
                            <span class="badge-admin badge-gray" style="margin-left: 1rem; white-space: nowrap;">{{ number_format($project->views_count) }}x</span>
                        </li>
                    @empty
                        <li style="padding: 2rem; text-align: center; color: var(--text-admin-secondary);">Belum ada data tampilan proyek.</li>
                    @endforelse
                </ul>
            </div>
        </div>

    </div>

    <!-- Software Analytics Section: Top Downloaded & Top Viewed -->
    <div class="admin-card" style="margin-bottom: 2rem;">
        <div class="admin-card-header" style="border-bottom: 1px solid var(--border-admin); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.75rem;">
            <div>
                <h4 class="admin-card-title" style="margin: 0; display: flex; align-items: center; gap: 0.5rem;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                    Laporan Analitik Software (Unduhan &amp; Tayangan)
                </h4>
                <div style="font-size: 0.8rem; color: var(--text-admin-muted); margin-top: 0.25rem;">
                    Statistik real-time software yang paling banyak di-download dan paling sering dilihat pengunjung.
                </div>
            </div>
            <div style="display: flex; gap: 0.5rem; align-items: center; flex-wrap: wrap;">
                <span class="badge-admin badge-emerald" style="font-size: 0.8rem; padding: 5px 12px; font-weight: 700;">
                    📥 {{ number_format($softwareStats['total_downloads'] ?? 0) }} Total Unduhan
                </span>
                <span class="badge-admin badge-blue" style="font-size: 0.8rem; padding: 5px 12px; font-weight: 700;">
                    👁️ {{ number_format($softwareStats['total_views'] ?? 0) }} Total Tayangan
                </span>
                <a href="{{ route('admin.software.index') }}" class="btn-admin btn-admin-secondary btn-admin-sm" style="font-size: 0.8rem; display: inline-flex; align-items: center; gap: 0.35rem;">
                    Kelola Software &rarr;
                </a>
            </div>
        </div>

        <div style="padding: 1.5rem;">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(340px, 1fr)); gap: 1.5rem;">
                
                <!-- 10 Software Terbanyak Di-download -->
                <div style="background: rgba(15, 23, 42, 0.4); border: 1px solid var(--border-admin); border-radius: var(--radius-sm); overflow: hidden;">
                    <div style="padding: 1rem 1.25rem; background: rgba(16, 185, 129, 0.08); border-bottom: 1px solid var(--border-admin); display: flex; justify-content: space-between; align-items: center;">
                        <h5 style="margin: 0; font-size: 0.95rem; font-weight: 700; color: #10b981; display: flex; align-items: center; gap: 0.5rem;">
                            <span>📥</span> 10 Software Terbanyak Di-download
                        </h5>
                        <span style="font-size: 0.75rem; color: var(--text-admin-secondary); font-weight: 600;">Rangking Unduhan</span>
                    </div>

                    <ul style="list-style: none; padding: 0; margin: 0;">
                        @forelse($popularSoftwareDownloads as $index => $soft)
                            <li style="padding: 0.85rem 1.25rem; border-bottom: 1px solid var(--border-admin); display: flex; justify-content: space-between; align-items: center; gap: 1rem; transition: background 0.15s;" onmouseenter="this.style.background='rgba(255,255,255,0.02)'" onmouseleave="this.style.background='transparent'">
                                <div style="display: flex; align-items: center; gap: 0.75rem; min-width: 0; flex: 1;">
                                    <span style="font-weight: 800; color: {{ $index < 3 ? '#10b981' : 'var(--text-admin-muted)' }}; font-size: 1.1rem; min-width: 22px; text-align: center;">
                                        {{ $index + 1 }}
                                    </span>
                                    <div style="width: 36px; height: 36px; border-radius: 8px; border: 1px solid var(--border-admin); background: #ffffff; display: flex; align-items: center; justify-content: center; overflow: hidden; padding: 4px; flex-shrink: 0;">
                                        <img src="{{ $soft->icon_url }}" alt="{{ $soft->title }}" style="width: 100%; height: 100%; object-fit: contain;">
                                    </div>
                                    <div style="min-width: 0; flex: 1;">
                                        <div style="font-weight: 600; font-size: 0.9rem; color: var(--text-admin-primary); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                            <a href="{{ route('software.show', $soft->slug) }}" target="_blank" style="color: inherit; text-decoration: none;" title="Buka Halaman Publik">
                                                {{ $soft->title }}
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 12px; height: 12px; display: inline-block; vertical-align: middle; color: var(--text-admin-muted);"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
                                            </a>
                                        </div>
                                        <div style="font-size: 0.725rem; color: var(--text-admin-secondary); display: flex; align-items: center; gap: 0.35rem; flex-wrap: wrap;">
                                            <span>v{{ $soft->version }}</span>
                                            <span>&bull;</span>
                                            <span>{{ $soft->category?->name ?? 'Software' }}</span>
                                            <span>&bull;</span>
                                            <span style="display: inline-flex; gap: 2px;">
                                                @foreach($soft->supported_os_list as $osSlug)
                                                    @php $meta = \App\Models\Software::getOsMeta($osSlug); @endphp
                                                    <span title="{{ $meta['label'] }}">{{ $meta['icon'] }}</span>
                                                @endforeach
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div style="text-align: right; flex-shrink: 0;">
                                    <span class="badge-admin badge-emerald" style="font-weight: 800; font-size: 0.8rem; white-space: nowrap; padding: 3px 8px;">
                                        📥 {{ number_format($soft->downloads_count) }}x
                                    </span>
                                    <div style="font-size: 0.7rem; color: var(--text-admin-muted); margin-top: 3px;">
                                        👁️ {{ number_format($soft->views_count) }}x tayang
                                    </div>
                                </div>
                            </li>
                        @empty
                            <li style="padding: 2.5rem 1rem; text-align: center; color: var(--text-admin-secondary);">
                                <div style="font-size: 1.5rem; margin-bottom: 0.25rem;">📥</div>
                                Belum ada unduhan software yang terekam.
                            </li>
                        @endforelse
                    </ul>
                </div>

                <!-- 10 Software Terbanyak Dilihat -->
                <div style="background: rgba(15, 23, 42, 0.4); border: 1px solid var(--border-admin); border-radius: var(--radius-sm); overflow: hidden;">
                    <div style="padding: 1rem 1.25rem; background: rgba(56, 189, 248, 0.08); border-bottom: 1px solid var(--border-admin); display: flex; justify-content: space-between; align-items: center;">
                        <h5 style="margin: 0; font-size: 0.95rem; font-weight: 700; color: var(--accent-sky); display: flex; align-items: center; gap: 0.5rem;">
                            <span>👁️</span> 10 Software Terbanyak Dilihat
                        </h5>
                        <span style="font-size: 0.75rem; color: var(--text-admin-secondary); font-weight: 600;">Rangking Tayangan</span>
                    </div>

                    <ul style="list-style: none; padding: 0; margin: 0;">
                        @forelse($popularSoftwareViews as $index => $soft)
                            <li style="padding: 0.85rem 1.25rem; border-bottom: 1px solid var(--border-admin); display: flex; justify-content: space-between; align-items: center; gap: 1rem; transition: background 0.15s;" onmouseenter="this.style.background='rgba(255,255,255,0.02)'" onmouseleave="this.style.background='transparent'">
                                <div style="display: flex; align-items: center; gap: 0.75rem; min-width: 0; flex: 1;">
                                    <span style="font-weight: 800; color: {{ $index < 3 ? 'var(--accent-sky)' : 'var(--text-admin-muted)' }}; font-size: 1.1rem; min-width: 22px; text-align: center;">
                                        {{ $index + 1 }}
                                    </span>
                                    <div style="width: 36px; height: 36px; border-radius: 8px; border: 1px solid var(--border-admin); background: #ffffff; display: flex; align-items: center; justify-content: center; overflow: hidden; padding: 4px; flex-shrink: 0;">
                                        <img src="{{ $soft->icon_url }}" alt="{{ $soft->title }}" style="width: 100%; height: 100%; object-fit: contain;">
                                    </div>
                                    <div style="min-width: 0; flex: 1;">
                                        <div style="font-weight: 600; font-size: 0.9rem; color: var(--text-admin-primary); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                            <a href="{{ route('software.show', $soft->slug) }}" target="_blank" style="color: inherit; text-decoration: none;" title="Buka Halaman Publik">
                                                {{ $soft->title }}
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 12px; height: 12px; display: inline-block; vertical-align: middle; color: var(--text-admin-muted);"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
                                            </a>
                                        </div>
                                        <div style="font-size: 0.725rem; color: var(--text-admin-secondary); display: flex; align-items: center; gap: 0.35rem; flex-wrap: wrap;">
                                            <span>v{{ $soft->version }}</span>
                                            <span>&bull;</span>
                                            <span>{{ $soft->category?->name ?? 'Software' }}</span>
                                            <span>&bull;</span>
                                            <span style="display: inline-flex; gap: 2px;">
                                                @foreach($soft->supported_os_list as $osSlug)
                                                    @php $meta = \App\Models\Software::getOsMeta($osSlug); @endphp
                                                    <span title="{{ $meta['label'] }}">{{ $meta['icon'] }}</span>
                                                @endforeach
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div style="text-align: right; flex-shrink: 0;">
                                    <span class="badge-admin badge-blue" style="font-weight: 800; font-size: 0.8rem; white-space: nowrap; padding: 3px 8px;">
                                        👁️ {{ number_format($soft->views_count) }}x
                                    </span>
                                    <div style="font-size: 0.7rem; color: var(--text-admin-muted); margin-top: 3px;">
                                        📥 {{ number_format($soft->downloads_count) }}x unduhan
                                    </div>
                                </div>
                            </li>
                        @empty
                            <li style="padding: 2.5rem 1rem; text-align: center; color: var(--text-admin-secondary);">
                                <div style="font-size: 1.5rem; margin-bottom: 0.25rem;">👁️</div>
                                Belum ada tayangan software yang terekam.
                            </li>
                        @endforelse
                    </ul>
                </div>

            </div>
        </div>
    </div>

    <!-- MODAL POPUP RIWAYAT HALAMAN PENGUNJUNG -->
    <div id="visitorLogsModal" style="display: none; position: fixed; inset: 0; background: rgba(0, 0, 0, 0.7); backdrop-filter: blur(8px); z-index: 9999; justify-content: center; align-items: center; padding: 1.5rem; opacity: 0; transition: opacity 0.25s ease;">
        <div style="background: var(--bg-admin-sidebar); border: 1px solid var(--border-admin-hover); border-radius: var(--radius-md); max-width: 650px; width: 100%; max-height: 85vh; display: flex; flex-direction: column; box-shadow: var(--shadow-admin); overflow: hidden; transform: scale(0.95); transition: transform 0.25s ease;">
            <!-- Modal Header -->
            <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--border-admin); display: flex; justify-content: space-between; align-items: flex-start;">
                <div>
                    <h4 style="font-weight: 700; font-size: 1.1rem; color: var(--text-admin-primary); margin: 0; display: flex; align-items: center; gap: 0.5rem;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                        Riwayat Kunjungan Pengunjung
                    </h4>
                    <div style="display: flex; align-items: center; gap: 0.6rem; margin-top: 0.5rem; font-size: 0.85rem;">
                        <code id="modalIpAddress" style="background: rgba(255,255,255,0.06); padding: 0.15rem 0.5rem; border-radius: 4px; font-family: monospace; color: var(--accent-blue);"></code>
                        <span id="modalLocationBadge" style="color: var(--text-admin-secondary);"></span>
                    </div>
                </div>
                <button type="button" onclick="closeVisitorLogsModal()" style="background: none; border: none; cursor: pointer; color: var(--text-admin-secondary); padding: 0.25rem; display: flex; align-items: center;" aria-label="Tutup">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                </button>
            </div>

            <!-- Modal Content (Scrollable List) -->
            <div id="modalLogsBody" style="padding: 1.5rem; overflow-y: auto; flex: 1;">
                <div id="modalLogsLoading" style="text-align: center; padding: 2.5rem 0; color: var(--text-admin-secondary);">
                    <svg style="animation: spin 1s linear infinite; width: 28px; height: 28px; margin: 0 auto 0.75rem auto; display: block;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10" stroke-opacity="0.25"></circle>
                        <path d="M12 2a10 10 0 0 1 10 10" stroke="var(--accent-blue)"></path>
                    </svg>
                    Memuat riwayat halaman...
                </div>
                <div id="modalLogsList" style="display: none;"></div>
            </div>

            <!-- Modal Footer -->
            <div style="padding: 1rem 1.5rem; background: rgba(0,0,0,0.15); border-top: 1px solid var(--border-admin); display: flex; justify-content: space-between; align-items: center;">
                <span id="modalTotalLogsCount" style="font-size: 0.8rem; color: var(--text-admin-muted);"></span>
                <button type="button" class="btn-admin btn-admin-secondary" onclick="closeVisitorLogsModal()">Tutup</button>
            </div>
        </div>
    </div>

    <style>
        @keyframes spin { 100% { transform: rotate(360deg); } }
    </style>

    <script>
        function openVisitorLogsModal(ip) {
            const modal = document.getElementById('visitorLogsModal');
            const container = modal.querySelector('div');
            const loading = document.getElementById('modalLogsLoading');
            const list = document.getElementById('modalLogsList');
            const ipBadge = document.getElementById('modalIpAddress');
            const locBadge = document.getElementById('modalLocationBadge');
            const totalCount = document.getElementById('modalTotalLogsCount');

            ipBadge.textContent = ip;
            locBadge.textContent = 'Memuat lokasi...';
            loading.style.display = 'block';
            list.style.display = 'none';
            totalCount.textContent = '';

            modal.style.display = 'flex';
            setTimeout(() => {
                modal.style.opacity = '1';
                container.style.transform = 'scale(1)';
            }, 10);

            // Fetch visitor logs via AJAX
            fetch(`{{ route('admin.reports.visitor-logs') }}?ip=${encodeURIComponent(ip)}`)
                .then(res => res.json())
                .then(data => {
                    loading.style.display = 'none';
                    list.style.display = 'block';

                    locBadge.innerHTML = `${data.flag || '🌐'} ${data.location || 'Lokasi tidak diketahui'}`;
                    totalCount.textContent = `Total riwayat terekam: ${data.total_logs} kunjungan`;

                    if (!data.logs || data.logs.length === 0) {
                        list.innerHTML = `
                            <div style="text-align: center; padding: 2rem 1rem; color: var(--text-admin-secondary);">
                                <p style="margin: 0;">Belum ada log halaman spesifik yang terekam untuk alamat IP ini.</p>
                                <span style="font-size: 0.75rem; color: var(--text-admin-muted); display: block; margin-top: 0.5rem;">
                                    (Pencatatan rincian URL dimulai sejak fitur ini diaktifkan)
                                </span>
                            </div>
                        `;
                        return;
                    }

                    let html = '<div style="display: flex; flex-direction: column; gap: 0.75rem;">';
                    data.logs.forEach((item, idx) => {
                        html += `
                            <div style="padding: 0.85rem 1rem; background: rgba(255,255,255,0.025); border: 1px solid var(--border-admin); border-radius: var(--radius-sm); display: flex; justify-content: space-between; align-items: center; gap: 1rem;">
                                <div style="overflow: hidden;">
                                    <div style="font-weight: 600; font-size: 0.9rem; color: var(--text-admin-primary); margin-bottom: 0.2rem; display: flex; align-items: center; gap: 0.4rem;">
                                        <span class="badge-admin badge-blue" style="font-size: 0.7rem; font-family: monospace;">#${idx + 1}</span>
                                        <span style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">${item.page_name}</span>
                                    </div>
                                    <a href="${item.url}" target="_blank" style="color: var(--accent-blue); font-size: 0.8rem; font-family: monospace; text-decoration: none; word-break: break-all;">
                                        ${item.url}
                                    </a>
                                </div>
                                <div style="text-align: right; flex-shrink: 0;">
                                    <div style="font-size: 0.8rem; font-weight: 600; color: var(--text-admin-secondary);">${item.time}</div>
                                    <div style="font-size: 0.7rem; color: var(--text-admin-muted);">${item.time_ago}</div>
                                </div>
                            </div>
                        `;
                    });
                    html += '</div>';
                    list.innerHTML = html;
                })
                .catch(err => {
                    loading.style.display = 'none';
                    list.style.display = 'block';
                    list.innerHTML = `
                        <div style="text-align: center; padding: 2rem 1rem; color: #ef4444;">
                            Gagal memuat riwayat pengunjung. Silakan coba lagi.
                        </div>
                    `;
                });
        }

        function closeVisitorLogsModal() {
            const modal = document.getElementById('visitorLogsModal');
            const container = modal.querySelector('div');
            modal.style.opacity = '0';
            container.style.transform = 'scale(0.95)';
            setTimeout(() => {
                modal.style.display = 'none';
            }, 250);
        }

        // Close on ESC key or clicking backdrop
        window.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closeVisitorLogsModal();
        });

        document.getElementById('visitorLogsModal').addEventListener('click', function(e) {
            if (e.target === this) closeVisitorLogsModal();
        });
    </script>
@endsection
