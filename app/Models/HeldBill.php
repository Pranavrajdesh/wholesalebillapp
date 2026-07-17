<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HeldBill extends Model
{
    protected $fillable = ['partner_id', 'payload'];

    protected function casts(): array
    {
        return ['payload' => 'array'];
    }

    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }
}