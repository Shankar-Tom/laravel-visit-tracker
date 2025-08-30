<?php

namespace Shankar\VisitTracker\Services;

use Illuminate\Support\Facades\DB;
use Shankar\VisitTracker\Models\Visit;

class VisitAnalytics
{
    /**
     * Most visited pages globally.
     */
    public function mostVisitedPages(int $limit = 10, ?\DateTime $fromDate = null, ?\DateTime $toDate = null)
    {
        $query = Visit::query()
            ->where('kind', 'page');

        if ($fromDate !== null) {
            $query->where('created_at', '>=', $fromDate);
        }

        if ($toDate !== null) {
            $query->where('created_at', '<=', $toDate);
        }

        return $query
            ->select('url', DB::raw('COUNT(*) as total'))
            ->groupBy('url')
            ->orderByDesc('total')
            ->limit($limit)
            ->get();
    }



    /**
     * Most visited pages by a user (morphable).
     */
    public function userMostVisitedPages($user, int $limit = 10, ?\DateTime $fromDate = null, ?\DateTime $toDate = null)
    {
        $query = Visit::query()
            ->where('kind', 'page')
            ->where('visitor_id', $user->getAuthIdentifier())
            ->where('visitor_type', get_class($user))
            ->select('url', DB::raw('COUNT(*) as total'))
            ->groupBy('url')
            ->orderByDesc('total')
            ->limit($limit);

        if ($fromDate !== null) {
            $query->where('created_at', '>=', $fromDate);
        }

        if ($toDate !== null) {
            $query->where('created_at', '<=', $toDate);
        }

        return $query->get();
    }

    /**
     * Most visited models globally for a given model class.
     */
    public function globalMostVisitedModels(string $modelClass, int $limit = 10, ?\DateTime $fromDate = null, ?\DateTime $toDate = null)
    {
        $query = Visit::query()
            ->where('kind', 'model')
            ->where('model_type', $modelClass)
            ->select('model_id', DB::raw('COUNT(*) as total'))
            ->groupBy('model_id')
            ->orderByDesc('total')
            ->limit($limit);

        if ($fromDate !== null) {
            $query->where('created_at', '>=', $fromDate);
        }

        if ($toDate !== null) {
            $query->where('created_at', '<=', $toDate);
        }

        return $query->get();
    }

    /**
     * Most visited models by a specific user for a given model class.
     */
    public function userMostVisitedModels($user, string $modelClass, int $limit = 10, ?\DateTime $fromDate = null, ?\DateTime $toDate = null)
    {
        $query = Visit::query()
            ->where('kind', 'model')
            ->where('model_type', $modelClass)
            ->where('visitor_id', $user->getAuthIdentifier())
            ->where('visitor_type', get_class($user))
            ->select('model_id', DB::raw('COUNT(*) as total'))
            ->groupBy('model_id')
            ->orderByDesc('total')
            ->limit($limit);

        if ($fromDate !== null) {
            $query->where('created_at', '>=', $fromDate);
        }

        if ($toDate !== null) {
            $query->where('created_at', '<=', $toDate);
        }

        return $query->get();
    }

    /**
     * Grouped by model type (class) for a user.
     */
    public function userModelTypeStats($user, int $limitPerType = 10, ?\DateTime $fromDate = null, ?\DateTime $toDate = null)
    {
        $query = Visit::query()
            ->where('kind', 'model')
            ->where('visitor_id', $user->getAuthIdentifier())
            ->where('visitor_type', get_class($user))
            ->select('model_type', DB::raw('COUNT(*) as total'))
            ->groupBy('model_type')
            ->orderByDesc('total')
            ->limit($limitPerType);

        if ($fromDate !== null) {
            $query->where('created_at', '>=', $fromDate);
        }

        if ($toDate !== null) {
            $query->where('created_at', '<=', $toDate);
        }

        return $query->get();
    }

    /**
     * All visits by a specific user.
     */
    public function userAllVisit($user, ?\DateTime $fromDate = null, ?\DateTime $toDate = null)
    {
        $query = Visit::query()
            ->where('kind', 'model')
            ->where('visitor_id', $user->getAuthIdentifier())
            ->where('visitor_type', get_class($user))
            ->select('model_type', 'model_id', DB::raw('COUNT(*) as total'))
            ->groupBy(['model_type', 'model_id'])
            ->orderByDesc('total');

        if ($fromDate !== null) {
            $query->where('created_at', '>=', $fromDate);
        }

        if ($toDate !== null) {
            $query->where('created_at', '<=', $toDate);
        }

        return $query->get()
            ->groupBy('model_type')
            ->map(function ($modelRows, $modelClass) {
                return [
                    'model_class' => class_basename($modelClass),
                    'models' => $this->attachModels($modelClass, $modelRows),
                ];
            })->values();
    }


    protected function attachModels(string $modelClass, $rows)
    {
        $ids = $rows->pluck('model_id')->all();
        if (empty($ids)) return collect();

        $models = $modelClass::whereIn((new $modelClass)->getKeyName(), $ids)->get()->keyBy((new $modelClass)->getKeyName());

        return $rows->map(function ($row) use ($models) {
            $model = $models->get($row->model_id);
            return [
                'model' => $model,
                'visits' => (int) $row->total,
            ];
        })->filter(fn($r) => ! is_null($r['model']))->values();
    }
}
