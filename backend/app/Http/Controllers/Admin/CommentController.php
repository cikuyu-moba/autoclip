<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Comment;

class CommentController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status', 'pending');
        
        $query = Comment::with(['post', 'user'])->latest();

        if ($status === 'approved') {
            $query->where('is_approved', true);
        } elseif ($status === 'pending') {
            $query->where('is_approved', false);
        }

        $comments = $query->paginate(20)->withQueryString();
        
        return view('admin.comments.index', compact('comments', 'status'));
    }

    public function approve($id)
    {
        $comment = Comment::findOrFail($id);
        $comment->update(['is_approved' => true]);

        return redirect()->back()->with('success', 'Komentar berhasil disetujui!');
    }

    public function reject($id)
    {
        $comment = Comment::findOrFail($id);
        $comment->delete();

        return redirect()->back()->with('success', 'Komentar berhasil ditolak/dihapus.');
    }

    public function reply(Request $request, $id)
    {
        $parent = Comment::findOrFail($id);

        $request->validate([
            'content' => 'required|string|max:1000'
        ]);

        Comment::create([
            'post_id' => $parent->post_id,
            'parent_id' => $parent->id,
            'username' => auth()->user()->name,
            'user_id' => auth()->id(),
            'content' => strip_tags($request->content),
            'is_approved' => true // Admin replies are auto approved
        ]);

        // Auto approve parent too if not yet approved when admin replies
        if (!$parent->is_approved) {
            $parent->update(['is_approved' => true]);
        }

        return redirect()->back()->with('success', 'Balasan komentar berhasil dikirim!');
    }
}
