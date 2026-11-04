@extends('layouts.app')

@section('title', $course->title)

@section('content')
<div style="background: radial-gradient(circle at top right, rgba(2, 132, 199, 0.05), transparent 60%); padding: 3rem 0;">
    <div style="max-width: 1000px; margin: 0 auto; padding: 0 1.5rem;">
        
        <!-- Breadcrumb / Header -->
        <div style="margin-bottom: 2.5rem;">
            <span style="color: var(--accent-sky); font-size: 0.85rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;">KURSUS MANDIRI</span>
            <h1 style="font-size: 2.5rem; font-weight: 800; color: var(--text-primary); margin-top: 0.5rem; letter-spacing: -1px; line-height: 1.1;">
                {{ $course->title }}
            </h1>
            <p style="color: var(--text-secondary); font-size: 1.1rem; margin-top: 0.75rem; max-width: 700px; font-weight: 400; line-height: 1.6;">
                {{ $course->description }}
            </p>
        </div>

        @if(session('success'))
            <div style="background: rgba(5, 150, 105, 0.08); border: 1px solid rgba(5, 150, 105, 0.2); color: var(--accent-emerald); padding: 1rem; border-radius: var(--radius-md); font-size: 0.9rem; margin-bottom: 2rem;">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div style="background: rgba(220, 38, 38, 0.08); border: 1px solid rgba(220, 38, 38, 0.2); color: var(--accent-danger); padding: 1rem; border-radius: var(--radius-md); font-size: 0.9rem; margin-bottom: 2rem;">
                {{ session('error') }}
            </div>
        @endif

        <!-- Quiz Panel (Pre & Post Test) -->
        <div style="background: var(--bg-secondary); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 2rem; box-shadow: var(--shadow-md); margin-bottom: 3rem;">
            <div style="border-bottom: 1px solid var(--border-color); padding-bottom: 1.25rem; margin-bottom: 1.5rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                <div>
                    <h3 style="font-size: 1.25rem; font-weight: 700; color: var(--text-primary);">Evaluasi Ujian</h3>
                    <p style="font-size: 0.85rem; color: var(--text-muted); margin-top: 0.25rem;">Uji kemampuan Anda sebelum dan sesudah mempelajari materi.</p>
                </div>
                @guest
                    <div style="font-size: 0.85rem; background: rgba(217, 119, 6, 0.08); border: 1px solid rgba(217, 119, 6, 0.2); color: var(--accent-amber); padding: 0.5rem 1rem; border-radius: var(--radius-sm); font-weight: 500;">
                        Silakan login untuk dapat memulai ujian.
                    </div>
                @endguest
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1rem;">
                
                <!-- Pre Test Card -->
                <div style="background: var(--bg-primary); border: 1px solid var(--border-color); border-radius: var(--radius-sm); padding: 1.5rem; display: flex; flex-direction: column; justify-content: space-between; position: relative;">
                    <div>
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.75rem;">
                            <span style="font-size: 0.75rem; font-weight: 700; color: var(--accent-sky); border: 1px solid rgba(2, 132, 199, 0.3); padding: 0.2rem 0.5rem; border-radius: 4px; text-transform: uppercase;">Awal</span>
                            @if(isset($preTestScore))
                                <span style="font-size: 0.8rem; font-weight: 700; color: var(--accent-emerald);">Tertinggi: {{ $preTestScore }}/100</span>
                            @endif
                        </div>
                        <h4 style="font-size: 1.1rem; font-weight: 700; color: var(--text-primary); margin-bottom: 0.5rem;">Pre Test HTML Dasar</h4>
                        <p style="font-size: 0.85rem; color: var(--text-secondary); line-height: 1.5; margin-bottom: 1.5rem;">
                            Dikerjakan sebelum membaca materi untuk menguji pengetahuan awal Anda tentang HTML.
                        </p>
                    </div>
                    
                    @guest
                        <a href="{{ route('student.login', ['redirect' => request()->fullUrl()]) }}" style="display: block; text-align: center; background: var(--accent-sky); color: #fff; padding: 0.65rem; border-radius: var(--radius-sm); font-size: 0.85rem; font-weight: 600; text-decoration: none; transition: var(--transition-fast);">
                            Login Untuk Mulai
                        </a>
                    @else
                        <a href="{{ route('courses.test', [$course->slug, 'pre_test']) }}" style="display: block; text-align: center; background: var(--accent-sky); color: #fff; padding: 0.65rem; border-radius: var(--radius-sm); font-size: 0.85rem; font-weight: 600; text-decoration: none; transition: var(--transition-fast);"
                           onmouseenter="this.style.opacity='0.9'" onmouseleave="this.style.opacity='1'">
                            {{ isset($preTestScore) ? 'Ulangi Pre Test' : 'Mulai Pre Test' }}
                        </a>
                    @endguest
                </div>

                <!-- Post Test Card -->
                <div style="background: var(--bg-primary); border: 1px solid var(--border-color); border-radius: var(--radius-sm); padding: 1.5rem; display: flex; flex-direction: column; justify-content: space-between; position: relative;">
                    <div>
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.75rem;">
                            <span style="font-size: 0.75rem; font-weight: 700; color: var(--accent-amber); border: 1px solid rgba(217, 119, 6, 0.3); padding: 0.2rem 0.5rem; border-radius: 4px; text-transform: uppercase;">Akhir</span>
                            @if(isset($postTestScore))
                                <span style="font-size: 0.8rem; font-weight: 700; color: var(--accent-emerald);">Tertinggi: {{ $postTestScore }}/100</span>
                            @endif
                        </div>
                        <h4 style="font-size: 1.1rem; font-weight: 700; color: var(--text-primary); margin-bottom: 0.5rem;">Post Test HTML Dasar</h4>
                        <p style="font-size: 0.85rem; color: var(--text-secondary); line-height: 1.5; margin-bottom: 1.5rem;">
                            Dikerjakan setelah Anda selesai mempelajari seluruh rangkuman materi di bawah ini.
                        </p>
                    </div>
                    
                    @guest
                        <a href="{{ route('student.login', ['redirect' => request()->fullUrl()]) }}" style="display: block; text-align: center; background: var(--text-muted); color: #fff; padding: 0.65rem; border-radius: var(--radius-sm); font-size: 0.85rem; font-weight: 600; text-decoration: none; transition: var(--transition-fast); opacity: 0.75;">
                            Login Untuk Mulai
                        </a>
                    @else
                        @if(isset($preTestScore))
                            <a href="{{ route('courses.test', [$course->slug, 'post_test']) }}" style="display: block; text-align: center; background: var(--accent-amber); color: #fff; padding: 0.65rem; border-radius: var(--radius-sm); font-size: 0.85rem; font-weight: 600; text-decoration: none; transition: var(--transition-fast);"
                               onmouseenter="this.style.opacity='0.9'" onmouseleave="this.style.opacity='1'">
                                {{ isset($postTestScore) ? 'Ulangi Post Test' : 'Mulai Post Test' }}
                            </a>
                        @else
                            <button disabled style="width: 100%; text-align: center; background: #e2e8f0; color: #94a3b8; border: 1px solid #cbd5e1; padding: 0.65rem; border-radius: var(--radius-sm); font-size: 0.85rem; font-weight: 600; cursor: not-allowed;" title="Selesaikan Pre Test terlebih dahulu">
                                🔒 Kerjakan Pre Test Dulu
                            </button>
                        @endif
                    @endguest
                </div>
            </div>
        </div>

        <!-- Materials Section -->
        <div style="background: var(--bg-secondary); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 3rem 2.5rem; box-shadow: var(--shadow-sm); line-height: 1.8;">
            <h2 style="font-size: 1.75rem; font-weight: 800; color: var(--text-primary); margin-bottom: 1.5rem; border-bottom: 2px solid var(--border-color); padding-bottom: 0.75rem;">Materi Pelajaran</h2>
            
            <div class="course-materials-content" style="color: var(--text-secondary); font-size: 1rem;">
                @if($course->materials)
                    @guest
                        <div style="position: relative; max-height: 600px; overflow: hidden; margin-bottom: 1.5rem;">
                            <div style="mask-image: linear-gradient(to bottom, black 60%, transparent 100%); -webkit-mask-image: linear-gradient(to bottom, black 60%, transparent 100%);">
                                {!! $course->materials !!}
                            </div>
                        </div>
                        
                        <div style="text-align: center; padding: 1rem 0 0; display: flex; flex-direction: column; align-items: center; justify-content: center;">
                            <div style="background: var(--bg-primary); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 2rem; max-width: 500px; width: 100%; box-shadow: var(--shadow-md);">
                                <div style="font-size: 1.75rem; margin-bottom: 0.75rem;">🔒</div>
                                <h3 style="font-size: 1.15rem; font-weight: 700; color: var(--text-primary); margin-bottom: 0.5rem;">Materi Terkunci</h3>
                                <p style="font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 1.5rem; line-height: 1.5;">Silakan masuk atau daftar terlebih dahulu untuk membaca materi lengkap secara gratis.</p>
                                <div style="display: flex; gap: 1rem; justify-content: center;">
                                    <a href="{{ route('student.login', ['redirect' => request()->fullUrl()]) }}" style="background: var(--accent-sky); color: #fff; text-decoration: none; padding: 0.6rem 1.5rem; font-size: 0.85rem; font-weight: 600; border-radius: var(--radius-sm); transition: var(--transition-fast);"
                                       onmouseenter="this.style.opacity='0.9'" onmouseleave="this.style.opacity='1'">Masuk</a>
                                    <a href="{{ route('student.register', ['redirect' => request()->fullUrl()]) }}" style="background: transparent; border: 1px solid var(--border-color); color: var(--text-primary); text-decoration: none; padding: 0.6rem 1.5rem; font-size: 0.85rem; font-weight: 600; border-radius: var(--radius-sm); transition: var(--transition-fast);"
                                       onmouseenter="this.style.background='var(--bg-secondary)'" onmouseleave="this.style.background='transparent'">Daftar</a>
                                </div>
                            </div>
                        </div>
                    @else
                        {!! $course->materials !!}

                        <!-- Booking Section -->
                        <div style="margin-top: 3.5rem; border-top: 1px solid var(--border-color); padding-top: 2.5rem;">
                            <div style="background: var(--bg-primary); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 2.5rem; text-align: center; box-shadow: var(--shadow-sm);">
                                <h3 style="font-size: 1.25rem; font-weight: 700; color: var(--text-primary); margin-bottom: 0.5rem; line-height: 1.5;">
                                    Masih bingung dengan materi ini, butuh penjelasan lebih lanjut atau kursus singkat?
                                </h3>
                                <p style="font-size: 0.95rem; color: var(--text-secondary); margin-bottom: 1.5rem;">
                                    Pesan Private Google Meet ke Mentor <strong>Arhana.my.id</strong>
                                </p>
                                <button type="button" onclick="openBookingModal()" style="background: var(--accent-sky); color: #fff; border: none; padding: 0.75rem 2rem; font-size: 0.9rem; font-weight: 600; border-radius: var(--radius-sm); cursor: pointer; transition: var(--transition-smooth); box-shadow: 0 4px 12px rgba(2, 132, 199, 0.2);"
                                        onmouseenter="this.style.background='#0274b3'; this.style.transform='translateY(-1px)';"
                                        onmouseleave="this.style.background='var(--accent-sky)'; this.style.transform='none';">
                                    Pesan Sekarang
                                </button>
                            </div>

                            <!-- Modal Booking (Hidden by Default) -->
                            <div id="bookingModal" onclick="handleModalOutsideClick(event)" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px); align-items: center; justify-content: center; z-index: 9999; padding: 1.5rem;">
                                <div class="modal-box" style="background: var(--bg-secondary); border: 1px solid var(--border-color); border-radius: var(--radius-md); max-width: 550px; width: 100%; max-height: 90vh; overflow-y: auto; padding: 2rem; box-shadow: var(--shadow-xl); position: relative;">
                                    
                                    <!-- Close button -->
                                    <button type="button" onclick="closeBookingModal()" style="position: absolute; top: 1.25rem; right: 1.25rem; background: none; border: none; font-size: 1.5rem; color: var(--text-muted); cursor: pointer; display: flex; align-items: center; justify-content: center; width: 30px; height: 30px; border-radius: 50%; transition: var(--transition-fast);" onmouseenter="this.style.background='var(--bg-primary)'; this.style.color='var(--text-primary)';" onmouseleave="this.style.background='none'; this.style.color='var(--text-muted)';">
                                        &times;
                                    </button>

                                    <h4 style="font-size: 1.25rem; font-weight: 800; color: var(--text-primary); margin-bottom: 1.5rem; border-bottom: 1px solid var(--border-color); padding-bottom: 0.75rem; text-align: left;">
                                        Booking Kelas Privat Google Meet
                                    </h4>

                                    <form action="{{ route('bookings.store') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="course_id" value="{{ $course->id }}">

                                        <div style="margin-bottom: 1.25rem; text-align: left;">
                                            <label style="display: block; font-size: 0.85rem; font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem;">Materi</label>
                                            <input type="text" readonly value="{{ $course->title }}" style="width: 100%; padding: 0.75rem 1rem; font-size: 0.9rem; border-radius: var(--radius-sm); border: 1px solid var(--border-color); background: var(--bg-primary); color: var(--text-muted); outline: none;">
                                        </div>

                                        <div style="margin-bottom: 1.25rem; text-align: left;">
                                            <label style="display: block; font-size: 0.85rem; font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem;">Nama</label>
                                            <input type="text" readonly value="{{ Auth::user()->name }}" style="width: 100%; padding: 0.75rem 1rem; font-size: 0.9rem; border-radius: var(--radius-sm); border: 1px solid var(--border-color); background: var(--bg-primary); color: var(--text-muted); outline: none;">
                                        </div>

                                        <div style="margin-bottom: 1.25rem; text-align: left;">
                                            <label style="display: block; font-size: 0.85rem; font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem;">Email</label>
                                            <input type="email" readonly value="{{ Auth::user()->email }}" style="width: 100%; padding: 0.75rem 1rem; font-size: 0.9rem; border-radius: var(--radius-sm); border: 1px solid var(--border-color); background: var(--bg-primary); color: var(--text-muted); outline: none;">
                                        </div>

                                        <div style="margin-bottom: 1.25rem; text-align: left;">
                                            <label style="display: block; font-size: 0.85rem; font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem;">No WA</label>
                                            <input type="text" name="wa_number" required placeholder="Contoh: 081234567890" style="width: 100%; padding: 0.75rem 1rem; font-size: 0.9rem; border-radius: var(--radius-sm); border: 1px solid var(--border-color); background: var(--bg-primary); color: var(--text-primary); outline: none; transition: var(--transition-fast);">
                                        </div>

                                        <div style="margin-bottom: 1.25rem; text-align: left;">
                                            <label style="display: block; font-size: 0.85rem; font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem;">Pertanyaan</label>
                                            <textarea name="question" required rows="4" placeholder="Tuliskan pertanyaan atau topik yang ingin dibahas..." style="width: 100%; padding: 0.75rem 1rem; font-size: 0.9rem; border-radius: var(--radius-sm); border: 1px solid var(--border-color); background: var(--bg-primary); color: var(--text-primary); outline: none; transition: var(--transition-fast); resize: vertical; line-height: 1.5;"></textarea>
                                        </div>

                                        <div style="margin-bottom: 1.5rem; text-align: left;">
                                            <label style="display: block; font-size: 0.85rem; font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem;">Request Jadwal</label>
                                            <input type="datetime-local" name="requested_schedule" required style="width: 100%; padding: 0.75rem 1rem; font-size: 0.9rem; border-radius: var(--radius-sm); border: 1px solid var(--border-color); background: var(--bg-primary); color: var(--text-primary); outline: none; transition: var(--transition-fast);">
                                        </div>

                                        <div style="background: rgba(2, 132, 199, 0.05); border: 1px solid rgba(2, 132, 199, 0.15); border-radius: var(--radius-sm); padding: 1.25rem; margin-bottom: 2rem; font-size: 0.8rem; line-height: 1.6; color: var(--text-secondary); text-align: left;">
                                            <p style="font-weight: 700; color: var(--text-primary); margin-bottom: 0.5rem; font-size: 0.85rem;">Catatan Penting:</p>
                                            <ul style="margin: 0; padding-left: 1.2rem; display: flex; flex-direction: column; gap: 0.25rem;">
                                                <li>Mohon tunggu konfirmasi dari Admin Arhana.my.id</li>
                                                <li>Kami akan menghubungi anda melalui Email/WA untuk konfirmasi jadwal</li>
                                                <li>Link Google Meet akan dikirim melalui email/WA setelah melakukan pembayaran</li>
                                                <li>Biaya Google Meeting (Kursus) Rp 50.000,-/jam</li>
                                            </ul>
                                        </div>

                                        <button type="submit" style="width: 100%; background: var(--accent-sky); color: #fff; border: none; padding: 0.85rem; font-size: 0.9rem; font-weight: 600; border-radius: var(--radius-sm); cursor: pointer; transition: var(--transition-smooth);"
                                                onmouseenter="this.style.background='#0274b3'"
                                                onmouseleave="this.style.background='var(--accent-sky)'">
                                            Kirim Pengajuan Pesanan
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <script>
                            function openBookingModal() {
                                const modal = document.getElementById('bookingModal');
                                modal.style.display = 'flex';
                                document.body.style.overflow = 'hidden';
                            }
                            
                            function closeBookingModal() {
                                const modal = document.getElementById('bookingModal');
                                modal.style.display = 'none';
                                document.body.style.overflow = '';
                            }

                            function handleModalOutsideClick(event) {
                                const modal = document.getElementById('bookingModal');
                                if (event.target === modal) {
                                    closeBookingModal();
                                }
                            }
                        </script>
                    @endguest
                @else
                    <p style="color: var(--text-muted); font-style: italic; text-align: center; padding: 2rem 0;">Materi pembelajaran sedang dipersiapkan oleh administrator.</p>
                @endif
            </div>
        </div>

    </div>
</div>

<style>
    /* Styling responsive grid for Pre/Post test cards */
    @media (max-width: 768px) {
        div[style*="grid-template-columns"] {
            grid-template-columns: 1fr !important;
        }
    }
    /* Material HTML styling overrides to match beautiful layout */
    .course-materials-content h2, .course-materials-content h3 {
        color: var(--text-primary);
        font-weight: 700;
        margin-top: 1.75rem;
        margin-bottom: 0.75rem;
    }
    .course-materials-content p {
        margin-bottom: 1.25rem;
    }
    .course-materials-content ul, .course-materials-content ol {
        margin-bottom: 1.25rem;
        padding-left: 1.5rem;
    }
    .course-materials-content code {
        background: var(--bg-primary);
        border: 1px solid var(--border-color);
        padding: 0.2rem 0.4rem;
        border-radius: 4px;
        font-family: Consolas, monospace;
        font-size: 0.9em;
        color: var(--accent-sky);
    }
    .course-materials-content pre {
        background: #0f172a;
        color: #e2e8f0;
        padding: 1.25rem;
        border-radius: var(--radius-sm);
        overflow-x: auto;
        margin-bottom: 1.5rem;
        border: 1px solid rgba(255,255,255,0.05);
    }
    .course-materials-content pre code {
        background: transparent;
        border: none;
        padding: 0;
        color: inherit;
        font-size: 0.85rem;
    }
    
    /* Modal Animations */
    #bookingModal {
        animation: fadeInBg 0.25s ease-out;
    }
    .modal-box {
        animation: slideInModal 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    }
    
    @keyframes fadeInBg {
        from { background: rgba(15, 23, 42, 0); backdrop-filter: blur(0px); }
        to { background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px); }
    }
    @keyframes slideInModal {
        from { opacity: 0; transform: scale(0.95) translateY(10px); }
        to { opacity: 1; transform: scale(1) translateY(0); }
    }
</style>
@endsection
