<?php

namespace App\Http\Controllers;

use App\Models\Post;

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
        return response()->json(['liked' => true]);
    }
}
