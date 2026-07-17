<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupplierBill extends Model
{
    protected $fillable = ['supplier_id', 'bill_no', 'bill_date', 'amount', 'note'];

    protected function casts(): array
    {
        return ['bill_date' => 'date', 'amount' => 'decimal:2'];
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
}