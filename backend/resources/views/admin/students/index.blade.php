@extends('layouts.admin')

@section('title', 'Siswa Terdaftar')
@section('page_title', 'Manajemen Siswa & Hasil Tes')

@section('admin_content')
    <div class="admin-card">
        <div class="admin-card-header" style="margin-bottom: 2rem;">
            <h4 class="admin-card-title">Daftar Siswa Terdaftar</h4>
        </div>
        
        @if(session('success'))
            <div style="background: rgba(5, 150, 105, 0.08); border: 1px solid rgba(5, 150, 105, 0.2); color: var(--accent-emerald); padding: 0.75rem 1rem; border-radius: var(--radius-sm); font-size: 0.85rem; margin-bottom: 1.5rem;">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div style="background: rgba(220, 38, 38, 0.08); border: 1px solid rgba(220, 38, 38, 0.2); color: var(--accent-danger); padding: 0.75rem 1rem; border-radius: var(--radius-sm); font-size: 0.85rem; margin-bottom: 1.5rem;">
                {{ session('error') }}
            </div>
        @endif

        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th style="width: 60px;">No</th>
                        <th>Nama Siswa</th>
                        <th>Alamat Email</th>
                        <th>Tanggal Registrasi</th>
                        <th style="text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $student)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td style="font-weight: 700;">{{ $student->name }}</td>
                            <td>{{ $student->email }}</td>
                            <td>{{ $student->created_at->format('d M Y H:i') }} WIB</td>
                            <td class="actions-cell">
                                <a href="{{ route('admin.students.show', $student->id) }}" class="btn-admin btn-admin-primary btn-admin-sm" style="background-color: var(--accent-sky); border-color: var(--accent-sky); color: #fff;">
                                    Detail
                                </a>
                                <form action="{{ route('admin.students.destroy', $student->id) }}" method="POST" 
                                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun siswa ini? Semua hasil tes yang terkait juga akan ikut terhapus.')"
                                      style="display: inline-block; margin: 0;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-admin btn-admin-danger btn-admin-sm">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align: center; color: var(--text-admin-secondary); padding: 2rem 0;">Belum ada siswa yang mendaftar.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
