<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Like;
use App\Models\Post;
use App\Notifications\LikedNotification;
use Illuminate\Http\Request;

class LikeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = $request->user();
        $isLikeExistOfThisUser = Like::where("post_id", $request->post)
        ->where("user_id", $user->id);
        if(!$isLikeExistOfThisUser->exists()){
            $liked = Like::create([
                "post_id" => $request->post,
                "user_id" => $user->id,
            ]);
            $liked->post->user_id !== $user->id && $liked->post->user()->first()->notify(new LikedNotification($liked));
            return 1;
        }else{
            $isLikeExistOfThisUser->delete();
            return 0;
        }
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
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {  
        return Like::where("post_id", $request->post)
                ->where("user_id", $request->user()->id)
                ->delete();
    }
}