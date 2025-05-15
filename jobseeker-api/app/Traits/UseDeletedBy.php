<?php

namespace App\Traits;

trait UseDeletedBy
{
    protected static function bootUseDeletedBy()
    {
        // We set the deleted_by attribute before deleted event so we doesn't get an error if Instance was deleted by force (without soft delete).
        static::deleting(function ($model) {
            $model->deleted_by = auth()->user()->id;
            $model->save();
        });
    }
}
