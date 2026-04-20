<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;

class ActivityLogger
{
    public static function log($event, $model = null, $message = '', $properties = [])
    {
        activity('system')
            ->causedBy(Auth::user())
            ->event($event)
            ->performedOn($model)
            ->withProperties(array_merge([
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ], $properties))
            ->log($message);
    }
}