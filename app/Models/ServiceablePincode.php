<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceablePincode extends Model
{
    protected $fillable = ['pincode', 'city', 'state', 'shipping_amount', 'estimated_days_min', 'estimated_days_max', 'cod_available', 'is_active'];

    protected function casts(): array
    {
        return ['shipping_amount' => 'decimal:2', 'cod_available' => 'boolean', 'is_active' => 'boolean'];
    }
}
