<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property string $to_status
 * @property Carbon $created_at
 */
class OrderStatusHistory extends Model
{
    protected $fillable = ['order_id', 'user_id', 'from_status', 'to_status', 'source', 'note'];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
