@extends('layouts.admin')

@section('title', 'Pesan Kontak')

@section('admin_content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
    <div>
        <h2 style="font-size: 1.5rem; font-weight: 700; color: var(--text-admin-primary); margin: 0 0 0.5rem 0;">Pesan Kontak Masuk</h2>
        <p style="color: var(--text-admin-secondary); font-size: 0.9rem; margin: 0;">Kelola pesan dan pertanyaan yang dikirimkan oleh pengunjung melalui formulir kontak.</p>
    </div>
    
    <div style="display: flex; gap: 0.5rem;">
        <a href="{{ route('admin.contact-messages.index') }}" class="btn-admin {{ $status === 'all' ? 'btn-admin-primary' : 'btn-admin-secondary' }}" style="padding: 0.5rem 1rem; font-size: 0.85rem; text-decoration: none;">Semua</a>
        <a href="{{ route('admin.contact-messages.index', ['status' => 'unread']) }}" class="btn-admin {{ $status === 'unread' ? 'btn-admin-primary' : 'btn-admin-secondary' }}" style="padding: 0.5rem 1rem; font-size: 0.85rem; text-decoration: none;">Belum Dibaca</a>
        <a href="{{ route('admin.contact-messages.index', ['status' => 'read']) }}" class="btn-admin {{ $status === 'read' ? 'btn-admin-primary' : 'btn-admin-secondary' }}" style="padding: 0.5rem 1rem; font-size: 0.85rem; text-decoration: none;">Sudah Dibaca</a>
    </div>
</div>

@if(session('success'))
    <div style="background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.2); color: #10b981; padding: 1rem; border-radius: var(--radius-sm); margin-bottom: 1.5rem;">
        {{ session('success') }}
    </div>
@endif

<div class="card-admin" style="padding: 0; overflow: hidden;">
    <div style="overflow-x: auto;">
        <table class="table-admin" style="width: 100%; border-collapse: collapse; text-align: left;">
            <thead>
                <tr style="border-bottom: 1px solid var(--border-admin); background: rgba(0, 0, 0, 0.02);">
                    <th style="padding: 1rem 1.5rem; font-size: 0.85rem; color: var(--text-admin-secondary);">Pengirim</th>
                    <th style="padding: 1rem 1.5rem; font-size: 0.85rem; color: var(--text-admin-secondary);">Subjek</th>
                    <th style="padding: 1rem 1.5rem; font-size: 0.85rem; color: var(--text-admin-secondary);">Cuplikan Pesan</th>
                    <th style="padding: 1rem 1.5rem; font-size: 0.85rem; color: var(--text-admin-secondary);">Status</th>
                    <th style="padding: 1rem 1.5rem; font-size: 0.85rem; color: var(--text-admin-secondary);">Tanggal</th>
                    <th style="padding: 1rem 1.5rem; font-size: 0.85rem; color: var(--text-admin-secondary); text-align: right;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($messages as $msg)
                    <tr style="border-bottom: 1px solid var(--border-admin); {{ $msg->status === 'unread' ? 'background: rgba(2, 132, 199, 0.03); font-weight: 600;' : '' }}">
                        <td style="padding: 1rem 1.5rem;">
                            <div style="color: var(--text-admin-primary);">{{ $msg->name }}</div>
                            <small style="color: var(--text-admin-secondary); font-weight: 400;"><a href="mailto:{{ $msg->email }}" style="color: var(--accent-sky);">{{ $msg->email }}</a></small>
                        </td>
                        <td style="padding: 1rem 1.5rem; color: var(--text-admin-primary); max-width: 200px;">
                            <span style="display: block; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $msg->subject }}</span>
                        </td>
                        <td style="padding: 1rem 1.5rem; color: var(--text-admin-secondary); font-weight: 400; max-width: 250px;">
                            <span style="display: block; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ Str::limit($msg->message, 80) }}</span>
                        </td>
                        <td style="padding: 1rem 1.5rem;">
                            @if($msg->status === 'unread')
                                <span style="background: rgba(239, 68, 68, 0.1); color: #ef4444; padding: 4px 10px; border-radius: 100px; font-size: 0.75rem; font-weight: 700;">Baru</span>
                            @else
                                <span style="background: rgba(16, 185, 129, 0.1); color: #10b981; padding: 4px 10px; border-radius: 100px; font-size: 0.75rem; font-weight: 600;">Dibaca</span>
                            @endif
                        </td>
                        <td style="padding: 1rem 1.5rem; color: var(--text-admin-secondary); font-size: 0.85rem; font-weight: 400;">
                            {{ $msg->created_at->format('d M Y H:i') }}
                        </td>
                        <td style="padding: 1rem 1.5rem; text-align: right;">
                            <div style="display: inline-flex; gap: 0.5rem; align-items: center;">
                                <a href="{{ route('admin.contact-messages.show', $msg->id) }}" class="btn-admin btn-admin-secondary" style="padding: 0.35rem 0.75rem; font-size: 0.8rem; text-decoration: none;">
                                    Buka
                                </a>
                                <form action="{{ route('admin.contact-messages.destroy', $msg->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pesan ini?');" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-admin" style="background: rgba(239, 68, 68, 0.1); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.2); padding: 0.35rem 0.75rem; font-size: 0.8rem; cursor: pointer;">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="padding: 3rem; text-align: center; color: var(--text-admin-secondary);">
                            Belum ada pesan kontak masuk.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($messages->hasPages())
        <div style="padding: 1.5rem; border-top: 1px solid var(--border-admin);">
            {{ $messages->links() }}
        </div>
    @endif
</div>
@endsection
