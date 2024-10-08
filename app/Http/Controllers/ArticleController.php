<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function index()
    {
        $articles = Article::with('user')->latest()->get();
        return response()->json($articles);
    }
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        // Create the article
        $article = Article::create([
            'title' => $validatedData['title'],
            'content' => $validatedData['content'],
            'user_id' => auth()->user()->id
        ]);

        return response()->json([
            'message' => 'Article created successfully!',
            'article' => $article
        ], 201);
    }

    public function show(Article $article)
    {
        $isOwnedByCurrentUser = auth()->check() && auth()->user()->id === $article->user_id;
        $isUpvotedByUser = auth()->check() && $article->isUpvotedByUser(auth()->id());
        return response()->json([
            'article' => $article->load(['comments.user', 'upvotes', 'user']),
            'isUpvotedByUser' => $isUpvotedByUser,
            'isOwnedByCurrentUser' => $isOwnedByCurrentUser,
        ]);
    }

    public function destroy(Article $article)
    {
        // Ensure that only the owner can delete the article
        if (auth()->user()->id !== $article->user_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Delete the article
        $article->delete();

        return response()->json(['message' => 'Article deleted successfully']);
    }

    public function edit(Article $article)
    {
        return response()->json($article);
    }

    public function update(Request $request, Article $article)
    {
        // Validate the updated data
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        // Update the article
        $article->update($validated);

        return response()->json([
            'message' => 'Article updated successfully!',
            'article' => $article
        ], 200);
    }
}
