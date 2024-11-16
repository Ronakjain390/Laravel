<?php

namespace App\Http\Middleware;
use Illuminate\Support\Facades\Log;
use Closure;

class CompressResponseMiddleware
{ 

     // URLs to exclude from compression
     protected $excludeUrls = [
        '/js/index.esm.js.map',
        // Add more URLs as needed
    ];

    public function handle($request, Closure $next)
    {
        $response = $next($request);

        // Check if the response is successful, not already compressed, and not in the exclude list
        if ($response->isSuccessful() && !$response->headers->has('Content-Encoding') && !$this->shouldExclude($request)) {
            // Compress the response using gzip
            $response->header('Content-Encoding', 'gzip');
            $response->header('Vary', 'Accept-Encoding');
            $response->setContent(gzencode($response->getContent(), 6));

            // Log that the response was compressed
            Log::info('Response compressed using gzip for URL: ' . $request->fullUrl());
        } else {
            // Log that the response was not compressed
            Log::info('Response was not compressed for URL: ' . $request->fullUrl() . ', Content Length: ' . strlen($response->getContent()));
        }

        return $response;
    }

    protected function shouldExclude($request)
    {
        $url = $request->path();

        foreach ($this->excludeUrls as $excludeUrl) {
            if (strpos($url, $excludeUrl) !== false) {
                return true;
            }
        }

        return false;
    }
}