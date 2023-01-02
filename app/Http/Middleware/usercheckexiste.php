<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

class usercheckexiste
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if(Auth::check()){
            $user=Auth::user();
            if($user->user_type=='1'){
                return $next($request);
            }
            else{
                return redirect('/');
            }
            
        }else{
            return redirect('/');
        }
    }
}