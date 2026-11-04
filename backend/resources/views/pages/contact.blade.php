@extends('layouts.app')

@section('title', 'Kontak Kami (Contact Us)')

@section('meta_tags')
    <meta name="description" content="Hubungi tim Arhana (arhana.my.id). Kirim pertanyaan, masukan, permohonan kerjasama, atau laporan kendala software melalui formulir kontak atau email resmi kami.">
    <link rel="canonical" href="{{ route('contact') }}" />
@endsection

@section('content')
<section class="section-container" style="padding-top: 3.5rem; padding-bottom: 5rem;">
    <div style="max-width: 1000px; margin: 0 auto;">
        
        <!-- Breadcrumb & Header -->
        <div style="margin-bottom: 3rem; text-align: center;">
            <div style="display: inline-flex; align-items: center; gap: 0.5rem; font-size: 0.85rem; color: var(--text-muted); margin-bottom: 1rem;">
                <a href="{{ route('home') }}" style="color: var(--text-secondary); text-decoration: none;">Beranda</a>
                <span>/</span>
                <span style="color: var(--accent-sky); font-weight: 600;">Kontak Kami</span>
            </div>
            
            <span class="section-subtitle">Mari Terhubung</span>
            <h1 style="font-size: 2.75rem; font-weight: 800; color: var(--text-primary); margin-bottom: 1rem; letter-spacing: -0.5px; line-height: 1.2;">
                Hubungi <span>Kami</span>
            </h1>
            <p style="color: var(--text-secondary); font-size: 1.05rem; max-width: 600px; margin: 0 auto; line-height: 1.6;">
                Punya pertanyaan, ide kolaborasi, masukan konten, atau butuh bantuan teknis? Kirimkan pesan Anda melalui formulir di bawah ini.
            </p>
        </div>

        @if(session('success'))
            <div style="background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.25); color: #059669; padding: 1.25rem 1.5rem; border-radius: var(--radius-md); margin-bottom: 2rem; display: flex; align-items: center; gap: 0.75rem; box-shadow: var(--shadow-sm);">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink: 0;"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                <div style="font-weight: 600; font-size: 0.95rem;">{{ session('success') }}</div>
            </div>
        @endif

        @if(session('error'))
            <div style="background: rgba(220, 38, 38, 0.08); border: 1px solid rgba(220, 38, 38, 0.2); color: var(--accent-danger); padding: 1.25rem 1.5rem; border-radius: var(--radius-md); margin-bottom: 2rem; display: flex; align-items: center; gap: 0.75rem;">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink: 0;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                <div style="font-size: 0.95rem;">{{ session('error') }}</div>
            </div>
        @endif

        <!-- Two Column Layout: Info Cards + Form -->
        <div style="display: grid; grid-template-columns: 1fr 1.6fr; gap: 2.5rem; align-items: flex-start;">
            
            <!-- Left Column: Information Cards -->
            <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                
                <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 1.75rem; box-shadow: var(--shadow-sm);">
                    <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem;">
                        <div style="width: 44px; height: 44px; border-radius: 10px; background: rgba(2, 132, 199, 0.1); color: var(--accent-sky); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                        </div>
                        <div>
                            <h3 style="font-size: 1.05rem; font-weight: 700; color: var(--text-primary); margin: 0;">Email Resmi</h3>
                            <span style="font-size: 0.8rem; color: var(--text-muted);">Tanggapan Cepat</span>
                        </div>
                    </div>
                    <p style="color: var(--text-secondary); font-size: 0.9rem; margin: 0 0 0.5rem 0; line-height: 1.5;">
                        Kirimkan pesan langsung ke kotak masuk kami untuk pertanyaan resmi atau kerjasama.
                    </p>
                    <a href="mailto:cikuyu.rc@gmail.com" style="color: var(--accent-sky); font-weight: 700; font-size: 0.95rem; text-decoration: none; word-break: break-all;">
                        cikuyu.rc@gmail.com
                    </a>
                </div>

                <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 1.75rem; box-shadow: var(--shadow-sm);">
                    <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem;">
                        <div style="width: 44px; height: 44px; border-radius: 10px; background: rgba(16, 185, 129, 0.1); color: #10b981; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        </div>
                        <div>
                            <h3 style="font-size: 1.05rem; font-weight: 700; color: var(--text-primary); margin: 0;">Waktu Operasional</h3>
                            <span style="font-size: 0.8rem; color: var(--text-muted);">Senin - Sabtu</span>
                        </div>
                    </div>
                    <p style="color: var(--text-secondary); font-size: 0.9rem; margin: 0; line-height: 1.6;">
                        <strong>08.00 - 17.00 WIB</strong><br>
                        Estimasi waktu respon email maksimal <strong>1 x 24 jam</strong> kerja.
                    </p>
                </div>

                <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 1.75rem; box-shadow: var(--shadow-sm);">
                    <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem;">
                        <div style="width: 44px; height: 44px; border-radius: 10px; background: rgba(245, 158, 11, 0.1); color: var(--accent-amber); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                        </div>
                        <div>
                            <h3 style="font-size: 1.05rem; font-weight: 700; color: var(--text-primary); margin: 0;">Lokasi &amp; Wilayah</h3>
                            <span style="font-size: 0.8rem; color: var(--text-muted);">Indonesia</span>
                        </div>
                    </div>
                    <p style="color: var(--text-secondary); font-size: 0.9rem; margin: 0; line-height: 1.6;">
                        Operasional digital di Indonesia. Melayani pembaca dan pengguna dari seluruh penjuru nusantara.
                    </p>
                </div>

            </div>

            <!-- Right Column: Interactive Contact Form -->
            <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 2.5rem; box-shadow: var(--shadow-sm);">
                <h3 style="font-size: 1.35rem; font-weight: 800; color: var(--text-primary); margin: 0 0 0.5rem 0;">Kirimkan Pesan Anda</h3>
                <p style="color: var(--text-secondary); font-size: 0.9rem; margin: 0 0 1.75rem 0;">Isi formulir di bawah ini dengan lengkap. Semua data dijaga kerahasiaannya sesuai Kebijakan Privasi kami.</p>

                <form action="{{ route('contact.store') }}" method="POST">
                    @csrf

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; margin-bottom: 1.25rem;">
                        <div>
                            <label for="name" style="display: block; font-size: 0.85rem; font-weight: 600; color: var(--text-primary); margin-bottom: 0.4rem;">Nama Lengkap <span style="color: var(--accent-danger);">*</span></label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}" required
                                style="width: 100%; padding: 0.75rem 1rem; font-size: 0.9rem; border-radius: var(--radius-sm); border: 1px solid var(--border-color); background: var(--bg-primary); color: var(--text-primary); outline: none; transition: var(--transition-fast);"
                                placeholder="Contoh: Budi Santoso">
                            @error('name')
                                <span style="color: var(--accent-danger); font-size: 0.8rem; margin-top: 0.25rem; display: block;">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label for="email" style="display: block; font-size: 0.85rem; font-weight: 600; color: var(--text-primary); margin-bottom: 0.4rem;">Alamat Email <span style="color: var(--accent-danger);">*</span></label>
                            <input type="email" name="email" id="email" value="{{ old('email') }}" required
                                style="width: 100%; padding: 0.75rem 1rem; font-size: 0.9rem; border-radius: var(--radius-sm); border: 1px solid var(--border-color); background: var(--bg-primary); color: var(--text-primary); outline: none; transition: var(--transition-fast);"
                                placeholder="nama@email.com">
                            @error('email')
                                <span style="color: var(--accent-danger); font-size: 0.8rem; margin-top: 0.25rem; display: block;">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div style="margin-bottom: 1.25rem;">
                        <label for="subject" style="display: block; font-size: 0.85rem; font-weight: 600; color: var(--text-primary); margin-bottom: 0.4rem;">Subjek / Kategori Pesan <span style="color: var(--accent-danger);">*</span></label>
                        <select name="subject" id="subject" required
                            style="width: 100%; padding: 0.75rem 1rem; font-size: 0.9rem; border-radius: var(--radius-sm); border: 1px solid var(--border-color); background: var(--bg-primary); color: var(--text-primary); outline: none; cursor: pointer; transition: var(--transition-fast);">
                            <option value="" disabled {{ old('subject') ? '' : 'selected' }}>Pilih Kategori Pesan...</option>
                            <option value="Pertanyaan Umum & Informasi" {{ old('subject') == 'Pertanyaan Umum & Informasi' ? 'selected' : '' }}>Pertanyaan Umum &amp; Informasi</option>
                            <option value="Kerjasama Bisnis & Iklan" {{ old('subject') == 'Kerjasama Bisnis & Iklan' ? 'selected' : '' }}>Kerjasama Bisnis &amp; Iklan</option>
                            <option value="Laporan Link Rusak / Software" {{ old('subject') == 'Laporan Link Rusak / Software' ? 'selected' : '' }}>Laporan Link Rusak / Masalah Software</option>
                            <option value="Permohonan DMCA / Hak Cipta" {{ old('subject') == 'Permohonan DMCA / Hak Cipta' ? 'selected' : '' }}>Permohonan Hak Cipta / DMCA Takedown</option>
                            <option value="Konsultasi Proyek & Pengembangan Web" {{ old('subject') == 'Konsultasi Proyek & Pengembangan Web' ? 'selected' : '' }}>Konsultasi Proyek &amp; Pengembangan Web</option>
                        </select>
                        @error('subject')
                            <span style="color: var(--accent-danger); font-size: 0.8rem; margin-top: 0.25rem; display: block;">{{ $message }}</span>
                        @enderror
                    </div>

                    <div style="margin-bottom: 1.75rem;">
                        <label for="message" style="display: block; font-size: 0.85rem; font-weight: 600; color: var(--text-primary); margin-bottom: 0.4rem;">Isi Pesan <span style="color: var(--accent-danger);">*</span></label>
                        <textarea name="message" id="message" rows="5" required
                            style="width: 100%; padding: 0.75rem 1rem; font-size: 0.9rem; border-radius: var(--radius-sm); border: 1px solid var(--border-color); background: var(--bg-primary); color: var(--text-primary); outline: none; font-family: inherit; line-height: 1.6; resize: vertical; transition: var(--transition-fast);"
                            placeholder="Tuliskan pesan, rincian pertanyaan, atau masukan Anda di sini...">{{ old('message') }}</textarea>
                        @error('message')
                            <span style="color: var(--accent-danger); font-size: 0.8rem; margin-top: 0.25rem; display: block;">{{ $message }}</span>
                        @enderror
                    </div>

                    <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
                        <span style="font-size: 0.8rem; color: var(--text-muted);">
                            Data Anda dilindungi oleh <a href="{{ route('privacy') }}" style="color: var(--accent-sky); text-decoration: underline;">Kebijakan Privasi</a>.
                        </span>
                        
                        <button type="submit" class="btn btn-primary" style="padding: 0.75rem 1.75rem; font-size: 0.95rem; font-weight: 700; border: none; cursor: pointer;">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 4px;"><line x1="22" y1="2" x2="11" y2="13"></line><polygon points="22 2 15 22 11 13 2 9 22 2"></polygon></svg>
                            Kirim Pesan
                        </button>
                    </div>
                </form>
            </div>

        </div>

        <!-- Quick FAQ Section -->
        <div style="margin-top: 4.5rem; border-top: 1px solid var(--border-color); padding-top: 3.5rem;">
            <div style="text-align: center; margin-bottom: 2rem;">
                <span class="section-subtitle">FAQ Ringkas</span>
                <h3 style="font-size: 1.75rem; font-weight: 800; color: var(--text-primary);">Pertanyaan yang Sering Diajukan</h3>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem;">
                <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 1.5rem;">
                    <h4 style="font-size: 1rem; font-weight: 700; color: var(--text-primary); margin-bottom: 0.5rem;">Berapa lama respon email akan dikirim?</h4>
                    <p style="color: var(--text-secondary); font-size: 0.88rem; line-height: 1.6; margin: 0;">
                        Kami biasanya membalas setiap pesan masuk dalam kurun waktu 1x24 jam kerja (Senin - Sabtu).
                    </p>
                </div>

                <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 1.5rem;">
                    <h4 style="font-size: 1rem; font-weight: 700; color: var(--text-primary); margin-bottom: 0.5rem;">Apakah software di situs ini gratis?</h4>
                    <p style="color: var(--text-secondary); font-size: 0.88rem; line-height: 1.6; margin: 0;">
                        Ya, seluruh software dalam direktori kami berlisensi freeware, open source, atau versi gratis resmi dari penyedia aslinya.
                    </p>
                </div>

                <div style="background: var(--bg-card); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 1.5rem;">
                    <h4 style="font-size: 1rem; font-weight: 700; color: var(--text-primary); margin-bottom: 0.5rem;">Bagaimana jika tautan unduhan rusak?</h4>
                    <p style="color: var(--text-secondary); font-size: 0.88rem; line-height: 1.6; margin: 0;">
                        Anda dapat memilih kategori "Laporan Link Rusak" pada formulir kontak atau menggunakan tombol laporkan link di halaman detail software.
                    </p>
                </div>
            </div>
        </div>

    </div>
</section>
@endsection
