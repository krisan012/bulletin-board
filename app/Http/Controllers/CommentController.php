<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(Request $request, Article $article)
    {
        $request->validate([
            'content' => 'required|string',
        ]);

        $comment = new Comment();
        $comment->content = $request->input('content');
        $comment->user_id = auth()->id(); // Assuming you are using Laravel authentication
        $comment->article_id = $article->id;
        $comment->save();

        return response()->json($comment->load('user'), 201);
    }
}
