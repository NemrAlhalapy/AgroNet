<?php

namespace App\Http\Middleware;

use App\Models\Comment;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckUserComment
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $commentid=$request->route('id');
        $comment=Comment::findOrFail($commentid);
        if (auth()->user()->id==$comment->user_id || auth()->user()->status==0)
        return $next($request);
        else
        return response()->json([
        'message'=>'its not your comment']);
    }
}
