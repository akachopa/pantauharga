<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    /**
     * Allow the request only when the signed-in user has one of the given roles.
     *
     * Partners who open an internal page are sent to their own dashboard.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if ($user && $user->hasAnyRole($roles)) {
            return $next($request);
        }

        if ($user && $user->hasRole('mitra') && ! in_array('mitra', $roles, true)) {
            return redirect()->route('mitra.dashboard');
        }

        abort(403, 'Akun Anda tidak memiliki akses ke halaman ini.');
    }
}
