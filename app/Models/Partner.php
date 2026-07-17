<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class Partner extends Authenticatable
{
    use HasApiTokens;

    protected $fillable = ['firm_name', 'contact_name', 'mobile', 'gst_number', 'alt_mobile', 'address', 'rate_group_id', 'portal_access', 'show_prices', 'is_active'];

    protected function casts(): array
    {
        return [
            'portal_access' => 'boolean',
            'show_prices' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function initials(): string
    {
        $words = preg_split('/\s+/', trim($this->firm_name));
        $first = mb_substr($words[0] ?? '', 0, 1);
        $second = mb_substr($words[1] ?? '', 0, 1);
        return mb_strtoupper($first . $second);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function balance(): float
    {
        return (float) $this->invoices()->sum('total') - (float) $this->payments()->sum('amount');
    }

    public function rateGroup()
    {
        return $this->belongsTo(RateGroup::class);
    }
}