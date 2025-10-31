<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

trait BelongsToTenant
{
    protected static function bootBelongsToTenant()
    {
        static::addGlobalScope('shop_id', function (Builder $builder) {
            if (auth()->check() && auth()->user()->shop_id) {
                $builder->where('shop_id', auth()->user()->shop_id);
            }
        });

        static::creating(function (Model $model) {
            if ($model->shop_id) {
                return;
            }
            if (auth()->check() && auth()->user()->shop_id) {
                $model->shop_id = auth()->user()->shop_id;

                return;
            }
            throw new \Exception('Cannot create record without a defined tenant (shop_id).');
        });
    }

    public function shop()
    {
        return $this->belongsTo(\App\Models\Shop::class);
    }
}
