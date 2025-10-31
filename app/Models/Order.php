<?php

namespace App\Models;

use App\Enums\OrderStatus;
use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'shop_id',
        'user_id',
        'status',
        'total_amount',
    ];

    protected $casts = [
        'status' => OrderStatus::class,
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // An Order has many OrderItems (the products in the order)
    public function items()
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'order_items')
            ->using(OrderItem::class)
            ->withPivot('quantity', 'price_at_order')
            ->withTimestamps();
    }
}
