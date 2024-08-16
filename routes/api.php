<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\UserAuthController;
use App\Http\Controllers\Api\SocialiteController;
use App\Http\Controllers\Api\User\UserController;
use App\Http\Controllers\Api\Coach\BankController;
use App\Http\Controllers\Api\Coach\PostController;
use App\Http\Controllers\Api\Coach\MediaController;
use App\Http\Controllers\Api\User\ReviewController;
use App\Http\Controllers\Api\Coach\FollowController;
use App\Http\Controllers\Api\Coach\LocationController;
use App\Http\Controllers\Api\Coach\OfferingController;
use App\Http\Controllers\Api\ForgotPasswordController;
use App\Http\Controllers\Api\PostInteractionController;
use App\Http\Controllers\Api\Coach\CertificateController;
use App\Http\Controllers\Api\Coach\AvailabilityController;
use App\Http\Controllers\Auth\EmailVerificationController;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('register/{usertype}', [UserAuthController::class, 'register']);
Route::post('login', [UserAuthController::class, 'login']);
Route::post('password/forgot', [ForgotPasswordController::class, 'sendResetCode']);
Route::post('password/verify-reset-code', [ForgotPasswordController::class, 'verifyResetCode']);
Route::post('password/reset', [ForgotPasswordController::class, 'resetPassword']);

Route::get('auth/google', [SocialiteController::class, 'redirectToGoogle']);
Route::get('google/callback', [SocialiteController::class, 'handleGoogleCallback']);

Route::get('/email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])
    ->name('verification.verify');

Route::post('/email/resend', [EmailVerificationController::class, 'resend'])
    ->middleware(['auth:sanctum'])
    ->name('verification.send');

Route::middleware('auth:sanctum')->group(function () {

    Route::post('logout', [UserAuthController::class, 'logout']);

    // Profile
    Route::get('profile', [ProfileController::class, 'profile']);
    Route::post('update-profile', [ProfileController::class, 'updateProfile']);

    ///////// Coach /////////

    // Location
    Route::post('store-location', [LocationController::class, 'storeOrUpdateLocation']);

    // Offerings
    Route::post('store-offering/{offeringId?}', [OfferingController::class, 'storeOrUpdateOffering']);

    // Bank Details
    Route::post('store-bank/{bankId?}', [BankController::class, 'storeOrUpdateBank']);

    // Post
    Route::post('/posts', [PostController::class, 'store']);
    Route::get('/posts', [PostController::class, 'index']);
    Route::get('/posts/{post}', [PostController::class, 'show']);
    Route::post('/update-post/{post}', [PostController::class, 'update']);
    Route::delete('/posts/{post}', [PostController::class, 'destroy']);

    // Post Reactions
    Route::post('/posts/{postId}/like', [PostInteractionController::class, 'likePost']);
    Route::post('/posts/{postId}/unlike', [PostInteractionController::class, 'unlikePost']);
    Route::post('/posts/{postId}/comment', [PostInteractionController::class, 'commentOnPost']);
    Route::post('/posts/{postId}/favorite', [PostInteractionController::class, 'favoritePost']);
    Route::post('/posts/{postId}/unfavorite', [PostInteractionController::class, 'unfavoritePost']);

    // Certificates
    Route::post('certificates/{certificateId?}', [CertificateController::class, 'storeOrUpdate']);
    Route::get('certificates/{certificateId?}', [CertificateController::class, 'view']);

    // Availability
    Route::prefix('coaches')->group(function () {
        Route::get('/availabilities', [AvailabilityController::class, 'index']);
        Route::post('/availabilities', [AvailabilityController::class, 'store']);
    });

    Route::prefix('availabilities')->group(function () {
        Route::get('{id}', [AvailabilityController::class, 'show']);
        Route::put('{availability}', [AvailabilityController::class, 'update']);
        Route::delete('{availability}', [AvailabilityController::class, 'destroy']);
    });

    // Follow Unfollow
    Route::post('/follow/{userId}', [FollowController::class, 'follow']);
    Route::post('/unfollow/{userId}', [FollowController::class, 'unfollow']);
    Route::get('/is-following/{userId}', [FollowController::class, 'isFollowing']);

    // Media
    Route::post('/media', [MediaController::class, 'store']);
    Route::get('/media', [MediaController::class, 'index']);
    Route::get('/media/{media}', [MediaController::class, 'show']);
    Route::post('/update-media/{media}', [MediaController::class, 'update']);
    Route::delete('/media/{media}', [MediaController::class, 'destroy']);


    ///// User /////
    Route::get('coaches', [UserController::class, 'allCoaches']);

    Route::post('coaches/{coach}/reviews', [ReviewController::class, 'store']);
    Route::post('reviews/{review}', [ReviewController::class, 'update']);
    Route::delete('reviews/{review}', [ReviewController::class, 'destroy']);

    Route::get('/coaches/filter', [UserController::class, 'filterCoaches']);
    Route::get('coach/{coachId}/{type}', [UserController::class, 'coachGallery']);
});
