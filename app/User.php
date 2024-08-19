<?php

namespace App;

use App\Models\Availability;
use App\Models\Like;
use App\Models\Post;
use App\Models\Review;
use App\Models\Comment;
use App\Models\Favorite;
use Illuminate\Support\Str;
use App\Models\CoachLocation;
use App\Models\CoachOffering;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements MustVerifyEmail
{
    use Notifiable, HasFactory, Notifiable, HasRoles, HasApiTokens;

    protected $fillable = [
        'username',
        'name',
        'email',
        'password',
        'phone_no',
        'dob',
        'referral_code ',
        'email_verified_at',
        'remember_token',
        'password_reset_code'
    ];

    public static function boot()
    {
        parent::boot();

        static::created(function ($user) {
            $user->referral_code = strtoupper(Str::random(10));
            $user->save();
        });
    }

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function coachLocation()
    {
        return $this->hasOne(CoachLocation::class);
    }

    public function referrals()
    {
        return $this->hasMany(User::class, 'referred_by');
    }

    public function coach()
    {
        return $this->belongsTo(User::class, 'coach_id');
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    // A post can have many comments
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    // A post can have many favorites
    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    public function followers()
    {
        return $this->belongsToMany(User::class, 'follows', 'followed_user_id', 'user_id');
    }

    // Define the relationship to following users
    public function following()
    {
        return $this->belongsToMany(User::class, 'follows', 'user_id', 'followed_user_id');
    }

    // Method to check if the user is following another user
    public function isFollowing($userId)
    {
        return $this->following()->where('followed_user_id', $userId)->exists();
    }

    // Method to follow another user
    public function follow($userId)
    {
        if (!$this->isFollowing($userId)) {
            $this->following()->attach($userId);
        }
    }

    // Method to unfollow a user
    public function unfollow($userId)
    {
        $this->following()->detach($userId);
    }

    public function offerings()
    {
        return $this->hasMany(CoachOffering::class, 'user_id', 'id');
    }

    public function availabilities()
    {
        return $this->hasMany(Availability::class, 'user_id', 'id');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'user_id');
    }

    // Reviews the user has written
    public function writtenReviews()
    {
        return $this->hasMany(Review::class, 'reviewer_id');
    }
}
