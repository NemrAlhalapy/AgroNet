<?php

namespace App\Http\Middleware;

use App\Models\Post;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $postid=$request->route('id');
        $post=Post::findOrFail($postid);
        if (auth()->user()->id==$post->user_id || auth()->user()->status==0)
        return $next($request);
        else
        return response()->json([
        'message'=>'its not your post']);
    }
}
