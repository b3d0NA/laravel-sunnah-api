<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Services\CommentService;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Post $post, CommentService $commentService)
    {
        $comments = $post->comments()->with(["user"])
            ->oldest()
            ->cursorPaginate(5);
        foreach($comments as $comment){
            $comment->commented_user = $comment->user->name;
            $comment->commented_username = $comment->user->username;
            $comment->user_profile = $comment->user->first()->imageUrl();
            $comment->commented_at = $comment->created_at->diffForHumans();
            $comment->unsetRelation("user");
        }
        return response()->json([
            "status" => 200,
            "comments" => $comments->items(),
            "cursor" => $comments->nextCursor()?->encode()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Post $post)
    {
        $comment = $post->comments()->create([
            "user_id" => $request->user()->id,
            "text" => $request->comment,
        ]);
        $comment->commented_user = $comment->user->name;
        $comment->commented_username = $comment->user->username;
        $comment->user_profile = $comment->user->first()->imageUrl();
        $comment->commented_at = $comment->created_at->diffForHumans();
        $comment->unsetRelation("user");
        return response()->json([
            "status" => 200,
            "comment" => $comment
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Comment $comment)
    {
        $request->validate([
            "text" => "required|min:1"
        ]);
        if ($request->user()->cannot('update', $comment)) {
            return response()->json([
                "status" => 403,
                "message" => "Forbidden! You do not own this comment."
            ], 403);
        }
        $comment =  $comment->update([
            "text" => $request->text
        ]);
        return response()->json([
            "status" => 200,
            "text" => $request->text
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Comment $comment)
    {
        if ($request->user()->cannot('delete', $comment)) {
            return response()->json([
                "status" => 403,
                "message" => "Forbidden! You do not own this post."
            ], 403);
        }
        $comment->delete();
        return response()->json([
            "status" => 200
        ]);
    }
}