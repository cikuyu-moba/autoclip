@extends('layouts.admin')

@section('title', 'Booking Kelas Privat')
@section('page_title', 'Pemesanan Google Meet Kelas Privat')

@section('admin_content')
    <div class="admin-card">
        <div class="admin-card-header" style="margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
            <div>
                <h4 class="admin-card-title">Daftar Pengajuan Kelas Privat</h4>
                <p style="font-size: 0.8rem; color: var(--text-admin-secondary); margin-top: 0.25rem;">Kelola pengajuan Google Meet private dari siswa.</p>
            </div>
        </div>

        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th style="width: 50px;">No</th>
                        <th>Nama Siswa</th>
                        <th>Materi Kursus</th>
                        <th>Tanggal Pengajuan</th>
                        <th>Status</th>
                        <th style="text-align: right; width: 120px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bookings as $booking)
                        <tr>
                            <td>{{ ($bookings->currentPage() - 1) * $bookings->perPage() + $loop->iteration }}</td>
                            <td style="font-weight: 700;">{{ $booking->user->name ?? 'User Terhapus' }}</td>
                            <td>
                                <span style="background: rgba(2, 132, 199, 0.08); color: var(--accent-sky); padding: 0.2rem 0.5rem; border-radius: 4px; font-size: 0.8rem; font-weight: 600;">
                                    {{ $booking->course->title ?? 'Materi Terhapus' }}
                                </span>
                            </td>
                            <td>
                                {{ $booking->created_at->format('d M Y H:i') }} WIB
                            </td>
                            <td>
                                @if($booking->status === 'pending')
                                    <span style="background-color: rgba(245, 158, 11, 0.1); color: var(--accent-amber); font-size: 0.75rem; font-weight: 700; padding: 3px 8px; border-radius: 12px; display: inline-block;">
                                        Pending Request
                                    </span>
                                @else
                                    <span style="background-color: rgba(16, 185, 129, 0.1); color: var(--accent-emerald); font-size: 0.75rem; font-weight: 700; padding: 3px 8px; border-radius: 12px; display: inline-block;">
                                        Solved
                                    </span>
                                @endif
                            </td>
                            <td style="text-align: right; white-space: nowrap;">
                                <a href="{{ route('admin.bookings.show', $booking->id) }}" class="btn-admin btn-admin-primary btn-admin-sm" style="background-color: var(--accent-sky); border-color: var(--accent-sky); color: #fff;">
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; color: var(--text-admin-secondary); padding: 3rem 0;">Belum ada pengajuan kelas privat.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($bookings->hasPages())
            <div style="margin-top: 2rem;">
                {{ $bookings->links() }}
            </div>
        @endif
    </div>
@endsection
