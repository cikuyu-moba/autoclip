@extends('layouts.app')

@section('title', 'Ujian: ' . ($type === 'pre_test' ? 'Pre Test' : 'Post Test'))

@section('content')
<div style="background: radial-gradient(circle at top right, rgba(2, 132, 199, 0.05), transparent 60%); padding: 3rem 0; min-height: 90vh;">
    <div style="max-width: 800px; margin: 0 auto; padding: 0 1.5rem;">
        
        <!-- Header -->
        <div style="margin-bottom: 2.5rem; display: flex; justify-content: space-between; align-items: flex-end; border-bottom: 1px solid var(--border-color); padding-bottom: 1.5rem;">
            <div>
                <a href="{{ route('courses.show', $course->slug) }}" style="display: inline-flex; align-items: center; gap: 0.5rem; font-size: 0.85rem; color: var(--text-muted); margin-bottom: 0.75rem; transition: var(--transition-fast);" onmouseenter="this.style.color='var(--accent-sky)'" onmouseleave="this.style.color='var(--text-muted)'">
                    <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
                    Kembali ke Detail Kursus
                </a>
                <h1 style="font-size: 2rem; font-weight: 800; color: var(--text-primary); letter-spacing: -0.5px;">
                    {{ $type === 'pre_test' ? 'Pre Test' : 'Post Test' }} - {{ $course->title }}
                </h1>
            </div>
            <div style="font-size: 0.9rem; font-weight: 700; color: var(--text-secondary); background: var(--bg-secondary); border: 1px solid var(--border-color); padding: 0.5rem 1rem; border-radius: var(--radius-sm);">
                Total: {{ $questions->count() }} Soal
            </div>
        </div>

        <!-- Quiz Form -->
        <form id="quizForm" style="display: flex; flex-direction: column; gap: 2rem;">
            @csrf
            
            @foreach($questions as $index => $question)
                <div style="background: var(--bg-secondary); border: 1px solid var(--border-color); border-radius: var(--radius-md); padding: 2rem; box-shadow: var(--shadow-sm);">
                    <div style="display: flex; gap: 0.75rem; align-items: flex-start; margin-bottom: 1.25rem;">
                        <span style="background: rgba(2, 132, 199, 0.08); color: var(--accent-sky); font-weight: 800; font-size: 0.85rem; padding: 0.25rem 0.6rem; border-radius: 4px; display: inline-block;">
                            Soal {{ $index + 1 }}
                        </span>
                    </div>
                    <p style="font-size: 1.05rem; font-weight: 600; color: var(--text-primary); margin-bottom: 1.5rem; line-height: 1.5;">
                        {{ $question->question_text }}
                    </p>

                    <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                        @foreach($question->options as $optIndex => $option)
                            <label style="display: flex; align-items: center; gap: 0.85rem; background: var(--bg-primary); border: 1px solid var(--border-color); padding: 1rem; border-radius: var(--radius-sm); cursor: pointer; transition: var(--transition-fast); position: relative;"
                                   onmouseenter="this.style.borderColor='rgba(2, 132, 199, 0.3)'; this.style.background='rgba(2, 132, 199, 0.02)';"
                                   onmouseleave="if(!this.querySelector('input').checked) { this.style.borderColor='var(--border-color)'; this.style.background='var(--bg-primary)'; }">
                                <input type="radio" name="answers[{{ $question->id }}]" value="{{ $option }}" required style="accent-color: var(--accent-sky); width: 1.1rem; height: 1.1rem; cursor: pointer;"
                                       onclick="selectOption(this)">
                                <span style="font-size: 0.95rem; color: var(--text-secondary); font-weight: 500;">
                                    {{ chr(65 + $optIndex) }}. {{ $option }}
                                </span>
                            </label>
                        @endforeach
                    </div>
                </div>
            @endforeach

            <!-- Action Button -->
            <div style="margin-top: 1rem; display: flex; justify-content: flex-end;">
                <button type="submit" id="submitBtn" style="background: var(--accent-sky); color: #fff; border: none; padding: 1rem 2.5rem; font-size: 1rem; font-weight: 700; border-radius: var(--radius-sm); cursor: pointer; transition: var(--transition-smooth); box-shadow: 0 4px 12px rgba(2, 132, 199, 0.2);"
                        onmouseenter="this.style.background='#0274b3'; this.style.transform='translateY(-1px)';"
                        onmouseleave="this.style.background='var(--accent-sky)'; this.style.transform='none';">
                    Kirim Jawaban
                </button>
            </div>
        </form>

    </div>
</div>

<!-- Glassmorphism Modal Result -->
<div id="resultModal" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(8px); z-index: 10000; align-items: center; justify-content: center; padding: 1.5rem;">
    <div style="background: rgba(15, 23, 42, 0.9); border: 1px solid rgba(255, 255, 255, 0.08); max-width: 500px; width: 100%; border-radius: var(--radius-md); padding: 2.5rem; box-shadow: 0 20px 50px rgba(0,0,0,0.5); text-align: center; color: #fff;">
        
        <div id="modalIconContainer" style="margin-bottom: 1.5rem;">
            <!-- Render icon dynamically -->
        </div>

        <h3 id="modalTitle" style="font-size: 1.75rem; font-weight: 800; color: #fff; margin-bottom: 0.5rem; letter-spacing: -0.5px;">Hasil Ujian</h3>
        
        <!-- Score Circle -->
        <div style="display: inline-flex; align-items: center; justify-content: center; width: 100px; height: 100px; border-radius: 50%; background: rgba(255,255,255,0.05); border: 3px solid var(--accent-sky); margin: 1rem auto 1.5rem auto;">
            <span id="modalScore" style="font-size: 2.25rem; font-weight: 800; color: #fff;">0</span>
        </div>

        <!-- Comparison Stats if Post Test -->
        <div id="comparisonContainer" style="display: none; background: rgba(255, 255, 255, 0.04); border-radius: var(--radius-sm); padding: 1rem; margin-bottom: 1.5rem; border: 1px solid rgba(255,255,255,0.05);">
            <div style="display: flex; justify-content: space-between; font-size: 0.85rem; color: #94a3b8;">
                <span>Skor Pre Test:</span>
                <span id="preTestCompScore" style="font-weight: 700; color: #fff;">-</span>
            </div>
            <div style="display: flex; justify-content: space-between; font-size: 0.85rem; color: #94a3b8; margin-top: 0.5rem; border-top: 1px solid rgba(255,255,255,0.05); padding-top: 0.5rem;">
                <span>Skor Post Test:</span>
                <span id="postTestCompScore" style="font-weight: 700; color: var(--accent-emerald);">0</span>
            </div>
        </div>

        <p id="modalMessage" style="color: #cbd5e1; font-size: 0.95rem; line-height: 1.6; margin-bottom: 2rem;">
            Rekomendasi tindakan belajar Anda.
        </p>

        <div id="modalActions" style="display: flex; gap: 0.75rem; justify-content: center;">
            <!-- Render actions dynamically -->
        </div>

    </div>
</div>

<script>
    function selectOption(radio) {
        // Find parent container labels and reset background/borders
        const container = radio.closest('div');
        const labels = container.querySelectorAll('label');
        labels.forEach(lbl => {
            lbl.style.borderColor = 'var(--border-color)';
            lbl.style.background = 'var(--bg-primary)';
        });
        
        // Highlight chosen label
        const chosenLabel = radio.closest('label');
        chosenLabel.style.borderColor = 'var(--accent-sky)';
        chosenLabel.style.background = 'rgba(2, 132, 199, 0.04)';
    }

    document.getElementById('quizForm').addEventListener('submit', function (e) {
        e.preventDefault();
        
        const submitBtn = document.getElementById('submitBtn');
        submitBtn.disabled = true;
        submitBtn.innerText = 'Mengirim...';

        const formData = new FormData(this);
        
        fetch('{{ route("courses.test.submit", [$course->slug, $type]) }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Populate Modal details
                document.getElementById('modalScore').innerText = data.score;
                document.getElementById('modalMessage').innerText = data.message;
                
                const iconContainer = document.getElementById('modalIconContainer');
                const actionsContainer = document.getElementById('modalActions');
                const comparisonContainer = document.getElementById('comparisonContainer');
                
                // Reset containers
                iconContainer.innerHTML = '';
                actionsContainer.innerHTML = '';
                comparisonContainer.style.display = 'none';

                // Icons
                const successIcon = `<svg width="48" height="48" fill="none" viewBox="0 0 24 24" stroke="var(--accent-emerald)" stroke-width="2.5" style="background: rgba(5, 150, 105, 0.1); border-radius: 50%; padding: 0.5rem; display: inline-block;"><polyline points="20 6 9 17 4 12"/></svg>`;
                const warningIcon = `<svg width="48" height="48" fill="none" viewBox="0 0 24 24" stroke="var(--accent-amber)" stroke-width="2.5" style="background: rgba(217, 119, 6, 0.1); border-radius: 50%; padding: 0.5rem; display: inline-block;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>`;

                if (data.score === 100) {
                    iconContainer.innerHTML = successIcon;
                    document.getElementById('modalTitle').innerText = 'Luar Biasa!';
                } else {
                    iconContainer.innerHTML = warningIcon;
                    document.getElementById('modalTitle').innerText = 'Tetap Semangat!';
                }

                const type = '{{ $type }}';
                if (type === 'pre_test') {
                    if (data.score === 100) {
                        actionsContainer.innerHTML = `
                            <a href="{{ route('courses.show', $course->slug) }}" style="flex: 1; display: inline-block; background: var(--accent-sky); color: #fff; padding: 0.75rem 1.5rem; border-radius: var(--radius-sm); font-weight: 700; text-decoration: none; font-size: 0.9rem;">Lanjut Kursus</a>
                        `;
                    } else {
                        actionsContainer.innerHTML = `
                            <a href="{{ route('courses.show', $course->slug) }}" style="flex: 1; display: inline-block; background: var(--accent-sky); color: #fff; padding: 0.75rem 1.5rem; border-radius: var(--radius-sm); font-weight: 700; text-decoration: none; font-size: 0.9rem;">Tutup & Mulai Belajar</a>
                        `;
                    }
                } else { // post_test
                    comparisonContainer.style.display = 'block';
                    document.getElementById('preTestCompScore').innerText = data.compare_pre_test !== null ? data.compare_pre_test + '/100' : 'Belum Tes';
                    document.getElementById('postTestCompScore').innerText = data.score + '/100';

                    if (data.score === 100) {
                        actionsContainer.innerHTML = `
                            <a href="{{ route('courses.show', $course->slug) }}" style="flex: 1; display: inline-block; background: var(--accent-emerald); color: #fff; padding: 0.75rem 1.5rem; border-radius: var(--radius-sm); font-weight: 700; text-decoration: none; font-size: 0.9rem;">Selesai</a>
                        `;
                    } else {
                        actionsContainer.innerHTML = `
                            <a href="{{ route('courses.show', $course->slug) }}" style="flex: 1; display: inline-block; background: transparent; color: #cbd5e1; border: 1px solid rgba(255,255,255,0.2); padding: 0.75rem 1.5rem; border-radius: var(--radius-sm); font-weight: 600; text-decoration: none; font-size: 0.9rem;">Belajar Lagi</a>
                            <a href="" style="flex: 1; display: inline-block; background: var(--accent-sky); color: #fff; padding: 0.75rem 1.5rem; border-radius: var(--radius-sm); font-weight: 700; text-decoration: none; font-size: 0.9rem;">Ulangi Tes</a>
                        `;
                    }
                }

                // Show modal
                const modal = document.getElementById('resultModal');
                modal.style.display = 'flex';
            }
        })
        .catch(error => {
            console.error(error);
            alert('Terjadi kesalahan saat mengirim jawaban.');
            submitBtn.disabled = false;
            submitBtn.innerText = 'Kirim Jawaban';
        });
    });
</script>
@endsection
