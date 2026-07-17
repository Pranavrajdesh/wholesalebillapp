<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CreditNote extends Model
{
    protected $fillable = ['cn_no', 'partner_id', 'cn_date', 'kind', 'reason', 'total'];

    protected function casts(): array
    {
        return [
            'cn_date' => 'date',
            'total' => 'decimal:2',
        ];
    }

    public function partner()
    {
        return $this->belongsTo(Partner::class);
    }

    public function lines()
    {
        return $this->hasMany(CreditNoteLine::class);
    }
}