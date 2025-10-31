<?php

namespace App\Models;

use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use BelongsToTenant, HasFactory;

    protected $fillable = [
        'name',
        'sku',
        'price',
        'stock',
        'shop_id',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'stock' => 'integer',
    ];

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    public function orders()
    {
        return $this->belongsToMany(Order::class, 'order_items')
            ->using(OrderItem::class)
            ->withPivot('quantity', 'price_at_order')
            ->withTimestamps();
    }
}
