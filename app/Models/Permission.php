<?php

namespace App\Models;

use Spatie\Permission\Models\Permission as SpatiePermission;

class Permission extends SpatiePermission
{
    public static function grouped(): array
    {
        return self::orderBy('name')
            ->get()
            ->groupBy(function ($permission) {
                return explode('.', $permission->name)[0]; // group by first segment
            })
            ->toArray();
    }

    public function getReadableNameAttribute()
    {
        return ucwords(str_replace(['.', '-'], [' ', ' '], $this->name));
    }
}
