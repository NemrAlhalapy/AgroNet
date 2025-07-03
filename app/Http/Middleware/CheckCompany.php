<?php

namespace App\Http\Middleware;

use App\Models\Company;
use App\Models\Post;
use App\Models\Product;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckCompany
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
   public function handle(Request $request, Closure $next): Response
{
    $company = Auth::user();

    if (!($company instanceof \App\Models\Company)) {
            return response()->json(['message' => 'ليس لديك صلاحية الوصول هنا.'], 403);
        }

    $productId = $request->route('id');
    $product = Product::findOrFail($productId);

    if ($company->id == $product->company_id) {
        return $next($request);
    }

    return response()->json([
        'message' => 'هذا المنتج لا يخصك ولا يمكنك حذفه او تعديله.'
    ]);
}

}
