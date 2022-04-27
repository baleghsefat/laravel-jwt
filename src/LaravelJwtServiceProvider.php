<?php

namespace Baleghsefat\Jwt;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;

class LaravelJwtServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Auth::viaRequest('jwt', function (Request $request) {
            $token = $request->header('authorization');
            if (isset($token)) {
                if (isTokenValid($token)) {
                    $tokenData = optional(tokenData($token));
                    $user = new User();
                    foreach ($tokenData as $key => $value) {
                        $user->{$key} = $value;
                    }

                    return $user;
                }
            }
        });
    }
}