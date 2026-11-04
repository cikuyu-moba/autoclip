<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Comment;

class CommentController extends Controller
{
    public function store(Request $request, $postId)
    {
        $post = Post::findOrFail($postId);

        $request->validate([
            'username' => 'nullable|string|max:50',
            'content' => 'required|string|max:1000',
            'parent_id' => 'nullable|exists:comments,id',
        ]);

        // 1. Get or set username cookie
        $username = $request->username;
        if (empty($username)) {
            // Read from cookie
            $username = $request->cookie('arhana_comment_username');
        }

        if (empty($username)) {
            $username = 'Guest_' . rand(1000, 9999);
        }

        // Clean username
        $username = strip_tags($username);
        $username = \Illuminate\Support\Str::limit($username, 30, '');

        // 2. Determine if user is logged in admin to bypass approval
        $isAdminReply = auth()->check();
        $isApproved = $isAdminReply; // Admin replies are auto-approved

        $comment = Comment::create([
            'post_id' => $post->id,
            'parent_id' => $request->parent_id,
            'username' => $isAdminReply ? auth()->user()->name : $username,
            'user_id' => $isAdminReply ? auth()->id() : null,
            'content' => strip_tags($request->content),
            'is_approved' => $isApproved
        ]);

        $cookie = cookie('arhana_comment_username', $username, 60 * 24 * 365); // 1 year cookie

        $message = $isApproved 
            ? 'Komentar Anda berhasil dipublikasikan!' 
            : 'Komentar Anda berhasil dikirim! Menunggu persetujuan admin sebelum tampil publik.';

        return redirect()->back()
            ->with('success', $message)
            ->withCookie($cookie);
    }
}
