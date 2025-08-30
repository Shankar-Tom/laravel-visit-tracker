# Laravel Visit Tracker

Easily track page and model visits with morph visitor model.

## Installation

```bash
composer require shankar/laravel-visit-tracker
```

Publish config and migrations:
```bash
php artisan vendor:publish --tag=visit-tracker-config
php artisan vendor:publish --tag=visit-tracker-migrations
php artisan migrate
```


## Usage

### Tracking visits

Add the `TracksVisits` trait to your model:

```php
use Shankar\VisitTracker\Traits\TracksVisits;

class Post extends Model
{
    use TracksVisits;
}
```

### Tracking model visits
### in controller log the visit

```php

public function show(Post $post)
{
    $post->logVisit();
    return view('post.show', compact('post'));
}
```

### Tracking page visits 
### in middleware

```php
Route::get('/my-url', function () {
    
})->middleware(TrackPageVisit::class);

```

### Get Analytics Data

```php
use Shankar\VisitTracker\Facades\VisitTracker;
use App\Models\Post;

VisitTracker::mostVisitedPages();
VisitTracker::userMostVisitedPages($user);
VisitTracker::globalMostVisitedModels(Post::class);
VisitTracker::userMostVisitedModels($user, Post::class);
VisitTracker::userModelTypeStats($user);
VisitTracker::userAllVisit($user);


```

### Get Analytics Data with Date Range

```php
use Shankar\VisitTracker\Facades\VisitTracker;
use App\Models\Post;

$fromDate = \Carbon\Carbon::now()->subMonth();
$toDate = \Carbon\Carbon::now();

VisitTracker::mostVisitedPages($fromDate, $toDate);
VisitTracker::userMostVisitedPages($user, $fromDate, $toDate);
VisitTracker::globalMostVisitedModels(Post::class, $fromDate, $toDate);
VisitTracker::userMostVisitedModels($user, Post::class, $fromDate, $toDate);
VisitTracker::userModelTypeStats($user, $fromDate, $toDate);
VisitTracker::userAllVisit($user, $fromDate, $toDate);


```




