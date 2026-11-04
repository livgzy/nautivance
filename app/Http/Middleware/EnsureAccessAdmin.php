<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class EnsureAccessAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
 
        if (! $user) {
            return redirect()->guest(route('login'));
        }
 
        if (! $user->is_admin) {
            Log::warning('admin.access.denied', [
                'user_id' => $user->id,
                'ip'      => $request->ip(),
                'path'    => $request->path(),
            ]);
 
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
 
            return redirect()->route('login')
                ->withErrors(['email' => 'Akun ini tidak memiliki akses admin.']);
        }
 
        return $next($request);
    }
}
