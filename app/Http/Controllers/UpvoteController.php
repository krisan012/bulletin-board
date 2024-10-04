<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Vote;
use Illuminate\Http\Request;

class UpvoteController extends Controller
{
    public function upvote(Article $article)
    {
        // Ensure user is authenticated
        $user = auth()->user();

        // Check if the user has already upvoted this article
        if ($article->isUpvotedByUser($user->id)) {
            return response()->json(['message' => 'Already upvoted'], 409);
        }

        // Add the upvote
        $upvote = Vote::create([
            'article_id' => $article->id,
            'user_id' => $user->id,
        ]);

        return response()->json($upvote, 201);
    }

    public function removeUpvote(Article $article)
    {
        $user = auth()->user();

        // Check if the user has already upvoted
        if (!$article->isUpvotedByUser($user->id)) {
            return response()->json(['message' => 'Not upvoted'], 404);
        }

        // Remove the upvote
        Vote::where('article_id', $article->id)
            ->where('user_id', $user->id)
            ->delete();

        return response()->json(['message' => 'Upvote removed'], 200);
    }
}
