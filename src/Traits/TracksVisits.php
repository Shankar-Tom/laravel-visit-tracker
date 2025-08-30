<?php

namespace Shankar\VisitTracker\Traits;

use Shankar\VisitTracker\Models\Visit;

trait TracksVisits
{
    /**
     * Log a visit to this model instance.
     */
    public function logVisit(): void
    {
        try {
            $visitor = auth()->user();

            Visit::create([
                'kind'         => 'model',
                'model_type'   => get_class($this),
                'model_id'     => $this->getKey(),
                'visitor_id'   => $visitor?->getAuthIdentifier(),
                'visitor_type' => $visitor ? get_class($visitor) : null,
                'ip'           => request()->ip(),
                'user_agent'   => request()->userAgent(),
                'referrer'     => request()->headers->get('referer'),
            ]);
        } catch (\Throwable $e) {
            report($e);
        }
    }
}
