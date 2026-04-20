<?php
namespace App\Observers;

use Spatie\Activitylog\Models\Activity;

class ActivityObserver
{
    public function creating(Activity $activity)
    {
        $props = $activity->properties ? $activity->properties->toArray() : [];

        $props['ip'] = request()->ip();
        $props['user_agent'] = request()->userAgent();

        $activity->properties = $props;
    }
}