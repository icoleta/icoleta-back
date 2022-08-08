<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Role;
use Illuminate\Http\Request;

class ensureAdmin
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
        $adminRole = Role::where('name', 'admin')->first();

        // Entidades nao podem ser admins
        if($request->user()->role_id == $adminRole->id && !$request->user()->isCompany) {
            return $next($request);
        }

        return response([
            'error' => 'Unauthorized'
        ], 401);
    }
}
