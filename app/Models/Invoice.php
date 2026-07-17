<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = [
        'invoice_no', 'partner_id', 'invoice_date', 'subtotal',
        'discount_type', 'discount_value', 'discount_amount', 'discount_note',
        'round_off', 'total',
    ];

    protected function casts(): array
    {
        return [
            'invoice_date' => 'date',
            'subtotal' => 'decimal:2',
            'discount_value' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'round_off' => 'decimal:2',
            'total' => 'decimal:2',
        ];
    }

    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }

    public function lines()
    {
        return $this->hasMany(InvoiceLine::class);
    }
}