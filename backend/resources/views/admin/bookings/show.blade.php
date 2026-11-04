@extends('layouts.admin')

@section('title', 'Detail Booking: ' . ($booking->user->name ?? 'Siswa'))
@section('page_title', 'Detail Pemesanan Kelas Privat')

@section('admin_content')
    <!-- Back Button -->
    <div style="margin-bottom: 1.5rem;">
        <a href="{{ route('admin.bookings.index') }}" class="btn-admin btn-admin-secondary" style="display: inline-flex; align-items: center; gap: 0.5rem; text-decoration: none;">
            <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
            Kembali ke Daftar Booking
        </a>
    </div>

    @if(session('success'))
        <div style="background: rgba(5, 150, 105, 0.08); border: 1px solid rgba(5, 150, 105, 0.2); color: var(--accent-emerald); padding: 0.75rem 1rem; border-radius: var(--radius-sm); font-size: 0.85rem; margin-bottom: 1.5rem;">
            {{ session('success') }}
        </div>
    @endif

    <!-- Booking Details Card -->
    <div class="admin-card" style="margin-bottom: 2rem;">
        <div class="admin-card-header" style="border-bottom: 1px solid var(--border-color); padding-bottom: 1rem; margin-bottom: 1.5rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
            <h4 class="admin-card-title">Informasi Pengajuan Privat</h4>
            <div>
                @if($booking->status === 'pending')
                    <span style="background-color: rgba(245, 158, 11, 0.1); color: var(--accent-amber); font-size: 0.8rem; font-weight: 700; padding: 4px 10px; border-radius: 12px;">
                        Pending Request
                    </span>
                @else
                    <span style="background-color: rgba(16, 185, 129, 0.1); color: var(--accent-emerald); font-size: 0.8rem; font-weight: 700; padding: 4px 10px; border-radius: 12px;">
                        Solved
                    </span>
                @endif
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; padding-bottom: 1.5rem; border-bottom: 1px solid var(--border-color);">
            <div>
                <p style="font-size: 0.85rem; color: var(--text-admin-secondary); margin-bottom: 0.25rem;">Nama Siswa</p>
                <h3 style="font-size: 1.15rem; font-weight: 700; color: var(--text-admin-primary);">{{ $booking->user->name ?? 'User Terhapus' }}</h3>
            </div>
            <div>
                <p style="font-size: 0.85rem; color: var(--text-admin-secondary); margin-bottom: 0.25rem;">Alamat Email</p>
                <h3 style="font-size: 1.15rem; font-weight: 700; color: var(--text-admin-primary);">{{ $booking->user->email ?? '-' }}</h3>
            </div>
            <div>
                <p style="font-size: 0.85rem; color: var(--text-admin-secondary); margin-bottom: 0.25rem;">Nomor WhatsApp</p>
                <h3 style="font-size: 1.15rem; font-weight: 700;">
                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $booking->wa_number) }}" target="_blank" style="color: var(--accent-emerald); text-decoration: none; display: inline-flex; align-items: center; gap: 0.4rem;">
                        <svg width="18" height="18" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946C.06 5.348 5.397.01 12.008.01c3.202.001 6.212 1.246 8.477 3.514 2.266 2.268 3.507 5.28 3.505 8.484-.004 6.657-5.34 11.997-11.953 11.997-2.005-.001-3.973-.502-5.73-1.45L0 24zm6.59-4.846c1.6.95 3.188 1.449 4.825 1.451 5.436 0 9.86-4.37 9.864-9.799.002-2.63-1.023-5.101-2.885-6.963C16.59 2.019 14.12 1.01 11.998 1.01c-5.44 0-9.866 4.372-9.87 9.802 0 1.714.47 3.387 1.358 4.842l-.993 3.629 3.562-.93zm11.362-7.397c-.3-.15-1.772-.875-2.046-.975-.276-.1-.476-.15-.676.15-.2.3-.775.975-.95 1.174-.175.2-.35.225-.65.075-.3-.15-1.265-.467-2.41-1.485-.89-.795-1.49-1.77-1.665-2.07-.175-.3-.018-.462.13-.61.135-.133.3-.35.45-.525.15-.175.2-.3.3-.5.1-.2.05-.375-.025-.525-.075-.15-.676-1.625-.926-2.225-.244-.589-.49-.51-.676-.519-.175-.008-.375-.01-.575-.01-.2 0-.525.075-.8 1.025-.275 1.25-.875 2.125-1.125 2.45-.25.325.875 1.625 2.425 2.125.375.125.675.2.9.275.375.125.712.1 1.0.06.325-.05 1.772-.725 2.022-1.4.25-.675.25-1.25.175-1.4-.075-.15-.275-.25-.575-.4z"/></svg>
                        {{ $booking->wa_number }} (Klik Hubungi)
                    </a>
                </h3>
            </div>
            <div>
                <p style="font-size: 0.85rem; color: var(--text-admin-secondary); margin-bottom: 0.25rem;">Materi Kursus</p>
                <h3 style="font-size: 1.15rem; font-weight: 700; color: var(--text-admin-primary);">{{ $booking->course->title ?? 'Materi Terhapus' }}</h3>
            </div>
            <div>
                <p style="font-size: 0.85rem; color: var(--text-admin-secondary); margin-bottom: 0.25rem;">Jadwal yang Diminta</p>
                <h3 style="font-size: 1.15rem; font-weight: 700; color: var(--text-admin-primary);">
                    @php
                        try {
                            echo \Carbon\Carbon::parse($booking->requested_schedule)->format('d F Y, H:i') . ' WIB';
                        } catch (\Exception $e) {
                            echo $booking->requested_schedule;
                        }
                    @endphp
                </h3>
            </div>
            <div>
                <p style="font-size: 0.85rem; color: var(--text-admin-secondary); margin-bottom: 0.25rem;">Tanggal Pengajuan</p>
                <h3 style="font-size: 1.15rem; font-weight: 700; color: var(--text-admin-primary);">{{ $booking->created_at->format('d F Y H:i') }} WIB</h3>
            </div>
        </div>

        <div style="padding: 1.5rem 0;">
            <p style="font-size: 0.85rem; color: var(--text-admin-secondary); margin-bottom: 0.5rem; font-weight: 600;">Pertanyaan / Topik Pembahasan:</p>
            <div style="background: var(--bg-primary); border: 1px solid var(--border-color); border-radius: var(--radius-sm); padding: 1.5rem; line-height: 1.6; color: var(--text-admin-primary); font-size: 0.95rem; white-space: pre-wrap;">{{ $booking->question }}</div>
        </div>

        <!-- Management Actions -->
        <div style="display: flex; gap: 1rem; border-top: 1px solid var(--border-color); padding-top: 1.5rem; margin-top: 1rem;">
            <form action="{{ route('admin.bookings.toggle', $booking->id) }}" method="POST" style="margin: 0;">
                @csrf
                <button type="submit" class="btn-admin btn-admin-primary" style="{{ $booking->status === 'pending' ? 'background-color: var(--accent-emerald); border-color: var(--accent-emerald);' : 'background-color: var(--text-muted); border-color: var(--text-muted);' }} color: #fff;">
                    {{ $booking->status === 'pending' ? 'Tandai Solved (Selesai)' : 'Tandai Pending (Belum Selesai)' }}
                </button>
            </form>

            <form action="{{ route('admin.bookings.destroy', $booking->id) }}" method="POST" 
                  onsubmit="return confirm('Apakah Anda yakin ingin menghapus pemesanan kelas privat ini?')"
                  style="margin: 0;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-admin btn-admin-danger">
                    Hapus Pemesanan
                </button>
            </form>
        </div>
    </div>
@endsection
