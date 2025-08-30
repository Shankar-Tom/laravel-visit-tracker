<?php

namespace Shankar\VisitTracker\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Shankar\VisitTracker\Models\Visit;

class TrackPageVisit
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        try {
            $visitor = auth()->user();
            Visit::create([
                'kind'         => 'page',
                'url'          => config('visit-tracker.track_full_url') ? $request->fullUrl() : $request->path(),
                'visitor_id'   => $visitor?->getAuthIdentifier(),
                'visitor_type' => $visitor ? get_class($visitor) : null,
                'ip'           => $request->ip(),
                'user_agent'   => $request->userAgent(),
                'referrer'     => $request->headers->get('referer'),
            ]);
        } catch (\Throwable $e) {
            // swallow tracking errors
            report($e);
        }

        return $response;
    }
}
