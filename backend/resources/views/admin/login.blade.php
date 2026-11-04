<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - Arhana CMS</title>
    
    <!-- Admin Styling -->
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <link rel="icon" type="image/png" href="{{ asset('logo/logo.png') }}">
</head>
<body class="login-page">

    <div class="login-card">
        <div class="login-header" style="display: flex; flex-direction: column; align-items: center; text-align: center; margin-bottom: 1.5rem;">
            <img src="{{ asset('logo/logo.png') }}" alt="Arhana Logo" style="height: 80px; width: auto; object-fit: contain; filter: invert(1); mix-blend-mode: screen; margin-bottom: 0.5rem;">
            <h1 class="login-logo" style="margin-top: 0.5rem; margin-bottom: 0.25rem;">
                ARHANA<span>.</span>CMS
            </h1>
            <p class="login-subtitle">Silakan login untuk mengelola konten website</p>
        </div>
        
        <form action="{{ route('login.post') }}" method="POST">
            @csrf
            
            <div class="form-group">
                <label for="email" class="form-label">Alamat Email</label>
                <input type="email" name="email" id="email" 
                       class="form-control @error('email') is-invalid @enderror" 
                       placeholder="nama@email.com" value="{{ old('email') }}" required autofocus>
                @error('email')
                    <span class="error-feedback">{{ $message }}</span>
                @enderror
            </div>
            
            <div class="form-group" style="margin-bottom: 2rem;">
                <label for="password" class="form-label">Kata Sandi</label>
                <div style="position: relative;">
                    <input type="password" name="password" id="password" 
                           class="form-control" placeholder="••••••••" style="padding-right: 2.5rem;" required>
                    <button type="button" onclick="togglePasswordVisibility('password', this)" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: var(--text-admin-secondary, #94a3b8); display: flex; align-items: center; padding: 0;">
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
            
            <div class="form-group" style="margin-bottom: 2.5rem; display: flex; justify-content: space-between; align-items: center;">
                <label class="checkbox-label">
                    <input type="checkbox" name="remember">
                    <span>Ingat Saya</span>
                </label>
            </div>
            
            <button type="submit" class="btn-admin btn-admin-primary" style="width: 100%; padding: 0.8rem; font-size: 0.9rem; border-radius: var(--radius-sm);">
                Masuk ke Dashboard
            </button>
        </form>
    </div>

</body>
</html>
