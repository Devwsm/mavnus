<?php

namespace App\Http\Middleware;

use App\Support\OrderCleanup;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AutoCancelExpiredOrders
{
    /**
     * Trigger cek order expired secara "ambient" - numpang di traffic normal,
     * gak butuh cron/scheduler sama sekali. Throttle di dalam OrderCleanup
     * yang jaga biar ini gak jalan tiap request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        OrderCleanup::runIfDue();

        return $next($request);
    }
}