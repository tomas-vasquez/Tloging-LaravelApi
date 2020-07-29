<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\JsonResponse;

class JsonPrettyPrintMiddleware
{
    /**
     * pone bonito los json devueltos por peticiones de tengan el query ?pretty=true
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $response =  $next($request);

        if($response instanceof JsonResponse) {
            // if($request->query("pretty") === "true"){
                $response->setEncodingOptions( JSON_PRETTY_PRINT);
            // }
        }
        return $response;
    }
}
