<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CompanyAuth
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        // تأكد إن اللي مسجل دخول هو instance من Company
        if ($user && $user instanceof \App\Models\Company) {
            return $next($request);
        }

        return response()->json(['message' => 'غير مصرح لك الوصول إلى هذا المسار.'], 403);
    }
}
