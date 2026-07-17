<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceLine extends Model
{
    protected $fillable = [
        'invoice_id', 'product_id', 'name', 'brand', 'category', 'hsn_code',
        'mrp', 'qty', 'free_qty', 'rate', 'scheme_percent',
        'tax_percent', 'tax_inclusive', 'amount',
    ];

    protected function casts(): array
    {
        return [
            'mrp' => 'decimal:2',
            'rate' => 'decimal:2',
            'scheme_percent' => 'decimal:2',
            'tax_percent' => 'decimal:2',
            'tax_inclusive' => 'boolean',
            'amount' => 'decimal:2',
        ];
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}