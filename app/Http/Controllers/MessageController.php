<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Events\NewMessage;

class MessageController extends Controller
{
    public function index()
    {
        $users = User::where('id', '!=', Auth::id())
            ->select('id', 'name', 'profile_picture')
            ->orderBy('name')
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'profile_picture' => $user->profile_picture 
                        ? Storage::url($user->profile_picture) 
                        : asset('storage/profile-pictures/default-avatar.jpg')
                ];
            });
        
        return view('messages.index', compact('users'));
    }

    public function getMessages(User $user)
    {
        $messages = Message::where(function($query) use ($user) {
            $query->where('sender_id', Auth::id())
                  ->where('receiver_id', $user->id);
        })->orWhere(function($query) use ($user) {
            $query->where('sender_id', $user->id)
                  ->where('receiver_id', Auth::id());
        })->with(['sender', 'receiver'])->orderBy('created_at', 'asc')->get();

        return response()->json($messages);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'content' => 'required|string'
        ]);

        $message = Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $validated['receiver_id'],
            'content' => $validated['content']
        ]);

        $message->load(['sender', 'receiver']);
        
        // Broadcast the new message event
        broadcast(new NewMessage($message))->toOthers();

        return response()->json($message);
    }

    public function markAsRead(User $user)
    {
        Message::where('sender_id', $user->id)
              ->where('receiver_id', Auth::id())
              ->whereNull('read_at')
              ->update(['read_at' => now()]);

        // Get updated unread count
        $unreadCount = Message::where('receiver_id', Auth::id())
            ->whereNull('read_at')
            ->count();

        return response()->json([
            'success' => true,
            'unreadCount' => $unreadCount
        ]);
    }

    public function getUnreadCount()
    {
        $count = Message::where('receiver_id', Auth::id())
            ->whereNull('read_at')
            ->count();

        return response()->json(['count' => $count]);
    }
} 