@extends('layouts.admin')

@section('title', 'Daftar Admin')
@section('page_title', 'Kelola Pengguna Administrator')

@section('admin_content')
    <div class="admin-card">
        <div class="admin-card-header">
            <h4 class="admin-card-title">Daftar Akun Administrator</h4>
            <a href="{{ route('admin.users.create') }}" class="btn-admin btn-admin-primary btn-admin-sm">+ Tambah Admin</a>
        </div>
        
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Nama Lengkap</th>
                        <th>Alamat Email</th>
                        <th>Role</th>
                        <th>Tanggal Terdaftar</th>
                        <th style="width: 150px; text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td style="font-weight: 700;">
                                {{ $user->name }}
                                @if($user->id === Auth::id())
                                    <span class="badge-admin badge-emerald" style="font-size: 0.65rem; margin-left: 0.35rem;">Akun Anda</span>
                                @endif
                            </td>
                            <td style="font-family: monospace; font-size: 0.85rem; color: var(--accent-blue);">
                                {{ $user->email }}
                            </td>
                            <td>
                                @if($user->role === 'admin')
                                    <span class="badge-admin badge-emerald" style="font-size: 0.75rem; font-weight: 600;">Admin</span>
                                @elseif($user->role === 'student')
                                    <span class="badge-admin badge-blue" style="font-size: 0.75rem; font-weight: 600;">Student</span>
                                @else
                                    <span class="badge-admin badge-gray" style="font-size: 0.75rem; font-weight: 600;">{{ ucfirst($user->role) }}</span>
                                @endif
                            </td>
                            <td style="font-size: 0.8rem; color: var(--text-admin-secondary);">
                                {{ $user->created_at->format('d M Y, H:i') }}
                            </td>
                            <td class="actions-cell" style="justify-content: flex-end;">
                                <a href="{{ route('admin.users.edit', $user->id) }}" class="btn-admin btn-admin-secondary btn-admin-sm">Edit</a>
                                
                                @if($user->id !== Auth::id())
                                    <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" 
                                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun administrator ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-admin btn-admin-danger btn-admin-sm">Hapus</button>
                                    </form>
                                @else
                                    <button class="btn-admin btn-admin-danger btn-admin-sm" disabled style="opacity: 0.4; cursor: not-allowed;" title="Anda tidak dapat menghapus akun Anda sendiri">Hapus</button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align: center; color: var(--text-admin-secondary); padding: 3rem;">
                                Belum ada akun administrator terdaftar.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
