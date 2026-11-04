@extends('layouts.admin')

@section('title', 'Moderasi Komentar')
@section('page_title', 'Moderasi Komentar Pengunjung')

@section('admin_content')
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <div style="display: flex; gap: 0.5rem;">
            <a href="{{ route('admin.comments.index', ['status' => 'pending']) }}" 
               class="btn-admin {{ $status === 'pending' ? 'btn-admin-primary' : 'btn-admin-secondary' }}" 
               style="font-size: 0.85rem; padding: 0.6rem 1.25rem;">
                Menunggu Persetujuan ({{ \App\Models\Comment::where('is_approved', false)->count() }})
            </a>
            <a href="{{ route('admin.comments.index', ['status' => 'approved']) }}" 
               class="btn-admin {{ $status === 'approved' ? 'btn-admin-primary' : 'btn-admin-secondary' }}" 
               style="font-size: 0.85rem; padding: 0.6rem 1.25rem;">
                Disetujui ({{ \App\Models\Comment::where('is_approved', true)->count() }})
            </a>
        </div>
    </div>

    <div class="admin-card">
        <div class="admin-card-header">
            <h4 class="admin-card-title">
                Daftar Komentar - {{ $status === 'pending' ? 'Menunggu Persetujuan' : 'Disetujui & Publik' }}
            </h4>
        </div>

        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th style="width: 15%;">Pengirim</th>
                        <th style="width: 25%;">Artikel Berita</th>
                        <th style="width: 40%;">Isi Komentar</th>
                        <th style="width: 20%; text-align: right;">Aksi / Balas</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($comments as $comment)
                        <tr>
                            <td>
                                <strong style="color: var(--text-admin-primary); display: block;">{{ $comment->username }}</strong>
                                @if($comment->user_id)
                                    <span style="font-size: 0.65rem; background-color: rgba(56, 189, 248, 0.15); color: var(--accent-blue); padding: 1px 4px; border-radius: 4px; font-weight: 700; text-transform: uppercase;">ADMIN</span>
                                @else
                                    <span style="font-size: 0.7rem; color: var(--text-admin-muted);">Pengunjung</span>
                                @endif
                                <span style="font-size: 0.7rem; color: var(--text-admin-muted); display: block; margin-top: 0.25rem;">
                                    {{ $comment->created_at->format('d M Y, H:i') }}
                                </span>
                            </td>
                            <td>
                                @if($comment->post)
                                    <a href="{{ route('news.show', $comment->post->slug) }}" target="_blank" style="color: var(--accent-blue); text-decoration: underline; font-weight: 500;">
                                        {{ Str::limit($comment->post->title, 40) }}
                                    </a>
                                @else
                                    <span style="color: var(--text-admin-muted); font-style: italic;">Artikel dihapus</span>
                                @endif
                            </td>
                            <td>
                                <div style="color: var(--text-admin-primary); line-height: 1.5; white-space: pre-wrap;">{{ $comment->content }}</div>
                                @if($comment->parent)
                                    <div style="margin-top: 0.5rem; padding: 0.5rem; border-left: 2px solid var(--border-admin-hover); background-color: rgba(255,255,255,0.01); font-size: 0.75rem; color: var(--text-admin-secondary);">
                                        Membalas komentar <strong>{{ $comment->parent->username }}</strong>: 
                                        <div style="font-style: italic;">"{{ Str::limit($comment->parent->content, 60) }}"</div>
                                    </div>
                                @endif
                            </td>
                            <td class="actions-cell">
                                <div style="display: flex; gap: 0.5rem; justify-content: flex-end; align-items: center; flex-wrap: wrap;">
                                    @if(!$comment->is_approved)
                                        <form action="{{ route('admin.comments.approve', $comment->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" class="btn-admin btn-admin-primary btn-admin-sm" style="background-color: var(--accent-emerald); color: #000;">Setujui</button>
                                        </form>
                                    @endif

                                    <!-- Trigger Reply Modal -->
                                    <button type="button" class="btn-admin btn-admin-secondary btn-admin-sm" onclick="openAdminReplyModal({{ $comment->id }}, '{{ addslashes($comment->username) }}', '{{ addslashes(Str::limit($comment->content, 120)) }}')">Balas</button>

                                    <form action="{{ route('admin.comments.reject', $comment->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus/menolak komentar ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-admin btn-admin-danger btn-admin-sm">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" style="text-align: center; color: var(--text-admin-secondary); padding: 3rem 1rem;">
                                Tidak ada komentar dalam kategori ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($comments->hasPages())
            <div style="margin-top: 2rem;">
                {{ $comments->links() }}
            </div>
        @endif
    </div>

    <!-- Premium Floating Admin Reply Modal -->
    <div id="adminReplyModal" style="display: none; position: fixed; inset: 0; background: rgba(0, 0, 0, 0.6); backdrop-filter: blur(8px); z-index: 9999; justify-content: center; align-items: center; padding: 1.5rem; opacity: 0; transition: opacity 0.3s ease;">
        <div style="background: var(--bg-admin-sidebar); border: 1px solid var(--border-admin-hover); border-radius: var(--radius-md); max-width: 500px; width: 100%; box-shadow: var(--shadow-admin); overflow: hidden; transform: scale(0.9); transition: transform 0.3s ease;">
            <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--border-admin); display: flex; justify-content: space-between; align-items: center;">
                <h4 style="font-weight: 700; font-size: 1.05rem; color: var(--text-admin-primary); margin: 0;">Balas Komentar Pengunjung</h4>
                <button type="button" onclick="closeAdminReplyModal()" style="background: none; border: none; cursor: pointer; color: var(--text-admin-secondary); display: flex; align-items: center;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                </button>
            </div>
            
            <form id="adminReplyForm" method="POST">
                @csrf
                <div style="padding: 1.5rem; display: flex; flex-direction: column; gap: 1.25rem;">
                    <div style="background: rgba(255, 255, 255, 0.015); border: 1px solid var(--border-admin); padding: 1rem; border-radius: var(--radius-sm);">
                        <span style="font-size: 0.75rem; color: var(--text-admin-muted); text-transform: uppercase; font-weight: 700; letter-spacing: 0.5px; display: block; margin-bottom: 0.35rem;">Komentar Pengirim (<span id="modalTargetUser"></span>)</span>
                        <p id="modalTargetContent" style="font-size: 0.875rem; color: var(--text-admin-secondary); line-height: 1.5; font-style: italic; margin: 0; white-space: pre-wrap;"></p>
                    </div>

                    <div class="form-group" style="margin: 0;">
                        <label for="replyModalContent" class="form-label" style="font-weight: 600;">Isi Balasan Admin</label>
                        <textarea name="content" id="replyModalContent" class="form-control" style="min-height: 120px;" placeholder="Tulis tanggapan atau jawaban resmi Anda di sini..." required></textarea>
                    </div>
                </div>

                <div style="padding: 1.25rem 1.5rem; background: rgba(0,0,0,0.1); border-top: 1px solid var(--border-admin); display: flex; justify-content: flex-end; gap: 0.75rem;">
                    <button type="button" class="btn-admin btn-admin-secondary" onclick="closeAdminReplyModal()">Batal</button>
                    <button type="submit" class="btn-admin btn-admin-primary">Kirim Balasan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openAdminReplyModal(commentId, username, contentText) {
            const modal = document.getElementById('adminReplyModal');
            const container = modal.querySelector('div');
            const form = document.getElementById('adminReplyForm');
            
            document.getElementById('modalTargetUser').textContent = username;
            document.getElementById('modalTargetContent').textContent = `"${contentText}"`;
            
            // Set dynamic action route path
            form.action = `/admin/comments/${commentId}/reply`;
            
            modal.style.display = 'flex';
            setTimeout(() => {
                modal.style.opacity = '1';
                container.style.transform = 'scale(1)';
            }, 10);
            
            document.getElementById('replyModalContent').focus();
        }

        function closeAdminReplyModal() {
            const modal = document.getElementById('adminReplyModal');
            const container = modal.querySelector('div');
            
            modal.style.opacity = '0';
            container.style.transform = 'scale(0.9)';
            
            setTimeout(() => {
                modal.style.display = 'none';
                document.getElementById('replyModalContent').value = '';
            }, 300);
        }

        // Close if clicking outside
        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById('adminReplyModal');
            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    closeAdminReplyModal();
                }
            });
        });
    </script>
@endsection
