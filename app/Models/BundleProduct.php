<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

/** @property int $quantity */
class BundleProduct extends Pivot
{
    protected function casts(): array
    {
        return ['quantity' => 'integer'];
    }
}
