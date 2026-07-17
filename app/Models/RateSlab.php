<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RateSlab extends Model
{
    protected $fillable = ['product_id', 'rate_group_id', 'min_qty', 'rate', 'scheme_percent', 'offer_buy_qty', 'offer_free_qty'];

    protected function casts(): array
    {
        return ['rate' => 'decimal:2',
            'scheme_percent' => 'decimal:2'];
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function rateGroup()
    {
        return $this->belongsTo(RateGroup::class);
    }
}