<?php

namespace Shankar\VisitTracker\Facades;

use Illuminate\Support\Facades\Facade;

class VisitTracker extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'visit-tracker';
    }
}
