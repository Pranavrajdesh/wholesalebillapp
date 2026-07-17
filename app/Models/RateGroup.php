<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RateGroup extends Model
{
    protected $fillable = ['name'];

    public function partners()
    {
        return $this->hasMany(Partner::class);
    }

    public function rateSlabs()
    {
        return $this->hasMany(RateSlab::class);
    }
}