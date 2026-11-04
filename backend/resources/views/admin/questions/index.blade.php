@extends('layouts.admin')

@section('title', 'Soal Ujian')
@section('page_title', 'Kelola Soal Ujian (Pilihan Ganda)')

@section('admin_content')
    <div class="admin-card">
        <div class="admin-card-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
            
            <!-- Course Filter Form -->
            <form action="{{ route('admin.questions.index') }}" method="GET" style="display: flex; align-items: center; gap: 0.75rem;">
                <label for="course_id" style="font-weight: 700; color: var(--text-admin-primary); font-size: 0.9rem;">Pilih Kursus:</label>
                <select name="course_id" id="course_id" class="form-control" onchange="this.form.submit()" style="max-width: 250px;">
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}" {{ $course->id == $selectedCourseId ? 'selected' : '' }}>
                            {{ $course->title }}
                        </option>
                    @endforeach
                </select>
            </form>

            <a href="{{ route('admin.questions.create') }}" class="btn-admin btn-admin-primary">
                + Tambah Soal Ujian
            </a>
        </div>
        
        @if(session('success'))
            <div style="background: rgba(5, 150, 105, 0.08); border: 1px solid rgba(5, 150, 105, 0.2); color: var(--accent-emerald); padding: 0.75rem 1rem; border-radius: var(--radius-sm); font-size: 0.85rem; margin-bottom: 1.5rem;">
                {{ session('success') }}
            </div>
        @endif

        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Tipe Tes</th>
                        <th>Pertanyaan / Soal</th>
                        <th>Pilihan Jawaban</th>
                        <th>Jawaban Benar</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($questions as $question)
                        <tr>
                            <td>
                                @if($question->type === 'pre_test')
                                    <span class="badge-admin" style="background: rgba(56, 189, 248, 0.1); color: var(--accent-sky); border: 1px solid rgba(56, 189, 248, 0.2); padding: 3px 8px; border-radius: 4px; font-size: 0.75rem; font-weight: 600; text-transform: uppercase;">Pre Test</span>
                                @elseif($question->type === 'post_test')
                                    <span class="badge-admin" style="background: rgba(245, 158, 11, 0.1); color: var(--accent-amber); border: 1px solid rgba(245, 158, 11, 0.2); padding: 3px 8px; border-radius: 4px; font-size: 0.75rem; font-weight: 600; text-transform: uppercase;">Post Test</span>
                                @else
                                    <span class="badge-admin" style="background: rgba(16, 185, 129, 0.1); color: var(--accent-emerald); border: 1px solid rgba(16, 185, 129, 0.2); padding: 3px 8px; border-radius: 4px; font-size: 0.75rem; font-weight: 600; text-transform: uppercase;">Pre & Post Test</span>
                                @endif
                            </td>
                            <td style="font-weight: 600; max-width: 300px;">{{ Str::limit($question->question_text, 100) }}</td>
                            <td>
                                <ul style="list-style-type: disc; padding-left: 1.25rem; font-size: 0.85rem; margin: 0; color: var(--text-admin-secondary);">
                                    @foreach($question->options as $opt)
                                        <li>{{ $opt }}</li>
                                    @endforeach
                                </ul>
                            </td>
                            <td>
                                <span style="color: var(--accent-emerald); font-weight: 600; font-size: 0.9rem;">
                                    {{ $question->options[$question->correct_option] ?? '' }}
                                </span>
                            </td>
                            <td class="actions-cell">
                                <a href="{{ route('admin.questions.edit', $question->id) }}" class="btn-admin btn-admin-secondary btn-admin-sm">
                                    Edit
                                </a>
                                <form action="{{ route('admin.questions.destroy', $question->id) }}" method="POST" 
                                      onsubmit="return confirm('Apakah Anda yakin ingin menghapus soal ini?')"
                                      style="display: inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-admin btn-admin-danger btn-admin-sm">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align: center; color: var(--text-admin-secondary); padding: 2rem 0;">Belum ada soal ujian untuk kursus yang dipilih.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
