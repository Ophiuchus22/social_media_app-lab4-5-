<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Notification;
use App\Events\NewNotification;

class LikeController extends Controller
{
    public function toggle(Post $post)
    {
        $like = $post->likes()->where('user_id', auth()->id())->first();

        if ($like) {
            $like->delete();
            return response()->json(['liked' => false]);
        }

        $post->likes()->create(['user_id' => auth()->id()]);

        // Create notification for post owner
        if ($post->user_id !== auth()->id()) {
            $notification = Notification::create([
                'user_id' => $post->user_id,
                'post_id' => $post->id,
                'type' => 'like',
                'message' => auth()->user()->name . ' liked your post'
            ]);

            broadcast(new NewNotification($notification))->toOthers();
        }

        return response()->json(['liked' => true]);
    }
}
