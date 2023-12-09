<?php

namespace App\Providers;

use App\Models\User;
use App\Models\Post;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Boot the authentication services for the application.
     *
     * @return void
     */
    public function boot()
    {
        // Here you may define how you wish users to be authenticated for your Lumen
        // application. The callback which receives the incoming request instance
        // should return either a User instance or null. You're free to obtain
        // the User instance via an API token or any other method necessary.

        /**
         * Register Post Policy
         */
        Gate::define('public-post', function($user)
        {
            return $user->role == 'reader' || $user->role == 'admin' || $user->role == 'editor';
        });

        Gate::define('read-post', function($user)
        {
            return $user->role == 'admin' || $user->role == 'editor' ? true : false;
        });

        Gate::define('store-post', function($user)
        {
            return $user->role == 'admin' || $user->role == 'editor' ? true : false;
        });

        Gate::define('create-post', function($user)
        {
            return $user->role == 'admin' || $user->role == 'editor' ? true : false;
        });

        Gate::define('detail-post', function($user, $post)
        {
            return $user->role == 'admin' ? true : ($user->role == 'editor' ? $post->user_id == $user->id : false);
        });

        Gate::define('modify-post', function($user, $post) {
            return $user->role == 'admin' ? true : ($user->role == 'editor' ? $post->user_id == $user->id : false);
        });

        $this->app['auth']->viaRequest('api', function ($request)
        {
            return $request->input('api_token') ? User::where('api_token', $request->input('api_token'))->first() : null;
        });
    }
}
