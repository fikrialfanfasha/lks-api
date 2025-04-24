<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{

    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'caption' => 'required|string',
            'attachments' => 'required|array',
            'attachments.*' => 'image|mimes:jpg,jpeg,png,webp,gif|max:2048',
        ]);
    
        $paths = [];
        foreach ($request->file('attachments') as $file) {
            $paths[] = $file->store('posts', 'public'); // simpan ke storage/app/public/posts
        }
    
        // Simpan ke database kalau ada model Post, contohnya:
        // $post = Post::create([
        //     'caption' => $validated['caption'],
        //     'user_id' => auth()->id(),
        //     'attachments' => json_encode($paths)
        // ]);
    
        return response()->json([
            'message' => 'Create post success',
            // 'data' => $post
        ], 201);
    }
    
}
