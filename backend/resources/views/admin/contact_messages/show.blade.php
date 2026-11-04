@extends('layouts.admin')

@section('title', 'Detail Pesan Kontak')

@section('admin_content')
<div style="margin-bottom: 2rem;">
    <a href="{{ route('admin.contact-messages.index') }}" style="display: inline-flex; align-items: center; gap: 0.5rem; color: var(--accent-sky); font-size: 0.9rem; text-decoration: none; font-weight: 600; margin-bottom: 1rem;">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
        Kembali ke Daftar Pesan
    </a>
    <h2 style="font-size: 1.5rem; font-weight: 700; color: var(--text-admin-primary); margin: 0;">Detail Pesan Kontak</h2>
</div>

@if(session('success'))
    <div style="background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.2); color: #10b981; padding: 1rem; border-radius: var(--radius-sm); margin-bottom: 1.5rem;">
        {{ session('success') }}
    </div>
@endif

<div class="card-admin" style="padding: 2rem; max-width: 800px;">
    <div style="display: flex; justify-content: space-between; align-items: flex-start; padding-bottom: 1.5rem; border-bottom: 1px solid var(--border-admin); margin-bottom: 1.5rem;">
        <div>
            <h3 style="font-size: 1.25rem; font-weight: 700; color: var(--text-admin-primary); margin: 0 0 0.5rem 0;">{{ $message->subject }}</h3>
            <div style="font-size: 0.9rem; color: var(--text-admin-secondary);">
                Dari: <strong style="color: var(--text-admin-primary);">{{ $message->name }}</strong> (<a href="mailto:{{ $message->email }}" style="color: var(--accent-sky);">{{ $message->email }}</a>)
            </div>
            <div style="font-size: 0.8rem; color: var(--text-admin-secondary); margin-top: 0.25rem;">
                Diterima pada: {{ $message->created_at->format('d F Y, H:i:s') }} 
                @if($message->ip_address)
                    &bull; IP: <code>{{ $message->ip_address }}</code>
                @endif
            </div>
        </div>
        
        <div>
            @if($message->status === 'unread')
                <span style="background: rgba(239, 68, 68, 0.1); color: #ef4444; padding: 4px 12px; border-radius: 100px; font-size: 0.8rem; font-weight: 700;">Belum Dibaca</span>
            @else
                <span style="background: rgba(16, 185, 129, 0.1); color: #10b981; padding: 4px 12px; border-radius: 100px; font-size: 0.8rem; font-weight: 600;">Sudah Dibaca</span>
            @endif
        </div>
    </div>

    <div style="margin-bottom: 2rem;">
        <h4 style="font-size: 0.85rem; text-transform: uppercase; color: var(--text-admin-secondary); letter-spacing: 0.5px; margin-bottom: 0.75rem;">Isi Pesan:</h4>
        <div style="background: rgba(0, 0, 0, 0.02); border: 1px solid var(--border-admin); border-radius: var(--radius-sm); padding: 1.5rem; font-size: 0.95rem; line-height: 1.7; color: var(--text-admin-primary); white-space: pre-wrap;">{{ $message->message }}</div>
    </div>

    <div style="display: flex; gap: 1rem; align-items: center; border-top: 1px solid var(--border-admin); padding-top: 1.5rem;">
        <a href="mailto:{{ $message->email }}?subject=Balasan: {{ rawurlencode($message->subject) }}" class="btn-admin btn-admin-primary" style="padding: 0.6rem 1.25rem; font-size: 0.9rem; text-decoration: none; display: inline-flex; align-items: center; gap: 0.5rem;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
            Balas via Email
        </a>

        <form action="{{ route('admin.contact-messages.toggle-read', $message->id) }}" method="POST" style="display: inline;">
            @csrf
            <button type="submit" class="btn-admin btn-admin-secondary" style="padding: 0.6rem 1.25rem; font-size: 0.9rem; cursor: pointer;">
                Tandai sebagai {{ $message->status === 'unread' ? 'Sudah Dibaca' : 'Belum Dibaca' }}
            </button>
        </form>

        <form action="{{ route('admin.contact-messages.destroy', $message->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pesan ini?');" style="display: inline; margin-left: auto;">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn-admin" style="background: rgba(239, 68, 68, 0.1); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.2); padding: 0.6rem 1.25rem; font-size: 0.9rem; cursor: pointer;">
                Hapus Pesan
            </button>
        </form>
    </div>
</div>
@endsection
