<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InwardEntry extends Model
{
    protected $fillable = ['inward_date', 'supplier_id', 'note'];

    protected function casts(): array
    {
        return ['inward_date' => 'date'];
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function lines()
    {
        return $this->hasMany(InwardLine::class);
    }
}