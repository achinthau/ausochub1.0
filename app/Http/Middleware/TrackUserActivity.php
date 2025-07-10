<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TrackUserActivity
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $lastActivity = session('last_activity_time', now());

            // Get idle time from configuration
            $idleTime = config('app.idle_time'); // in minutes

            if (now()->diffInMinutes($lastActivity) >= $idleTime) {
                // Update the 'onbreak' table

                // DB::table('onbreak')->updateOrInsert(
                //     ['user_id' => $user->id],
                //     ['status' => 1, 'updated_at' => now()]
                // );

                // Log out the user
                Auth::guard('web')->logout();
                session()->flush(); // Clear session data
                return redirect('/login')->with('message', 'You have been logged out due to inactivity.');
            }

            // Update last activity time
            session(['last_activity_time' => now()]);
        }

        return $next($request);
    }
}
