<?php

namespace App\Models\Concerns;

use App\Models\ActivityLog;
use Illuminate\Support\Str;

/**
 * Writes an activity log entry whenever the model is created, updated or deleted.
 */
trait LogsActivity
{
    public static function bootLogsActivity(): void
    {
        foreach (['created', 'updated', 'deleted'] as $event) {
            static::{$event}(function ($model) use ($event) {
                ActivityLog::record($event, $model, sprintf(
                    '%s "%s" %s',
                    Str::headline(class_basename($model)),
                    $model->activityLabel(),
                    $event
                ));
            });
        }
    }

    public function activityLabel(): string
    {
        return (string) ($this->number ?? $this->name ?? $this->getKey());
    }
}
