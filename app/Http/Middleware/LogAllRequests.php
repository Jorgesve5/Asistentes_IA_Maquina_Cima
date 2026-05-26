<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LogAllRequests
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);
        
        if ($response->status() >= 500) {
            $content = $response->getContent();
            Log::channel('single')->error("500 ERROR CAUGHT BY MIDDLEWARE", [
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'payload' => $request->all(),
                'response' => substr($content, 0, 1000)
            ]);
            file_put_contents(storage_path('logs/500_response.html'), $content);
        }
        
        return $response;
    }
}
