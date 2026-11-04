@extends('layouts.app')

@section('title', 'Lupa Kata Sandi')

@section('content')
<div style="min-height: 80vh; display: flex; align-items: center; justify-content: center; padding: 3rem 1.5rem; background: radial-gradient(circle at top right, rgba(2, 132, 199, 0.05), transparent 60%);">
    <div style="max-width: 440px; width: 100%; background: var(--bg-secondary); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 2.5rem; box-shadow: var(--shadow-lg);">
        <div style="text-align: center; margin-bottom: 2.5rem;">
            <h1 style="font-size: 1.75rem; font-weight: 800; color: var(--text-primary); margin-bottom: 0.5rem; letter-spacing: -0.5px;">Lupa Kata Sandi</h1>
            <p style="color: var(--text-secondary); font-size: 0.9rem;">Masukkan alamat email Anda dan kami akan mengirimkan tautan untuk menyetel ulang kata sandi.</p>
        </div>

        @if(session('status'))
            <div style="background: rgba(5, 150, 105, 0.08); border: 1px solid rgba(5, 150, 105, 0.2); color: var(--accent-emerald); padding: 0.75rem 1rem; border-radius: var(--radius-sm); font-size: 0.85rem; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.5rem;">
                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 16 14 14 12 10 10 12"/></svg>
                <span>{{ session('status') }}</span>
            </div>
        @endif

        <form action="{{ route('password.email') }}" method="POST">
            @csrf

            <div style="margin-bottom: 2rem;">
                <label for="email" style="display: block; font-size: 0.85rem; font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem;">Alamat Email</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus
                    style="width: 100%; padding: 0.75rem 1rem; font-size: 0.9rem; border-radius: var(--radius-sm); border: 1px solid var(--border-color); background: var(--bg-primary); color: var(--text-primary); outline: none; transition: var(--transition-fast);"
                    placeholder="nama@email.com">
                @error('email')
                    <span style="color: var(--accent-danger); font-size: 0.8rem; margin-top: 0.25rem; display: block;">{{ $message }}</span>
                @enderror
            </div>

            <button type="submit" style="width: 100%; background: var(--accent-sky); color: #fff; border: none; padding: 0.85rem; font-size: 0.9rem; font-weight: 600; border-radius: var(--radius-sm); cursor: pointer; transition: var(--transition-smooth); box-shadow: 0 4px 12px rgba(2, 132, 199, 0.2);"
                onmouseenter="this.style.background='#0274b3'; this.style.transform='translateY(-1px)';"
                onmouseleave="this.style.background='var(--accent-sky)'; this.style.transform='none';">
                Kirim Tautan Reset
            </button>
        </form>

        <div style="text-align: center; margin-top: 2rem; border-top: 1px solid var(--border-color); padding-top: 1.5rem;">
            <p style="font-size: 0.85rem; color: var(--text-secondary);">
                Kembali ke <a href="{{ route('student.login') }}" style="color: var(--accent-sky); font-weight: 600; text-decoration: none;">Halaman Masuk</a>
            </p>
        </div>
    </div>
</div>
@endsection
