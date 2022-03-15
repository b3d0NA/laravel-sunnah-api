<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PostRequest;
use App\Models\Image;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum')->except('show');
    }

    public function index(Request $request)
    {
        $posts = Post::withCount(["likes", "comments"])
            ->latest()
            ->cursorPaginate(8);
        foreach ($posts as $post) {
            $post->posted_user = $post->user->pluck("name")->implode(",");
            $post->user_profile = $post->user->first()->imageUrl();
            $post->posted_at = $post->created_at->diffForHumans();
            $post->liked = $request->user()->isUserLikedPost($post);
            $post->unsetRelation("user");
        }
        return response()->json([
            "status" => 200,
            "posts" => $posts->items(),
            "cursor" => $posts->nextCursor()?->encode(),
        ]);
    }

    public function store(PostRequest $request, Post $post)
    {
        $post->user_id = $request->user()->id;
        if ($request->filled('text')) {
            $post->text = $request->text;
        }
        $post->save();
        if ($request->has("images")) {
            foreach ($request->images as $image) {
                $extension = $image->getClientOriginalExtension();
                $filename = $image->storeAs("public/posts", $post->id . '_' . time() . '.' . $extension);
                $imageUrl = url(Storage::url($filename));

                Image::create([
                    "imageable_id" => $post->id,
                    "imageable_type" => Post::class,
                    "url" => $imageUrl,
                ]);
            }
            $post->images = $post->images()->get()->pluck("url");
        }
        $post->likes_count = $post->likes()->count();
        $post->comments_count = $post->comments()->count();
        $post->posted_user = $post->user->pluck("name")->implode(",");
        $post->user_profile = $post->user->first()->imageUrl();
        $post->posted_at = $post->created_at->diffForHumans();
        return response()->json([
            "status" => 200,
            "post" => $post,
        ]);
    }

    public function show(Post $post, Request $request)
    {
        $post->likes_count = $post->likes->count();
        $post->comments_count = $post->comments->count();
        $post->posted_user = $post->user->pluck("name")->implode(",");
        $post->user_profile = $post->user->first()->imageUrl();
        $post->posted_at = $post->created_at->diffForHumans();
        $post->liked = $request->user()?->isUserLikedPost($post);
        return response()->json([
            "post" => $post,
        ]);
    }

    public function update(Request $request, Post $post)
    {
        if ($request->user()->cannot('update', $post)) {
            return response()->json([
                "status" => 403,
                "message" => "Forbidden! You do not own this post.",
            ], 403);
        }
        $post = $post->update([
            "text" => $request->text,
        ]);
        return response()->json([
            "status" => 200,
            "text" => $request->text,
        ]);
    }

    public function destroy(Request $request, Post $post)
    {
        if ($request->user()->cannot('delete', $post)) {
            return response()->json([
                "status" => 403,
                "message" => "Forbidden! You do not own this post.",
            ], 403);
        }
        $post->delete();
        return response()->json([
            "status" => 200,
        ]);
    }
}