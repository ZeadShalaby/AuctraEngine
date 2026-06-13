<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class SetLocale
{
    public function handle(Request $request, Closure $next)
    {
        // $locale = Session::get('locale', config('app.locale')); 

        // App::setLocale($locale);

        $locale = $request->header('Accept-Language', 'en'); // default en
        if (in_array($locale, ['en', 'ar'])) {
            App::setLocale($locale);
        }
        return $next($request);
    }
}