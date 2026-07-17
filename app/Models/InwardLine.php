<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InwardLine extends Model
{
    protected $fillable = ['inward_entry_id', 'product_id', 'name', 'brand', 'category', 'qty', 'purchase_rate'];

    protected function casts(): array
    {
        return ['purchase_rate' => 'decimal:2'];
    }

    public function entry()
    {
        return $this->belongsTo(InwardEntry::class, 'inward_entry_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}