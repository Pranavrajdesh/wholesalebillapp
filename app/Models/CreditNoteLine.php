<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CreditNoteLine extends Model
{
    protected $fillable = ['credit_note_id', 'product_id', 'name', 'brand', 'category', 'mrp', 'qty', 'rate', 'amount'];

    protected function casts(): array
    {
        return [
            'mrp' => 'decimal:2',
            'rate' => 'decimal:2',
            'amount' => 'decimal:2',
        ];
    }

    public function creditNote()
    {
        return $this->belongsTo(CreditNote::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}