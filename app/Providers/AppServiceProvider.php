<?php

namespace App\Providers;

use App\HeaderAlertBuilder;
use App\Models\ActionPlan;
use App\Models\User;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useTailwind();

        RateLimiter::for('sp2kp', function (Request $request) {
            return Limit::perMinute(5)->by((string) (Auth::id() ?: $request->ip()));
        });

        // Share globally to views
        View::composer('*', function ($view) {
            $currentUser = null;
            $allUsers = collect();
            $visibleMenus = collect();
            $openActionCount = 0;

            if (request()->routeIs('home')) {
                $view->with('currentUser', $currentUser);
                $view->with('allUsers', $allUsers);
                $view->with('visibleMenus', $visibleMenus);
                $view->with('openActionCount', $openActionCount);
                $view->with('headerAlerts', collect());

                return;
            }

            if (Schema::hasTable('users')) {
                $allUsers = User::with('roles')->where('is_active', true)->orderBy('id')->get();

                if (! Auth::check()) {
                    $simulatedId = session('simulated_user_id', 1);
                    $currentUser = User::with('roles')->find($simulatedId) ?? $allUsers->first();
                    if ($currentUser) {
                        Auth::login($currentUser);
                    }
                } else {
                    $currentUser = Auth::user();
                }

                if ($currentUser && Schema::hasTable('v_user_visible_menus')) {
                    $visibleMenus = DB::table('v_user_visible_menus')
                        ->where('user_id', $currentUser->id)
                        ->orderBy('sort_order')
                        ->get();

                    if ($visibleMenus->isEmpty()) {
                        $visibleMenus = DB::table('app_menus')->orderBy('sort_order')->get();
                    }
                }
            }

            if (Schema::hasTable('action_plans')) {
                $openActionCount = ActionPlan::whereIn('status', ['antrean', 'dalam_proses', 'terlambat'])->count();
            }

            $headerAlerts = collect();
            $prefs = session('notification_prefs', [
                'stock_low' => true,
                'report_missing' => true,
                'need_verify' => true,
                'policy_trigger' => true,
            ]);

            try {
                $headerAlerts = app(HeaderAlertBuilder::class)->build(5)->filter(function (array $alert) use ($prefs) {
                    $title = strtolower($alert['title']);
                    if (str_contains($title, 'stok') && ! ($prefs['stock_low'] ?? true)) {
                        return false;
                    }
                    if (str_contains($title, 'verifikasi') && ! ($prefs['need_verify'] ?? true)) {
                        return false;
                    }
                    if (($alert['tone'] ?? '') === 'rose' && str_contains($title, 'stok') === false && ! ($prefs['policy_trigger'] ?? true)) {
                        return false;
                    }

                    return true;
                })->values();
            } catch (\Throwable) {
                $headerAlerts = collect();
            }

            $view->with('currentUser', $currentUser);
            $view->with('allUsers', $allUsers);
            $view->with('visibleMenus', $visibleMenus);
            $view->with('openActionCount', $openActionCount);
            $view->with('headerAlerts', $headerAlerts);
        });
    }
}
