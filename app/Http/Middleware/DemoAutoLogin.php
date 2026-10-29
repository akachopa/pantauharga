<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class DemoAutoLogin
{
    /**
     * Sign in the simulated user when demo auto login is enabled.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (config('auth.demo_auto_login') && ! Auth::check()) {
            $user = User::query()->where('is_active', true)->find(session('simulated_user_id', 1))
                ?? User::query()->where('is_active', true)->orderBy('id')->first();

            if ($user) {
                Auth::login($user);
            }
        }

        return $next($request);
    }
}
