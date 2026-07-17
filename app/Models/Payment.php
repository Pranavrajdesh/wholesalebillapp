<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = ['partner_id', 'payment_date', 'amount', 'method', 'reference', 'note'];

    protected function casts(): array
    {
        return [
            'payment_date' => 'date',
            'amount' => 'decimal:2',
        ];
    }

    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }
}