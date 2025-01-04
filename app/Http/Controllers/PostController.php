<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::with(['user', 'comments.user', 'likes'])->latest()->get();
        return response()->json($posts);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        $post = Post::create([
            'content' => $validated['content'],
            'user_id' => auth()->id()
        ]);

        return response()->json($post->load(['user', 'comments', 'likes']));
    }

    public function destroy(Post $post)
    {
        $this->authorize('delete', $post);
        $post->delete();
        return response()->json(['message' => 'Post deleted']);
    }

    public function update(Request $request, Post $post)
    {
        // Check if user is authorized to update this post
        if ($request->user()->id !== $post->user_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        $post->update([
            'content' => $validated['content']
        ]);

        return response()->json([
            'message' => 'Post updated successfully',
            'post' => $post
        ]);
    }
}
