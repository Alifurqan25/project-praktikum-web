<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Class Order for user orders.
 *
 * @package App\Models
 */
class Order extends Model
{
    // Traits
    use HasFactory;

    /**
     * Order fillable.
     *
     * @var string[]
     */
    protected $fillable = [
        'cart_id',
        'item_id',
        'number'
    ];

    /**
     * Each order belongs to a cart.
     *
     * @return BelongsTo
     */
    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }

    /**
     * Each order has an item.
     *
     * @return HasOne
     */
    public function item(): HasOne
    {
        return $this->hasOne(Item::class);
    }
}