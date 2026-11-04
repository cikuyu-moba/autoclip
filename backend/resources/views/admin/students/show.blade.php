@extends('layouts.admin')

@section('title', 'Detail Siswa: ' . $student->name)
@section('page_title', 'Detail Siswa & Hasil Kursus')

@section('admin_content')
    <!-- Back Button -->
    <div style="margin-bottom: 1.5rem;">
        <a href="{{ route('admin.students.index') }}" class="btn-admin btn-admin-secondary" style="display: inline-flex; align-items: center; gap: 0.5rem; text-decoration: none;">
            <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
            Kembali ke Daftar Siswa
        </a>
    </div>

    <!-- Student Profile Card -->
    <div class="admin-card" style="margin-bottom: 2rem;">
        <div class="admin-card-header">
            <h4 class="admin-card-title">Profil Pengguna</h4>
        </div>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; padding: 1rem 0;">
            <div>
                <p style="font-size: 0.85rem; color: var(--text-admin-secondary); margin-bottom: 0.25rem;">Nama Lengkap</p>
                <h3 style="font-size: 1.25rem; font-weight: 700; color: var(--text-admin-primary);">{{ $student->name }}</h3>
            </div>
            <div>
                <p style="font-size: 0.85rem; color: var(--text-admin-secondary); margin-bottom: 0.25rem;">Alamat Email</p>
                <h3 style="font-size: 1.25rem; font-weight: 700; color: var(--text-admin-primary);">{{ $student->email }}</h3>
            </div>
            <div>
                <p style="font-size: 0.85rem; color: var(--text-admin-secondary); margin-bottom: 0.25rem;">Tanggal Registrasi</p>
                <p style="font-weight: 600; color: var(--text-admin-primary);">{{ $student->created_at->format('d F Y H:i') }} WIB</p>
            </div>
        </div>
    </div>

    <!-- Student Courses Performance -->
    <div class="admin-card">
        <div class="admin-card-header">
            <h4 class="admin-card-title">Hasil Kursus & Ujian Evaluasi</h4>
        </div>

        @if(session('success'))
            <div style="background: rgba(5, 150, 105, 0.08); border: 1px solid rgba(5, 150, 105, 0.2); color: var(--accent-emerald); padding: 0.75rem 1rem; border-radius: var(--radius-sm); font-size: 0.85rem; margin-bottom: 1.5rem;">
                {{ session('success') }}
            </div>
        @endif

        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Kursus Pelajaran</th>
                        <th>Status Pre-Test (Tertinggi)</th>
                        <th>Status Post-Test (Tertinggi)</th>
                        <th>Aksi Reset</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($courses as $course)
                        @php
                            $hasTakenPre = $course->pre_test_attempts > 0;
                            $hasTakenPost = $course->post_test_attempts > 0;
                        @endphp
                        <tr>
                            <td style="font-weight: 700;">{{ $course->title }}</td>
                            <td>
                                @if($hasTakenPre)
                                    <span class="badge-admin" style="background: rgba(5, 150, 105, 0.1); color: var(--accent-emerald); border: 1px solid rgba(5, 150, 105, 0.2); padding: 3px 8px; border-radius: 4px; font-weight: 700;">
                                        Selesai (Nilai: {{ $course->pre_test_high }}/100)
                                    </span>
                                    <span style="display:block; font-size:0.75rem; color:var(--text-admin-secondary); margin-top:4px;">
                                        Jumlah percobaan: {{ $course->pre_test_attempts }}x
                                    </span>
                                @else
                                    <span style="color: var(--text-admin-secondary); font-style: italic;">Belum Mengikuti</span>
                                @endif
                            </td>
                            <td>
                                @if($hasTakenPost)
                                    <span class="badge-admin" style="background: rgba(5, 150, 105, 0.1); color: var(--accent-emerald); border: 1px solid rgba(5, 150, 105, 0.2); padding: 3px 8px; border-radius: 4px; font-weight: 700;">
                                        Selesai (Nilai: {{ $course->post_test_high }}/100)
                                    </span>
                                    <span style="display:block; font-size:0.75rem; color:var(--text-admin-secondary); margin-top:4px;">
                                        Jumlah percobaan: {{ $course->post_test_attempts }}x
                                    </span>
                                @else
                                    <span style="color: var(--text-admin-secondary); font-style: italic;">Belum Mengikuti</span>
                                @endif
                            </td>
                            <td>
                                @if($hasTakenPre || $hasTakenPost)
                                    <form action="{{ route('admin.students.reset-scores', [$student->id, $course->id]) }}" method="POST" 
                                          onsubmit="return confirm('Apakah Anda yakin ingin mereset seluruh skor ujian kursus ini untuk siswa {{ $student->name }}? Ini akan menghapus data riwayat pengerjaan sehingga siswa tersebut dapat mengulang dari awal.')"
                                          style="display: inline-block;">
                                        @csrf
                                        <button type="submit" class="btn-admin btn-admin-danger btn-admin-sm">
                                            Reset Skor (0)
                                        </button>
                                    </form>
                                @else
                                    <button disabled class="btn-admin btn-admin-secondary btn-admin-sm" style="opacity: 0.5; cursor: not-allowed;">
                                        Tidak ada data
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" style="text-align: center; color: var(--text-admin-secondary); padding: 2rem 0;">Belum ada kursus pelajaran yang terdaftar di database.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
