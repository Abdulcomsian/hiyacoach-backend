<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\UserAuthController;
use App\Http\Controllers\Api\SocialiteController;
use App\Http\Controllers\Api\User\UserController;
use App\Http\Controllers\Api\Coach\BankController;
use App\Http\Controllers\Api\Coach\PostController;
use App\Http\Controllers\Api\User\EmailController;
use App\Http\Controllers\Api\Coach\MediaController;
use App\Http\Controllers\Api\User\ReviewController;
use App\Http\Controllers\Api\Coach\FollowController;
use App\Http\Controllers\Api\User\TrainingController;
use App\Http\Controllers\Api\Coach\LocationController;
use App\Http\Controllers\Api\Coach\OfferingController;
use App\Http\Controllers\Api\ForgotPasswordController;
use App\Http\Controllers\Api\PostInteractionController;
use App\Http\Controllers\Api\Coach\CertificateController;
use App\Http\Controllers\Api\Coach\AvailabilityController;
use App\Http\Controllers\Api\Coach\DietPlanController;
use App\Http\Controllers\Api\Coach\TrainingPlanController;
use App\Http\Controllers\Api\User\CheckInPhotosController;
use App\Http\Controllers\Api\User\WeeklyReportController;
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

    // Diet Plan
    Route::post('/diet-plan/{userId}', [DietPlanController::class, 'store']);
    Route::post('/update-diet-plan/{id}', [DietPlanController::class, 'update']);
    Route::delete('/diet-plan/{id}', [DietPlanController::class, 'destroy']);

    // Training Plan
    Route::post('/training-plan/{userId}', [TrainingPlanController::class, 'store']);
    Route::post('/update-training-plan/{id}', [TrainingPlanController::class, 'update']);
    Route::delete('/training-plan/{id}', [TrainingPlanController::class, 'destroy']);

    ///// User /////
    Route::get('coaches', [UserController::class, 'allCoaches']);

    // Reviews
    Route::post('coaches/{coach}/reviews', [ReviewController::class, 'store']);
    Route::post('reviews/{review}', [ReviewController::class, 'update']);
    Route::delete('reviews/{review}', [ReviewController::class, 'destroy']);

    // Filter Coaches
    Route::get('gender-count', [UserController::class,'genderCount']);
    Route::get('/coaches/filter', [UserController::class, 'filterCoaches']);
    Route::get('coach/{coachId}/{type}', [UserController::class, 'coachGallery']);

    // Saved Posts
    Route::get('saved-posts', [UserController::class, 'savedPosts']);

    // Settings
    Route::get('user/settings', [SettingsController::class, 'getSettings']);
    Route::post('user/settings', [SettingsController::class, 'createOrUpdate']);

    // Delete Account
    Route::delete('user/delete-account', [UserController::class, 'deleteAccount']);

    // Send Email
    Route::post('send-email', [EmailController::class, 'sendEmail']);

    // Book Training
    Route::get('book-training/{coachId}', [TrainingController::class, 'getTraining']);
    Route::post('book-training/{coachId}', [TrainingController::class, 'bookTraining']);
    Route::get('upcoming-sessions', [TrainingController::class, 'getUpcomingSessions']);
    Route::post('cancel-upcoming-session/{id}', [TrainingController::class, 'cancelUpcomingSessions']);
    Route::get('past-sessions', [TrainingController::class, 'getPastSessions']);

    // Online Coaching Hub
    Route::get('online-coaching-hub',[UserController::class, 'onlineCoachingHub']);

    // FAQs
    Route::get('faqs',[UserController::class, 'allFaqs']);

    // Booked Sessions
    Route::get('booked-sessions',[UserController::class, 'BookedSessions']);

    // Check in photos
    Route::post('/check-in-photos/{coachId}', [CheckInPhotosController::class, 'store']);
    Route::post('/update-check-in-photos/{id}', [CheckInPhotosController::class, 'update']);
    Route::delete('/check-in-photos/{id}', [CheckInPhotosController::class, 'destroy']);

    // Weekly Report
    Route::post('/weekly-report/{coachId}', [WeeklyReportController::class, 'store']);
    Route::post('/update-weekly-report/{id}', [WeeklyReportController::class, 'update']);
    Route::delete('/weekly-report/{id}', [WeeklyReportController::class, 'destroy']);
});
