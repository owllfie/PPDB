<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $userRole = (int) Auth::user()->role;
        $allowed = array_map('intval', $roles);

        if (!in_array($userRole, $allowed)) {
            abort(403);
        }

        return $next($request);
    }
}
