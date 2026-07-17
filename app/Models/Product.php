<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name', 'brand_id', 'category_id', 'barcode', 'mrp',
        'hsn_code', 'tax_percent', 'tax_inclusive', 'track_stock',
        'stock_qty', 'image_path', 'is_visible', 'rate_visible', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'mrp' => 'decimal:2',
            'tax_percent' => 'decimal:2',
            'tax_inclusive' => 'boolean',
            'track_stock' => 'boolean',
            'is_visible' => 'boolean',
            'rate_visible' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function rateSlabs()
    {
        return $this->hasMany(RateSlab::class);
    }

    public function initials(): string
    {
        $words = preg_split('/\s+/', trim($this->name));
        $first = mb_substr($words[0] ?? '', 0, 1);
        $second = mb_substr($words[1] ?? '', 0, 1);
        return mb_strtoupper($first . $second);
    }
}