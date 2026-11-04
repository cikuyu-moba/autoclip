@extends('layouts.app')

@section('title', 'Download ' . $software->title . ' v' . $software->version . ' Terbaru Gratis')

@section('meta_tags')
    <meta name="description" content="Download {{ $software->title }} v{{ $software->version }} gratis untuk PC {{ $software->os_compatibility }}. {{ Str::limit(strip_tags($software->description), 140) }}">
    <meta property="og:title" content="Download {{ $software->title }} v{{ $software->version }} Gratis Terbaru">
    <meta property="og:description" content="{{ Str::limit(strip_tags($software->description), 150) }}">
    <meta property="og:image" content="{{ $software->icon_url }}">
    <meta property="og:type" content="article">
@endsection

@section('content')
    <section class="section-container" style="max-width: 1050px; margin: 0 auto; padding-bottom: 4rem;">
        
        <!-- Breadcrumbs -->
        <nav style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.85rem; color: var(--text-muted); margin-bottom: 2rem; flex-wrap: wrap;">
            <a href="{{ route('home') }}" style="color: var(--text-secondary); text-decoration: none;">Beranda</a>
            <span>/</span>
            <a href="{{ route('software.index') }}" style="color: var(--text-secondary); text-decoration: none;">Software</a>
            @if($software->category)
                <span>/</span>
                <a href="{{ route('software.index', ['category' => $software->category->slug]) }}" style="color: var(--text-secondary); text-decoration: none;">
                    {{ $software->category->name }}
                </a>
            @endif
            <span>/</span>
            <span style="color: var(--accent-sky); font-weight: 600;">{{ $software->title }}</span>
        </nav>

        <!-- Flash Success / Error Messages -->
        @if(session('success'))
            <div style="background-color: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.3); color: #10b981; padding: 1rem 1.25rem; border-radius: var(--radius-md); font-size: 0.925rem; font-weight: 600; margin-bottom: 2rem; display: flex; align-items: center; gap: 0.75rem;">
                <span style="font-size: 1.2rem;">✓</span>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div style="background-color: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.3); color: #ef4444; padding: 1rem 1.25rem; border-radius: var(--radius-md); font-size: 0.925rem; font-weight: 600; margin-bottom: 2rem; display: flex; align-items: center; gap: 0.75rem;">
                <span style="font-size: 1.2rem;">⚠️</span>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <!-- Main Software Hero Card -->
        <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-lg); padding: 2.25rem; box-shadow: var(--shadow-sm); margin-bottom: 2.5rem;">
            <div style="display: flex; gap: 2rem; align-items: flex-start; flex-wrap: wrap;">
                
                <!-- Icon Box -->
                <div style="width: 100px; height: 100px; border-radius: 20px; border: 1px solid var(--border-color); background: #ffffff; display: flex; align-items: center; justify-content: center; overflow: hidden; padding: 12px; box-shadow: var(--shadow-md); flex-shrink: 0;">
                    <img src="{{ $software->icon_url }}" alt="{{ $software->title }} logo" style="width: 100%; height: 100%; object-fit: contain;">
                </div>

                <!-- Info Column -->
                <div style="flex: 1; min-width: 280px;">
                    <div style="display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap; margin-bottom: 0.4rem;">
                        <h1 style="font-size: 2rem; font-weight: 800; color: var(--text-primary); line-height: 1.2;">
                            {{ $software->title }}
                        </h1>
                        <span style="background: rgba(15, 23, 42, 0.06); color: var(--text-primary); font-size: 0.85rem; font-weight: 700; padding: 3px 10px; border-radius: 6px;">
                            v{{ $software->version }}
                        </span>
                        <span style="background: rgba(148, 163, 184, 0.12); color: var(--text-secondary); font-size: 0.85rem; font-weight: 600; padding: 3px 10px; border-radius: 6px;">
                            {{ $software->license }}
                        </span>
                    </div>

                    <div style="font-size: 0.95rem; color: var(--text-secondary); margin-bottom: 1.25rem;">
                        Dikembangkan oleh: <strong>{{ $software->developer ?? 'Komunitas Pengembang' }}</strong>
                        @if($software->category)
                            &bull; Kategori: <strong>{{ $software->category->name }}</strong>
                        @endif
                    </div>

                    <!-- Supported Operating Systems Selector / Switcher -->
                    <div style="margin-bottom: 1.25rem;">
                        <div style="font-size: 0.8rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.4rem;">
                            <span>Pilih Sistem Operasi:</span>
                        </div>
                        <div class="os-selector-tabs" style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                            @php
                                $supportedOs = $software->supported_os_list;
                                $firstOs = $supportedOs[0] ?? 'windows';
                            @endphp
                            @foreach($supportedOs as $osKey)
                                @php
                                    $meta = \App\Models\Software::getOsMeta($osKey);
                                    $isFirst = $loop->first;
                                    $osDownloadUrl = route('software.download', ['slug' => $software->slug, 'os' => $osKey]);
                                @endphp
                                <button type="button" 
                                        onclick="selectSoftwareOs('{{ $osKey }}', '{{ $osDownloadUrl }}', '{{ $meta['label'] }}', '{{ $meta['icon'] }}')"
                                        id="os-tab-btn-{{ $osKey }}"
                                        class="os-detail-tab-btn {{ $isFirst ? 'active' : '' }}"
                                        style="padding: 0.55rem 1.05rem; border-radius: 8px; font-size: 0.85rem; font-weight: 700; display: inline-flex; align-items: center; gap: 0.45rem; cursor: pointer; border: 1px solid {{ $isFirst ? 'var(--accent-sky)' : 'var(--border-color)' }}; background: {{ $isFirst ? 'rgba(2, 132, 199, 0.12)' : 'var(--bg-card)' }}; color: {{ $isFirst ? 'var(--accent-sky)' : 'var(--text-primary)' }}; box-shadow: var(--shadow-sm); transition: all 0.2s;">
                                    <span style="font-size: 1.05rem;">{{ $meta['icon'] }}</span>
                                    <span>{{ $meta['label'] }}</span>
                                    @if($isFirst)
                                        <span style="font-size: 0.65rem; background: var(--accent-sky); color: #fff; padding: 1px 6px; border-radius: 4px; font-weight: 800;">REKOMENDASI</span>
                                    @endif
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <!-- CTA Buttons -->
                    <div style="display: flex; gap: 0.85rem; align-items: center; flex-wrap: wrap;">
                        @php
                            $initialMeta = \App\Models\Software::getOsMeta($firstOs);
                        @endphp
                        <a href="{{ route('software.download', ['slug' => $software->slug, 'os' => $firstOs]) }}" 
                           id="mainDownloadBtn" 
                           class="btn btn-primary" 
                           style="padding: 0.85rem 1.8rem; font-size: 1rem; font-weight: 700; display: inline-flex; align-items: center; gap: 0.5rem; border-radius: var(--radius-md); box-shadow: 0 4px 14px rgba(2, 132, 199, 0.35);">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="width: 18px; height: 18px;"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                            <span id="mainDownloadBtnText">Download untuk {{ $initialMeta['label'] }} ({{ $software->file_size ?? 'Resmi' }})</span>
                        </a>

                        @if($software->official_website)
                            <a href="{{ $software->official_website }}" target="_blank" rel="noopener noreferrer" class="btn btn-secondary" style="padding: 0.85rem 1.25rem; font-size: 0.9rem; display: inline-flex; align-items: center; gap: 0.4rem; border-radius: var(--radius-md);">
                                Website Resmi
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 14px; height: 14px;"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
                            </a>
                        @endif

                        <!-- Report Broken Link Button -->
                        <button type="button" onclick="openReportModal()" class="btn btn-secondary" style="padding: 0.85rem 1.15rem; font-size: 0.875rem; color: #ef4444; border-color: rgba(239, 68, 68, 0.25); display: inline-flex; align-items: center; gap: 0.4rem; border-radius: var(--radius-md);" title="Laporkan jika link download ini mati atau error">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 15px; height: 15px;"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
                            Laporkan Link Rusak
                        </button>
                    </div>
                </div>
            </div>

            <!-- Trust / Security Callout & Real Counters -->
            <div style="margin-top: 1.75rem; padding-top: 1.5rem; border-top: 1px solid var(--border-color); display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
                <div style="display: flex; align-items: center; gap: 0.5rem; color: var(--accent-emerald); font-size: 0.875rem; font-weight: 600;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="width: 18px; height: 18px;"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                    100% Aman &amp; Terverifikasi &mdash; Link Installer Resmi Bebas Malware
                </div>

                <div style="display: flex; align-items: center; gap: 1.25rem; font-size: 0.825rem; color: var(--text-muted);">
                    <span title="Dihitung secara realtime dari klik unduhan di web ini">📥 <strong>{{ number_format($software->downloads_count) }}</strong> Total Unduhan</span>
                    <span title="Dihitung secara realtime dari kunjungan halaman ini">👁️ <strong>{{ number_format($software->views_count) }}</strong> Kali Dilihat</span>
                    <span>🕒 Diperbarui {{ $software->updated_at->diffForHumans() }}</span>
                </div>
            </div>
        </div>

        <!-- Content Grid: Left Details & Right Specifications -->
        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem; align-items: start;">
            
            <!-- Left Column: Description, Instructions & Discussion/Comments -->
            <div style="display: flex; flex-direction: column; gap: 2rem;">
                <!-- Description Card -->
                <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 2rem; box-shadow: var(--shadow-sm);">
                    <h2 style="font-size: 1.35rem; font-weight: 700; color: var(--text-primary); margin-bottom: 1.25rem; padding-bottom: 0.75rem; border-bottom: 2px solid var(--accent-sky); display: inline-block;">
                        Tentang {{ $software->title }}
                    </h2>
                    
                    <p style="font-size: 1rem; color: var(--text-secondary); line-height: 1.7; margin-bottom: 1.5rem;">
                        {{ $software->description }}
                    </p>

                    @if(!empty($software->features))
                        <div class="software-content-body" style="font-size: 0.95rem; color: var(--text-secondary); line-height: 1.7;">
                            {!! $software->features !!}
                        </div>
                    @endif
                </div>

                <!-- Safe Download Instructions Card -->
                <div style="background: rgba(2, 132, 199, 0.04); border: 1px dashed rgba(2, 132, 199, 0.25); border-radius: var(--radius-md); padding: 1.75rem;">
                    <h4 style="font-size: 1.1rem; font-weight: 700; color: var(--text-primary); margin-bottom: 0.75rem;">
                        💡 Panduan Cara Download &amp; Install
                    </h4>
                    <ol style="margin-left: 1.25rem; color: var(--text-secondary); font-size: 0.9rem; line-height: 1.6;">
                        <li>Klik tombol <strong>"Download Sekarang"</strong> di atas.</li>
                        <li>Proses download file installer resmi akan berjalan langsung dari server rilis.</li>
                        <li>Buka file installer yang telah selesai diunduh di folder <em>Downloads</em> PC Anda.</li>
                        <li>Ikuti petunjuk instalasi di layar (*Next*, setujui lisensi, dan *Finish*).</li>
                        <li>Software siap langsung digunakan tanpa perlu lisensi berbayar!</li>
                    </ol>
                </div>

                <!-- Discussion & Software Request Section -->
                <div class="software-discussion-card" id="discussion-section" style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 2rem; box-shadow: var(--shadow-sm);">
                    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.75rem; padding-bottom: 1rem; border-bottom: 1px solid var(--border-color);">
                        <div>
                            <h3 style="font-size: 1.3rem; font-weight: 700; color: var(--text-primary); margin: 0; display: flex; align-items: center; gap: 0.5rem;">
                                <span>💬</span> Diskusi &amp; Request Software
                            </h3>
                            <p style="font-size: 0.85rem; color: var(--text-muted); margin: 0.25rem 0 0 0;">
                                Beri ulasan, ajukan pertanyaan, atau ajukan permohonan penambahan software baru.
                            </p>
                        </div>
                        <span style="background: rgba(2, 132, 199, 0.1); color: var(--accent-sky); font-weight: 700; font-size: 0.85rem; padding: 4px 12px; border-radius: 20px;">
                            {{ $software->comments->count() }} Diskusi
                        </span>
                    </div>

                    <!-- Comment Submission Form -->
                    <form action="{{ route('software.comments.store', $software->id) }}" method="POST" style="margin-bottom: 2.5rem; background: rgba(148, 163, 184, 0.04); padding: 1.5rem; border-radius: var(--radius-sm); border: 1px solid var(--border-color);">
                        @csrf
                        
                        <!-- Type Selector: Diskusi vs Request -->
                        <div style="display: flex; gap: 1.5rem; align-items: center; margin-bottom: 1.25rem; flex-wrap: wrap;">
                            <span style="font-size: 0.875rem; font-weight: 700; color: var(--text-primary);">Pilih Topik:</span>
                            <label style="display: inline-flex; align-items: center; gap: 0.4rem; cursor: pointer; font-size: 0.875rem; color: var(--text-primary);">
                                <input type="radio" name="type" value="comment" checked style="accent-color: var(--accent-sky);">
                                <span>💬 Komentar / Pertanyaan</span>
                            </label>
                            <label style="display: inline-flex; align-items: center; gap: 0.4rem; cursor: pointer; font-size: 0.875rem; color: var(--text-primary);">
                                <input type="radio" name="type" value="request" style="accent-color: var(--accent-amber);">
                                <span>💡 Request Software / Versi Baru</span>
                            </label>
                        </div>

                        <!-- Username Area (with LocalStorage/Cookie check) -->
                        <div style="margin-bottom: 1.25rem;">
                            <div id="swUsernameInputArea">
                                <label for="swCommentUsername" style="display: block; font-size: 0.85rem; font-weight: 600; color: var(--text-secondary); margin-bottom: 0.4rem;">Nama / Username Anda</label>
                                <input type="text" name="username" id="swCommentUsername" placeholder="Masukkan nama Anda (misal: Budi_Tech)..." style="width: 100%; padding: 0.75rem 1rem; border: 1px solid var(--border-color); border-radius: var(--radius-sm); background: var(--bg-card); color: var(--text-primary); font-size: 0.9rem; transition: border-color 0.2s;" onfocus="this.style.borderColor='var(--accent-sky)'" onblur="this.style.borderColor='var(--border-color)'">
                            </div>

                            <div id="swUsernameGreetingArea" style="display: none; background: rgba(56, 189, 248, 0.05); border: 1px solid rgba(56, 189, 248, 0.15); padding: 0.75rem 1rem; border-radius: var(--radius-sm); align-items: center; justify-content: space-between; gap: 1rem;">
                                <span style="font-size: 0.875rem; color: var(--text-secondary);">
                                    Berkomentar sebagai: <strong id="swActiveUsernameDisplay" style="color: var(--accent-sky);"></strong>
                                </span>
                                <button type="button" onclick="changeSwCommenterUsername()" style="background: none; border: none; padding: 0; color: var(--accent-amber); font-weight: 700; font-size: 0.8rem; cursor: pointer; text-decoration: underline;">
                                    Ganti Nama
                                </button>
                            </div>
                        </div>

                        <!-- Message Textarea -->
                        <div style="margin-bottom: 1.25rem;">
                            <label for="swCommentContent" style="display: block; font-size: 0.85rem; font-weight: 600; color: var(--text-secondary); margin-bottom: 0.4rem;">Pesan / Pertanyaan / Request</label>
                            <textarea name="content" id="swCommentContent" rows="4" placeholder="Tuliskan komentar mengenai software ini, atau request software lain yang Anda inginkan..." style="width: 100%; padding: 0.75rem 1rem; border: 1px solid var(--border-color); border-radius: var(--radius-sm); background: var(--bg-card); color: var(--text-primary); font-size: 0.9rem; line-height: 1.5; resize: vertical;" required></textarea>
                        </div>

                        <div style="display: flex; justify-content: flex-end;">
                            <button type="submit" class="btn btn-primary" style="padding: 0.75rem 1.75rem; font-weight: 700; font-size: 0.95rem; border-radius: var(--radius-sm); display: inline-flex; align-items: center; gap: 0.4rem;">
                                <span>Kirim Pesan</span>
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 16px; height: 16px;"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg>
                            </button>
                        </div>
                    </form>

                    <!-- Comments & Requests List -->
                    <div class="software-comments-list" style="display: flex; flex-direction: column; gap: 1.5rem;">
                        @forelse($software->comments as $comment)
                            <div class="sw-comment-box" id="comment-{{ $comment->id }}" style="padding-bottom: 1.5rem; border-bottom: 1px solid var(--border-color);">
                                <div style="display: flex; gap: 1rem; align-items: flex-start;">
                                    
                                    <!-- Avatar -->
                                    <div style="width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1rem; font-weight: 800; flex-shrink: 0; background: {{ $comment->user_id ? 'linear-gradient(135deg, var(--accent-sky), #0284c7)' : 'rgba(148, 163, 184, 0.15)' }}; color: {{ $comment->user_id ? '#fff' : 'var(--text-primary)' }}; border: 1px solid {{ $comment->user_id ? 'var(--accent-sky)' : 'var(--border-color)' }};">
                                        {{ strtoupper(substr($comment->username, 0, 1)) }}
                                    </div>

                                    <div style="flex: 1; min-width: 0;">
                                        <!-- Header row -->
                                        <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap; margin-bottom: 0.4rem;">
                                            <strong style="font-size: 0.95rem; color: var(--text-primary);">{{ $comment->username }}</strong>
                                            
                                            @if($comment->user_id)
                                                <span style="font-size: 0.7rem; background-color: rgba(2, 132, 199, 0.15); color: var(--accent-sky); padding: 1px 7px; border-radius: 4px; font-weight: 800; text-transform: uppercase;">
                                                    Staff Arhana
                                                </span>
                                            @endif

                                            @if($comment->type === 'request')
                                                <span style="font-size: 0.7rem; background-color: rgba(245, 158, 11, 0.15); color: #d97706; padding: 1px 7px; border-radius: 4px; font-weight: 700;">
                                                    💡 Request Software
                                                </span>
                                            @endif

                                            <span style="font-size: 0.75rem; color: var(--text-muted);">&bull;</span>
                                            <span style="font-size: 0.75rem; color: var(--text-muted);">{{ $comment->created_at->diffForHumans() }}</span>
                                        </div>

                                        <!-- Content -->
                                        <div style="font-size: 0.925rem; color: var(--text-secondary); line-height: 1.6; margin-bottom: 0.6rem; white-space: pre-wrap;">{{ $comment->content }}</div>

                                        <!-- Reply Toggle Button -->
                                        <button type="button" onclick="toggleSwReplyBox({{ $comment->id }})" style="background: none; border: none; padding: 0; color: var(--accent-sky); font-weight: 600; font-size: 0.8rem; cursor: pointer; display: inline-flex; align-items: center; gap: 0.25rem;">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="width: 13px; height: 13px;"><polyline points="9 17 4 12 9 7"></polyline><path d="M20 18v-2a4 4 0 0 0-4-4H4"></path></svg>
                                            Balas
                                        </button>

                                        <!-- Inline Reply Form Box -->
                                        <div id="sw-reply-box-{{ $comment->id }}" style="display: none; margin-top: 1rem; padding: 1rem; border-radius: var(--radius-sm); border: 1px solid var(--border-color); background: rgba(148, 163, 184, 0.03);">
                                            <form action="{{ route('software.comments.store', $software->id) }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                                                <input type="hidden" name="type" value="comment">
                                                
                                                <div style="margin-bottom: 0.75rem;">
                                                    <input type="text" name="username" class="sw-reply-username-input" placeholder="Nama Anda..." style="width: 100%; padding: 0.6rem 0.85rem; border: 1px solid var(--border-color); border-radius: var(--radius-sm); background: var(--bg-card); color: var(--text-primary); font-size: 0.85rem; margin-bottom: 0.5rem;">
                                                    <textarea name="content" rows="3" placeholder="Tulis balasan Anda..." style="width: 100%; padding: 0.6rem 0.85rem; border: 1px solid var(--border-color); border-radius: var(--radius-sm); background: var(--bg-card); color: var(--text-primary); font-size: 0.85rem; line-height: 1.5; resize: vertical;" required></textarea>
                                                </div>

                                                <div style="display: flex; gap: 0.5rem; justify-content: flex-end;">
                                                    <button type="button" class="btn btn-secondary btn-sm" onclick="toggleSwReplyBox({{ $comment->id }})" style="padding: 0.4rem 0.85rem; font-size: 0.8rem;">Batal</button>
                                                    <button type="submit" class="btn btn-primary btn-sm" style="padding: 0.4rem 1rem; font-size: 0.8rem;">Kirim Balasan</button>
                                                </div>
                                            </form>
                                        </div>

                                        <!-- Nested Replies -->
                                        @if($comment->replies && $comment->replies->count() > 0)
                                            <div style="margin-top: 1.25rem; padding-left: 1.25rem; border-left: 2px solid var(--border-color); display: flex; flex-direction: column; gap: 1.25rem;">
                                                @foreach($comment->replies as $reply)
                                                    <div style="display: flex; gap: 0.75rem; align-items: flex-start;">
                                                        <div style="width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.85rem; font-weight: 800; flex-shrink: 0; background: {{ $reply->user_id ? 'linear-gradient(135deg, var(--accent-sky), #0284c7)' : 'rgba(148, 163, 184, 0.15)' }}; color: {{ $reply->user_id ? '#fff' : 'var(--text-primary)' }}; border: 1px solid {{ $reply->user_id ? 'var(--accent-sky)' : 'var(--border-color)' }};">
                                                            {{ strtoupper(substr($reply->username, 0, 1)) }}
                                                        </div>
                                                        <div style="flex: 1; min-width: 0;">
                                                            <div style="display: flex; align-items: center; gap: 0.4rem; flex-wrap: wrap; margin-bottom: 0.25rem;">
                                                                <strong style="font-size: 0.875rem; color: var(--text-primary);">{{ $reply->username }}</strong>
                                                                @if($reply->user_id)
                                                                    <span style="font-size: 0.65rem; background-color: rgba(2, 132, 199, 0.15); color: var(--accent-sky); padding: 1px 6px; border-radius: 4px; font-weight: 800; text-transform: uppercase;">Staff Arhana</span>
                                                                @endif
                                                                <span style="font-size: 0.7rem; color: var(--text-muted);">&bull;</span>
                                                                <span style="font-size: 0.7rem; color: var(--text-muted);">{{ $reply->created_at->diffForHumans() }}</span>
                                                            </div>
                                                            <div style="font-size: 0.875rem; color: var(--text-secondary); line-height: 1.5; white-space: pre-wrap;">{{ $reply->content }}</div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif

                                    </div>
                                </div>
                            </div>
                        @empty
                            <div style="text-align: center; padding: 3rem 1rem; color: var(--text-muted); font-size: 0.95rem;">
                                <div style="font-size: 2rem; margin-bottom: 0.5rem;">💬</div>
                                <div style="font-weight: 600; color: var(--text-primary); margin-bottom: 0.25rem;">Belum ada komentar atau request software.</div>
                                <p style="font-size: 0.85rem; margin: 0;">Jadilah yang pertama untuk mendiskusikan atau meminta software favorit Anda!</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Right Column: Specs Table & Sidebar -->
            <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                
                <!-- Specs Card -->
                <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 1.5rem; box-shadow: var(--shadow-sm);">
                    <h3 style="font-size: 1.1rem; font-weight: 700; color: var(--text-primary); margin-bottom: 1.25rem; padding-bottom: 0.5rem; border-bottom: 1px solid var(--border-color);">
                        Informasi Teknis
                    </h3>

                    <table style="width: 100%; border-collapse: collapse; font-size: 0.875rem;">
                        <tbody>
                            <tr style="border-bottom: 1px solid var(--border-color);">
                                <td style="padding: 0.65rem 0; color: var(--text-muted);">Nama:</td>
                                <td style="padding: 0.65rem 0; font-weight: 600; text-align: right; color: var(--text-primary);">{{ $software->title }}</td>
                            </tr>
                            <tr style="border-bottom: 1px solid var(--border-color);">
                                <td style="padding: 0.65rem 0; color: var(--text-muted);">Versi Rilis:</td>
                                <td style="padding: 0.65rem 0; font-weight: 700; text-align: right; color: var(--accent-sky);">v{{ $software->version }}</td>
                            </tr>
                            <tr style="border-bottom: 1px solid var(--border-color);">
                                <td style="padding: 0.65rem 0; color: var(--text-muted);">Ukuran File:</td>
                                <td style="padding: 0.65rem 0; font-weight: 600; text-align: right; color: var(--text-primary);">{{ $software->file_size ?? '-' }}</td>
                            </tr>
                            <tr style="border-bottom: 1px solid var(--border-color);">
                                <td style="padding: 0.65rem 0; color: var(--text-muted);">Lisensi:</td>
                                <td style="padding: 0.65rem 0; font-weight: 600; text-align: right; color: var(--accent-emerald);">{{ $software->license }}</td>
                            </tr>
                            <tr style="border-bottom: 1px solid var(--border-color);">
                                <td style="padding: 0.65rem 0; color: var(--text-muted); vertical-align: top;">Platform:</td>
                                <td style="padding: 0.65rem 0; text-align: right;">
                                    <div style="display: flex; flex-wrap: wrap; gap: 4px; justify-content: flex-end; margin-bottom: 0.25rem;">
                                        @foreach($software->supported_os_list as $osSlug)
                                            @php $meta = \App\Models\Software::getOsMeta($osSlug); @endphp
                                            <span style="background: {{ $meta['badge_bg'] }}; color: {{ $meta['badge_color'] }}; font-size: 0.725rem; font-weight: 700; padding: 2px 6px; border-radius: 4px; display: inline-flex; align-items: center; gap: 2px;">
                                                <span>{{ $meta['icon'] }}</span>
                                                <span>{{ $meta['label'] }}</span>
                                            </span>
                                        @endforeach
                                    </div>
                                    <div style="font-size: 0.75rem; color: var(--text-muted);">
                                        {{ $software->os_compatibility }}
                                    </div>
                                </td>
                            </tr>
                            <tr style="border-bottom: 1px solid var(--border-color);">
                                <td style="padding: 0.65rem 0; color: var(--text-muted);">Pengembang:</td>
                                <td style="padding: 0.65rem 0; font-weight: 600; text-align: right; color: var(--text-primary);">{{ $software->developer ?? 'Komunitas' }}</td>
                            </tr>
                            <tr>
                                <td style="padding: 0.65rem 0; color: var(--text-muted);">Terakhir Update:</td>
                                <td style="padding: 0.65rem 0; font-weight: 600; text-align: right; color: var(--text-primary);">{{ $software->updated_at->format('d M Y') }}</td>
                            </tr>
                        </tbody>
                    </table>

                    <!-- OS Multi-Download Links Box (if multiple platforms supported) -->
                    @if(count($software->supported_os_list) > 1)
                        <div style="margin-top: 1.25rem; padding-top: 1rem; border-top: 1px solid var(--border-color);">
                            <div style="font-size: 0.785rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 0.6rem; letter-spacing: 0.5px;">
                                Unduh untuk OS Lainnya:
                            </div>
                            <div style="display: flex; flex-direction: column; gap: 0.45rem;">
                                @foreach($software->supported_os_list as $osSlug)
                                    @php $meta = \App\Models\Software::getOsMeta($osSlug); @endphp
                                    <a href="{{ route('software.download', ['slug' => $software->slug, 'os' => $osSlug]) }}" 
                                       style="display: flex; align-items: center; justify-content: space-between; text-decoration: none; padding: 0.5rem 0.75rem; background: var(--bg-secondary); border: 1px solid var(--border-color); border-radius: 6px; font-size: 0.825rem; font-weight: 600; color: var(--text-primary); transition: all 0.2s;"
                                       onmouseenter="this.style.borderColor='var(--accent-sky)'; this.style.color='var(--accent-sky)'"
                                       onmouseleave="this.style.borderColor='var(--border-color)'; this.style.color='var(--text-primary)'">
                                        <span style="display: flex; align-items: center; gap: 0.4rem;">
                                            <span>{{ $meta['icon'] }}</span>
                                            <span>Versi {{ $meta['label'] }}</span>
                                        </span>
                                        <span style="font-size: 0.75rem; color: var(--accent-sky); font-weight: 700;">Unduh &rarr;</span>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div style="margin-top: 1.25rem; display: flex; flex-direction: column; gap: 0.5rem;">
                        <button type="button" onclick="openReportModal()" style="background: none; border: none; padding: 0.4rem; font-size: 0.8rem; color: #ef4444; cursor: pointer; text-decoration: underline; text-align: center;">
                            ⚠️ Link download bermasalah? Laporkan di sini
                        </button>
                    </div>
                </div>

                <!-- Related Software Recommendations -->
                @if(isset($relatedSoftwares) && $relatedSoftwares->count() > 0)
                    <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 1.5rem; box-shadow: var(--shadow-sm);">
                        <h4 style="font-size: 1rem; font-weight: 700; color: var(--text-primary); margin-bottom: 1rem; padding-bottom: 0.5rem; border-bottom: 1px solid var(--border-color);">
                            Software Terkait
                        </h4>

                        <div style="display: flex; flex-direction: column; gap: 0.85rem;">
                            @foreach($relatedSoftwares as $related)
                                <a href="{{ route('software.show', $related->slug) }}" style="display: flex; align-items: center; gap: 0.75rem; text-decoration: none; padding: 0.5rem; border-radius: var(--radius-sm); transition: background 0.2s;" onmouseenter="this.style.background='var(--bg-card-hover)'" onmouseleave="this.style.background='transparent'">
                                    <div style="width: 36px; height: 36px; border-radius: 6px; border: 1px solid var(--border-color); background: #fff; display: flex; align-items: center; justify-content: center; overflow: hidden; padding: 3px; flex-shrink: 0;">
                                        <img src="{{ $related->icon_url }}" alt="{{ $related->title }}" style="width: 100%; height: 100%; object-fit: contain;">
                                    </div>
                                    <div style="flex: 1; min-width: 0;">
                                        <div style="font-weight: 600; font-size: 0.875rem; color: var(--text-primary); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                            {{ $related->title }}
                                        </div>
                                        <div style="font-size: 0.75rem; color: var(--text-muted);">
                                            v{{ $related->version }} &bull; {{ $related->license }}
                                        </div>
                                    </div>
                                    <span style="color: var(--accent-sky); font-size: 0.8rem; font-weight: 600;">&rarr;</span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

            </div>
        </div>
    </section>

    <!-- Report Broken Link Modal Dialog -->
    <div id="reportBrokenLinkModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.65); backdrop-filter: blur(4px); z-index: 99999; align-items: center; justify-content: center; padding: 1.5rem;">
        <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-lg); max-width: 520px; width: 100%; padding: 2rem; box-shadow: 0 20px 40px rgba(0, 0, 0, 0.35); position: relative; animation: modalFadeIn 0.25s ease-out;">
            
            <!-- Close button -->
            <button type="button" onclick="closeReportModal()" style="position: absolute; top: 1.25rem; right: 1.25rem; background: none; border: none; font-size: 1.5rem; color: var(--text-muted); cursor: pointer; line-height: 1;" aria-label="Tutup">&times;</button>
            
            <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1rem;">
                <div style="width: 40px; height: 40px; border-radius: 10px; background: rgba(239, 68, 68, 0.12); color: #ef4444; display: flex; align-items: center; justify-content: center;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="width: 22px; height: 22px;"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
                </div>
                <div>
                    <h3 style="font-size: 1.2rem; font-weight: 700; color: var(--text-primary); margin: 0;">Laporkan Link Rusak</h3>
                    <p style="font-size: 0.825rem; color: var(--text-muted); margin: 0.15rem 0 0 0;">{{ $software->title }} v{{ $software->version }}</p>
                </div>
            </div>

            <form action="{{ route('software.report', $software->id) }}" method="POST">
                @csrf
                <div style="margin-bottom: 1rem;">
                    <label style="display: block; font-size: 0.85rem; font-weight: 600; color: var(--text-primary); margin-bottom: 0.4rem;">Versi OS yang Bermasalah:</label>
                    <select name="os" style="width: 100%; padding: 0.6rem 0.85rem; border: 1px solid var(--border-color); border-radius: var(--radius-sm); background: var(--bg-card); color: var(--text-primary); font-size: 0.85rem;">
                        @foreach($software->supported_os_list as $osKey)
                            @php $meta = \App\Models\Software::getOsMeta($osKey); @endphp
                            <option value="{{ $osKey }}">Versi {{ $meta['label'] }} ({{ $meta['icon'] }})</option>
                        @endforeach
                    </select>
                </div>

                <div style="margin-bottom: 1.25rem;">
                    <label style="display: block; font-size: 0.85rem; font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem;">Kendala yang Anda alami:</label>
                    <div style="display: flex; flex-direction: column; gap: 0.5rem; font-size: 0.875rem; color: var(--text-secondary);">
                        <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                            <input type="radio" name="reason" value="Link 404 / Halaman Not Found" checked style="accent-color: #ef4444;">
                            <span>Link 404 / Halaman Download Tidak Ditemukan</span>
                        </label>
                        <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                            <input type="radio" name="reason" value="Download Gagal / File Rusak" style="accent-color: #ef4444;">
                            <span>Download Gagal / File Rusak (Corrupt)</span>
                        </label>
                        <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                            <input type="radio" name="reason" value="Versi Sudah Usang (Outdated)" style="accent-color: #ef4444;">
                            <span>Versi Rilis Sudah Ketinggalan Jaman (Ada versi baru)</span>
                        </label>
                        <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                            <input type="radio" name="reason" value="Link Mengarah ke Web Lain / Iklan" style="accent-color: #ef4444;">
                            <span>Link Mengarah ke Website Lain / Halaman Iklan</span>
                        </label>
                        <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                            <input type="radio" name="reason" value="Lainnya" style="accent-color: #ef4444;">
                            <span>Kendala Lainnya</span>
                        </label>
                    </div>
                </div>

                <div style="margin-bottom: 1.5rem;">
                    <label for="reportNote" style="display: block; font-size: 0.85rem; font-weight: 600; color: var(--text-primary); margin-bottom: 0.4rem;">Catatan Tambahan (Opsional)</label>
                    <textarea name="note" id="reportNote" rows="3" placeholder="Jelaskan pesan error atau berikan tautan versi terbaru jika Anda mengetahuinya..." style="width: 100%; padding: 0.75rem; border: 1px solid var(--border-color); border-radius: var(--radius-sm); background: var(--bg-card); color: var(--text-primary); font-size: 0.875rem; line-height: 1.4; resize: vertical;"></textarea>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 0.75rem;">
                    <button type="button" class="btn btn-secondary" onclick="closeReportModal()" style="padding: 0.65rem 1.25rem; font-size: 0.9rem;">Batal</button>
                    <button type="submit" class="btn btn-primary" style="padding: 0.65rem 1.5rem; font-size: 0.9rem; font-weight: 700; background: #ef4444; border-color: #ef4444; color: #fff;">
                        Kirim Laporan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Scripts for Comments, Username persistence, and Report Modal -->
    <script>
        function openReportModal() {
            const modal = document.getElementById('reportBrokenLinkModal');
            if (modal) {
                modal.style.display = 'flex';
            }
        }

        function closeReportModal() {
            const modal = document.getElementById('reportBrokenLinkModal');
            if (modal) {
                modal.style.display = 'none';
            }
        }

        // Close on ESC key or backdrop click
        window.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') closeReportModal();
        });

        document.getElementById('reportBrokenLinkModal')?.addEventListener('click', function(e) {
            if (e.target === this) closeReportModal();
        });

        function selectSoftwareOs(osKey, downloadUrl, osLabel, osIcon) {
            document.querySelectorAll('.os-detail-tab-btn').forEach(btn => {
                btn.style.borderColor = 'var(--border-color)';
                btn.style.background = 'var(--bg-card)';
                btn.style.color = 'var(--text-primary)';
            });
            const activeBtn = document.getElementById('os-tab-btn-' + osKey);
            if (activeBtn) {
                activeBtn.style.borderColor = 'var(--accent-sky)';
                activeBtn.style.background = 'rgba(2, 132, 199, 0.12)';
                activeBtn.style.color = 'var(--accent-sky)';
            }

            const mainBtn = document.getElementById('mainDownloadBtn');
            const mainBtnText = document.getElementById('mainDownloadBtnText');
            if (mainBtn && mainBtnText) {
                mainBtn.href = downloadUrl;
                mainBtnText.textContent = 'Download untuk ' + osLabel + ' ({{ $software->file_size ?? 'Resmi' }})';
            }
        }

        // Username persistence
        document.addEventListener('DOMContentLoaded', function () {
            checkSwUsernameCookie();
        });

        function checkSwUsernameCookie() {
            let username = localStorage.getItem('arhana_comment_username');
            if (!username || username.trim() === '') {
                username = getCookieVal('arhana_comment_username');
            }

            const inputArea = document.getElementById('swUsernameInputArea');
            const greetingArea = document.getElementById('swUsernameGreetingArea');
            const displaySpan = document.getElementById('swActiveUsernameDisplay');
            const mainInput = document.getElementById('swCommentUsername');

            if (username && username.trim() !== '') {
                localStorage.setItem('arhana_comment_username', username);
                if (inputArea) inputArea.style.display = 'none';
                if (greetingArea) {
                    greetingArea.style.display = 'flex';
                    if (displaySpan) displaySpan.textContent = username;
                }
                if (mainInput) mainInput.value = username;

                // Sync nested reply inputs
                document.querySelectorAll('.sw-reply-username-input').forEach(input => {
                    input.value = username;
                });
            }
        }

        function changeSwCommenterUsername() {
            localStorage.removeItem('arhana_comment_username');
            document.cookie = 'arhana_comment_username=; Max-Age=0; path=/;';
            const inputArea = document.getElementById('swUsernameInputArea');
            const greetingArea = document.getElementById('swUsernameGreetingArea');
            const mainInput = document.getElementById('swCommentUsername');
            if (greetingArea) greetingArea.style.display = 'none';
            if (inputArea) {
                inputArea.style.display = 'block';
                if (mainInput) {
                    mainInput.value = '';
                    mainInput.focus();
                }
            }
        }

        function toggleSwReplyBox(commentId) {
            const box = document.getElementById('sw-reply-box-' + commentId);
            if (box) {
                box.style.display = (box.style.display === 'none' || box.style.display === '') ? 'block' : 'none';
            }
        }

        function getCookieVal(name) {
            const match = document.cookie.match(new RegExp('(^|;\\s*)(' + name + ')=([^;]*)'));
            return match ? decodeURIComponent(match[3]) : null;
        }
    </script>

    <!-- Additional styling for content body and animation -->
    <style>
        .software-content-body h3 {
            font-size: 1.15rem;
            color: var(--text-primary);
            margin: 1.25rem 0 0.5rem;
        }
        .software-content-body ul {
            margin-left: 1.5rem;
            margin-bottom: 1rem;
        }
        .software-content-body li {
            margin-bottom: 0.35rem;
        }
        @keyframes modalFadeIn {
            from {
                opacity: 0;
                transform: scale(0.96);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }
    </style>
@endsection
