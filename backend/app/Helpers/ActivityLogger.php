<?php

namespace App\Helpers;

use Spatie\Activitylog\ActivitylogServiceProvider;
use Spatie\Activitylog\Facades\Activity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable;

class ActivityLogger
{
    /**
     * Log an activity.
     *
     * @param string $logName
     * @param string $event
     * @param string $description
     * @param Model|null $model
     * @param array $properties
     * @param Model|Authenticatable|null $causedBy
     * @return \Spatie\Activitylog\Contracts\Activity|null
     */
    public static function log(
        string $logName,
        string $event,
        string $description,
        ?Model $model = null,
        array $properties = [],
        $causedBy = null
    ) {
        // اختياري: تحقق من وجود logName و event
        if (empty($logName) || empty($event)) {
            throw new \InvalidArgumentException('logName and event are required');
        }

        $log = Activity::useLog($logName)
            ->causedBy($causedBy)
            ->event($event)
            ->withProperties($properties);

        if ($model) {
            $log->performedOn($model);
        }

        return $log->log($description);
    }
}