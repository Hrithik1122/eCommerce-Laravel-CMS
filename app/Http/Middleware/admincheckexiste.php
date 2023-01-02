<?php

namespace App\Http\Middleware;

use Closure;
use Sentinel;
use Session;
class admincheckexiste
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
        if(Sentinel::check()){
            $user=Sentinel::getUser();
            if($user->user_type=='2'){
                return $next($request);
            }
            else{
                if(Session::get("is_web")=='1'){
                    return redirect('/');
                }else{
                    return redirect('login');
                }
                
            }
            
        }else{
            if(Session::get("is_web")=='1'){
                    return redirect('/');
            }else{
                    return redirect('login');
            }
        }
    }
}
