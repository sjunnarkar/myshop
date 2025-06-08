<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckMaintenanceMode
{
    public function handle(Request $request, Closure $next)
    {
        // Get maintenance mode from environment
        $maintenanceMode = env('MAINTENANCE_MODE', false);

        if ($maintenanceMode) {
            // Show maintenance page for everyone
            return response()->view('maintenance');
        }

        return $next($request);
    }
} 