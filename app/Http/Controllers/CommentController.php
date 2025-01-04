<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use App\Models\Notification;
use App\Events\NewNotification;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(Request $request, Post $post)
    {
        $validated = $request->validate([
            'content' => 'required|string|max:500',
        ]);

        $comment = $post->comments()->create([
            'content' => $validated['content'],
            'user_id' => auth()->id()
        ]);

        // Create notification for post owner
        if ($post->user_id !== auth()->id()) {
            $notification = Notification::create([
                'user_id' => $post->user_id,
                'post_id' => $post->id,
                'type' => 'comment',
                'message' => auth()->user()->name . ' commented on your post'
            ]);

            broadcast(new NewNotification($notification))->toOthers();
        }

        return response()->json($comment->load('user'));
    }

    public function destroy(Comment $comment)
    {
        $this->authorize('delete', $comment);
        
        $comment->delete();
        
        return response()->json(['message' => 'Comment deleted successfully']);
    }
}
