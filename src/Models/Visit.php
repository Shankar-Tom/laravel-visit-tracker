<?php

namespace Shankar\VisitTracker\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Visit extends Model
{
    protected $fillable = [
        'kind',
        'url',
        'model_type',
        'model_id',
        'visitor_id',
        'visitor_type',
        'ip',
        'user_agent',
        'referrer',
    ];

    public function visitor(): MorphTo
    {
        return $this->morphTo();
    }

    public function visitable(): MorphTo
    {
        return $this->morphTo(null, 'model_type', 'model_id');
    }
}
