<?php

namespace App\Providers;

use App\Models\Post;
use App\Models\DietPlan;
use App\Policies\PostPolicy;
use Illuminate\Support\Carbon;
use App\Policies\DietPlanPolicy;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Gate;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        Post::class => PostPolicy::class,
        DietPlan::class => DietPlanPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        VerifyEmail::createUrlUsing(function ($notifiable) {
            // Generate the signed route without domain, so it only includes the path
            $signedRoute = URL::temporarySignedRoute(
                'verification.verify',
                Carbon::now()->addMinutes(60),
                [
                    'id' => $notifiable->getKey(),
                    'hash' => sha1($notifiable->getEmailForVerification()),
                ]
            );

            // Prepend '/api' to the generated path
            $apiPath = str_replace(url('/'), url('/api'), $signedRoute);

            return $apiPath;
        });
    }
}
