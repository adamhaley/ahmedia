<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PostController extends Controller
{
    public function __invoke(Request $request, string $slug): Response
    {
        $post = Post::query()
            ->where('slug', $slug)
            ->when(! $request->user(), fn ($query) => $query->published())
            ->firstOrFail();

        return response()->view('posts.show', ['post' => $post]);
    }
}
