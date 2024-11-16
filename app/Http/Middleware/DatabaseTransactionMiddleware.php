<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DatabaseTransactionMiddleware
{
    public function handle($request, Closure $next)
    {
        DB::beginTransaction();

        try {
            $response = $next($request);
            if (in_array($response->status(), [400, 403, 404, 409, 422])) {
                DB::rollBack();
                Log::error('Database transaction rolled back. Status '.$response->status().' encountered.'. $response);

                // You can add additional error handling or log the issue as required
            } else {
                DB::commit();
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::channel('error')->error('Database transaction failed', ['exception' => $e]);

            // Handle the exception based on your application's requirements
            // For example, you can return a JSON response or redirect to an error page
            return response()->json(['message' => $e], 500);
        }

        return $response;
    }
}
