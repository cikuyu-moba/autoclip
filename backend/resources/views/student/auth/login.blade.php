@extends('layouts.app')

@section('title', 'Masuk Akun Siswa')

@section('content')
<div style="min-height: 80vh; display: flex; align-items: center; justify-content: center; padding: 3rem 1.5rem; background: radial-gradient(circle at top right, rgba(2, 132, 199, 0.05), transparent 60%);">
    <div style="max-width: 440px; width: 100%; background: var(--bg-secondary); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 2.5rem; box-shadow: var(--shadow-lg);">
        <div style="text-align: center; margin-bottom: 2.5rem;">
            <h1 style="font-size: 1.75rem; font-weight: 800; color: var(--text-primary); margin-bottom: 0.5rem; letter-spacing: -0.5px;">Selamat Datang</h1>
            <p style="color: var(--text-secondary); font-size: 0.9rem;">Masuk untuk melanjutkan pembelajaran Anda di Arhana.</p>
        </div>

        @if(session('error'))
            <div style="background: rgba(220, 38, 38, 0.08); border: 1px solid rgba(220, 38, 38, 0.2); color: var(--accent-danger); padding: 0.75rem 1rem; border-radius: var(--radius-sm); font-size: 0.85rem; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.5rem;">
                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <form action="{{ route('student.login.post') }}" method="POST">
            @csrf

            <div style="margin-bottom: 1.25rem;">
                <label for="email" style="display: block; font-size: 0.85rem; font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem;">Alamat Email</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus
                    style="width: 100%; padding: 0.75rem 1rem; font-size: 0.9rem; border-radius: var(--radius-sm); border: 1px solid var(--border-color); background: var(--bg-primary); color: var(--text-primary); outline: none; transition: var(--transition-fast);"
                    placeholder="nama@email.com">
                @error('email')
                    <span style="color: var(--accent-danger); font-size: 0.8rem; margin-top: 0.25rem; display: block;">{{ $message }}</span>
                @enderror
            </div>

            <div style="margin-bottom: 1.5rem;">
                <label for="password" style="display: block; font-size: 0.85rem; font-weight: 600; color: var(--text-primary); margin-bottom: 0.5rem;">Kata Sandi</label>
                <div style="position: relative;">
                    <input type="password" name="password" id="password" required
                        style="width: 100%; padding: 0.75rem 2.5rem 0.75rem 1rem; font-size: 0.9rem; border-radius: var(--radius-sm); border: 1px solid var(--border-color); background: var(--bg-primary); color: var(--text-primary); outline: none; transition: var(--transition-fast);"
                        placeholder="••••••••">
                    <button type="button" onclick="togglePasswordVisibility('password', this)" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: var(--text-muted); display: flex; align-items: center; padding: 0;">
                        <svg class="eye-open" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        <svg class="eye-closed" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="display: none;"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24M1 1l22 22"/></svg>
                    </button>
                </div>
            </div>

            <script>
                function togglePasswordVisibility(inputId, button) {
                    const input = document.getElementById(inputId);
                    const eyeOpen = button.querySelector('.eye-open');
                    const eyeClosed = button.querySelector('.eye-closed');
                    if (input.type === 'password') {
                        input.type = 'text';
                        eyeOpen.style.display = 'none';
                        eyeClosed.style.display = 'block';
                    } else {
                        input.type = 'password';
                        eyeOpen.style.display = 'block';
                        eyeClosed.style.display = 'none';
                    }
                }
            </script>

            <div style="margin-bottom: 2rem; display: flex; align-items: center; justify-content: space-between;">
                <label style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.85rem; color: var(--text-secondary); cursor: pointer;">
                    <input type="checkbox" name="remember" style="accent-color: var(--accent-sky); cursor: pointer;">
                    <span>Ingat Saya</span>
                </label>
                <a href="{{ route('password.request') }}" style="font-size: 0.85rem; color: var(--accent-sky); font-weight: 600; text-decoration: none;">Lupa Kata Sandi?</a>
            </div>

            <button type="submit" style="width: 100%; background: var(--accent-sky); color: #fff; border: none; padding: 0.85rem; font-size: 0.9rem; font-weight: 600; border-radius: var(--radius-sm); cursor: pointer; transition: var(--transition-smooth); box-shadow: 0 4px 12px rgba(2, 132, 199, 0.2);"
                onmouseenter="this.style.background='#0274b3'; this.style.transform='translateY(-1px)';"
                onmouseleave="this.style.background='var(--accent-sky)'; this.style.transform='none';">
                Masuk
            </button>
        </form>

        <div style="text-align: center; margin-top: 2rem; border-top: 1px solid var(--border-color); padding-top: 1.5rem;">
            <p style="font-size: 0.85rem; color: var(--text-secondary);">
                Belum memiliki akun? <a href="{{ route('student.register') }}" style="color: var(--accent-sky); font-weight: 600; text-decoration: none;">Daftar gratis</a>
            </p>
        </div>
    </div>
</div>
@endsection
