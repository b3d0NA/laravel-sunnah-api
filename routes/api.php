<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\LikeController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\UserController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource("posts", PostController::class);
    Route::apiResource("likes", LikeController::class)->only(["store", "destroy"]);
    Route::apiResource("posts.comments", CommentController::class)->shallow();
    Route::get("notifications", [NotificationController::class, "index"]);
    Route::get("users/show", [UserController::class, "show"]);

    Route::post("logout", [AuthController::class, "logout"]);
});

Route::middleware("guest")->group(function (){
    Route::post("register", [AuthController::class, "register"]);
    Route::post("login", [AuthController::class, "login"]);
});