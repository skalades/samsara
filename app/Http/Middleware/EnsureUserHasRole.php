<?php

namespace App\Http\Middleware;

use App\Shared\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!Auth::check()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }

            return redirect()->route('login')
                ->with('warning', 'Silakan login terlebih dahulu untuk mengakses sistem.');
        }

        $user = Auth::user();
        $userRole = $user->role instanceof UserRole ? $user->role->value : (string) $user->role;

        // Superadmin always has universal access across all modules
        if ($userRole === UserRole::SUPERADMIN->value) {
            return $next($request);
        }

        // Check if user's role is in the authorized roles list
        $allowedRoles = [];
        foreach ($roles as $roleGroup) {
            foreach (explode(',', $roleGroup) as $r) {
                $allowedRoles[] = trim($r);
            }
        }

        if (!in_array($userRole, $allowedRoles, true)) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthorized action for this role.'], 403);
            }

            abort(403, 'Akses terbatas: Akun Anda tidak memiliki wewenang untuk modul ini.');
        }

        return $next($request);
    }
}
