@extends('layouts.admin')

@section('title', 'Tambah Soal')
@section('page_title', 'Tambah Soal Pilihan Ganda')

@section('admin_content')
    <div class="admin-card" style="max-width: 700px; margin: 0 auto;">
        <div class="admin-card-header" style="margin-bottom: 2rem;">
            <h4 class="admin-card-title">Form Tambah Soal Ujian</h4>
        </div>

        <form action="{{ route('admin.questions.store') }}" method="POST">
            @csrf

            <!-- Course Selection -->
            <div class="form-group" style="margin-bottom: 2rem;">
                <label for="course_id" class="form-label">Kursus Pelajaran</label>
                <select name="course_id" id="course_id" class="form-control" required>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}" {{ old('course_id') == $course->id ? 'selected' : '' }}>
                            {{ $course->title }}
                        </option>
                    @endforeach
                </select>
                @error('course_id')
                    <span class="error-feedback">{{ $message }}</span>
                @enderror
            </div>

            <!-- Exam Type Selection -->
            <div class="form-group" style="margin-bottom: 2rem;">
                <label class="form-label" style="display: block; margin-bottom: 0.75rem;">Tipe Ujian</label>
                <div style="display: flex; gap: 1.5rem; flex-wrap: wrap; margin-top: 0.5rem;">
                    <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; color: var(--text-admin-primary);">
                        <input type="radio" name="type" value="both" {{ old('type', 'both') == 'both' ? 'checked' : '' }} required style="accent-color: var(--accent-emerald); width: 1.1rem; height: 1.1rem;">
                        <span>Pre Test & Post Test</span>
                    </label>
                    <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; color: var(--text-admin-primary);">
                        <input type="radio" name="type" value="pre_test" {{ old('type') == 'pre_test' ? 'checked' : '' }} required style="accent-color: var(--accent-emerald); width: 1.1rem; height: 1.1rem;">
                        <span>Pre Test (Ujian Awal)</span>
                    </label>
                    <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; color: var(--text-admin-primary);">
                        <input type="radio" name="type" value="post_test" {{ old('type') == 'post_test' ? 'checked' : '' }} required style="accent-color: var(--accent-emerald); width: 1.1rem; height: 1.1rem;">
                        <span>Post Test (Ujian Akhir)</span>
                    </label>
                </div>
                @error('type')
                    <span class="error-feedback" style="display: block; margin-top: 0.5rem;">{{ $message }}</span>
                @enderror
            </div>

            <!-- Question text area -->
            <div class="form-group" style="margin-bottom: 1.5rem;">
                <label for="question_text" class="form-label">Pertanyaan / Soal</label>
                <textarea name="question_text" id="question_text" class="form-control" rows="4" placeholder="Tuliskan isi pertanyaan di sini..." required>{{ old('question_text') }}</textarea>
                @error('question_text')
                    <span class="error-feedback">{{ $message }}</span>
                @enderror
            </div>

            <!-- Multiple Choice Options -->
            <div style="background: var(--bg-admin-card-header); padding: 1.5rem; border-radius: var(--radius-sm); border: 1px solid var(--border-admin); margin-bottom: 2rem;">
                <h5 style="margin-top: 0; margin-bottom: 1rem; color: var(--text-admin-primary);">Pilihan Jawaban (Pilihan Ganda):</h5>
                
                <div class="form-group" style="margin-bottom: 1.5rem;">
                    <label for="correct_answer" class="form-label" style="color: var(--accent-emerald); font-weight: 700; display: flex; align-items: center; gap: 0.5rem;">
                        <span style="background: rgba(16, 185, 129, 0.1); color: var(--accent-emerald); padding: 2px 8px; border-radius: 4px; font-size: 0.75rem;">Benar</span>
                        Jawaban yang Benar
                    </label>
                    <input type="text" id="correct_answer" name="correct_answer" class="form-control" style="border-color: var(--accent-emerald);" placeholder="Tuliskan jawaban yang benar di sini..." value="{{ old('correct_answer') }}" required>
                    @error('correct_answer')
                        <span class="error-feedback">{{ $message }}</span>
                    @enderror
                </div>
                
                <div style="border-top: 1px solid var(--border-admin); margin: 1.5rem 0; padding-top: 1rem;">
                    <label class="form-label" style="color: var(--accent-amber); font-weight: 700; display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1rem;">
                        <span style="background: rgba(245, 158, 11, 0.1); color: var(--accent-amber); padding: 2px 8px; border-radius: 4px; font-size: 0.75rem;">Salah</span>
                        3 Jawaban Salah / Pengecoh
                    </label>
                    
                    <div class="form-group" style="margin-bottom: 1rem; display: flex; align-items: center; gap: 1rem;">
                        <span style="font-weight: 700; width: 30px; color: var(--text-admin-secondary);">1.</span>
                        <input type="text" name="wrong_answers[0]" class="form-control" placeholder="Tulis jawaban salah pertama" value="{{ old('wrong_answers.0') }}" required>
                    </div>
                    
                    <div class="form-group" style="margin-bottom: 1rem; display: flex; align-items: center; gap: 1rem;">
                        <span style="font-weight: 700; width: 30px; color: var(--text-admin-secondary);">2.</span>
                        <input type="text" name="wrong_answers[1]" class="form-control" placeholder="Tulis jawaban salah kedua" value="{{ old('wrong_answers.1') }}" required>
                    </div>
                    
                    <div class="form-group" style="margin-bottom: 0; display: flex; align-items: center; gap: 1rem;">
                        <span style="font-weight: 700; width: 30px; color: var(--text-admin-secondary);">3.</span>
                        <input type="text" name="wrong_answers[2]" class="form-control" placeholder="Tulis jawaban salah ketiga" value="{{ old('wrong_answers.2') }}" required>
                    </div>
                    
                    @error('wrong_answers')
                        <span class="error-feedback" style="margin-top: 0.5rem; display: block;">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div style="display: flex; gap: 0.75rem;">
                <button type="submit" class="btn-admin btn-admin-primary">Simpan Soal</button>
                <a href="{{ route('admin.questions.index') }}" class="btn-admin btn-admin-secondary">Batal</a>
            </div>
        </form>
    </div>
@endsection
